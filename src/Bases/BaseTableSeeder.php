<?php

namespace Mathrix\Lumen\Bases;

use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class BaseTableSeeder.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @codeCoverageIgnore
 */
class BaseTableSeeder extends Seeder
{
    public const DEFAULT_PROGRESS_BAR_FORMAT = "[%bar%] %percent:3s%% (%current%/%max%) ETA: %estimated:-6s%";

    /** @var OutputStyle */
    protected $output;


    /**
     * Override default call.
     *
     * @param string $class
     * @param bool $silent
     */
    public function call($class, $silent = false)
    {
        $this->resolve($class)
            ->__invoke();
    }


    /**
     * Set the console command instance.
     *
     * @param Command $command
     *
     * @return $this
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;
        $this->output = $command->getOutput();

        return $this;
    }


    /**
     * Seed data using a json file.
     *
     * @param string $filename The json file name, without the trailing .json
     * @param string|null $table If null, same as filename
     */
    public function seedFromJson(string $filename, string $table = null)
    {
        if ($table === null) {
            $table = $filename;
        }

        $path = app()->databasePath("raws") . DIRECTORY_SEPARATOR . "$filename.json";
        $jsonData = json_decode(file_get_contents($path), true);

        $this->output->writeln("<comment>Seeding:</comment> $table from json");
        $this->seedFromArray($jsonData, $table);
    }


    /**
     * Send data to database using a raw array.
     *
     * @param array $rawData
     * @param string $table
     * @param int $chunkSize
     */
    public function seedFromArray(array $rawData, string $table, int $chunkSize = 100)
    {
        $progressBar = $this->output->createProgressBar(count($rawData));
        $progressBar->setFormat(self::DEFAULT_PROGRESS_BAR_FORMAT);

        // Let's find all row keys
        $keys = [];
        foreach ($rawData as $row) {
            $keys = array_merge($keys, array_keys($row));
        }

        // Fill missing row with all keys
        $emptyRow = array_fill_keys($keys, null);
        foreach ($rawData as $k => $row) {
            $rawData[$k] = array_merge($emptyRow, $row);
        }

        // Handle json encode of array, assuming they should be injected a strings
        foreach ($rawData as $key => $row) {
            foreach ($row as $column => $val) {
                if (is_array($val)) {
                    $rawData[$key][$column] = json_encode($val);
                }
            }
        }

        // Insert data in database by chunk
        for ($i = 0; $i < count($rawData); $i += $chunkSize) {
            $insertData = array_slice($rawData, $i, $chunkSize);
            DB::table($table)->insert($insertData);
            $progressBar->advance(count($insertData));
        }

        $progressBar->finish();
        $this->output->write("\n");
    }


    /**
     * Seed data using a csv file.
     *
     * @param string $filename The csv file name, without the trailing .csv
     * @param string|null $table If null, same as filename
     */
    public function seedFromCsv(string $filename, string $table = null)
    {
        if ($table === null) {
            $table = $filename;
        }

        $path = app()->databasePath("raws") . DIRECTORY_SEPARATOR . "$filename.csv";

        $csvFile = file($path);
        $csvData = [];
        foreach ($csvFile as $line) {
            $csvData[] = str_getcsv($line);
        }
        $headings = array_shift($csvData);

        $csvData = array_map(function ($line) use ($headings) {
            return array_combine($headings, $line);
        }, $csvData);

        $this->output->writeln("<comment>Seeding:</comment> $table from csv");
        $this->seedFromArray($csvData, $table);
    }


    /**
     * Seed from sub-factory.
     *
     * @param string $modelClass
     * @param string $subFactory
     * @param int $count
     * @param array $factoryOptions
     */
    public function seedFromSubFactory(string $modelClass, string $subFactory, int $count, array $factoryOptions = [])
    {
        $factoryOptions = array_merge([
            "subFactory" => $subFactory,
            "count" => $count
        ], $factoryOptions);

        $this->seedFromFactory($modelClass, $factoryOptions);
    }


    /**
     * Seed from factory.
     *
     * @param string $modelClass
     * @param array|int $factoryOptions
     */
    public function seedFromFactory(string $modelClass, $factoryOptions)
    {
        $defaultFactoryOptions = [
            "subFactory" => null,
            "count" => -1,
            "table" => null,
            "override" => []
        ];

        if (is_integer($factoryOptions)) {
            $factoryOptions = [
                "count" => $factoryOptions
            ];
        }

        $factoryOptions = array_merge($defaultFactoryOptions, $factoryOptions);

        if ($factoryOptions["table"] === null) {
            /** @var Model $model */
            $model = new $modelClass();
            $factoryOptions["table"] = $model->getTable();
        }

        $factoryName = $factoryOptions["table"] .
            ($factoryOptions["subFactory"] !== null ? ":{$factoryOptions["subFactory"]}" : "");

        $this->output->writeln("<comment>Seeding:</comment> $factoryName from factory");

        if (is_string($factoryOptions["subFactory"])) {
            /** @var Collection $factoryData */
            $factoryData = factory(
                $modelClass,
                $factoryOptions["subFactory"],
                $factoryOptions["count"]
            )->make($factoryOptions["override"]);
        } else {
            /** @var Collection $factoryData */
            $factoryData = factory(
                $modelClass,
                $factoryOptions["count"]
            )->make($factoryOptions["override"]);
        }

        /**
         * @var int $k
         * @var BaseModel $model
         */
        foreach ($factoryData as $k => $model) {
            $factoryData[$k] = $model->getAttributes();
        }

        $this->seedFromArray($factoryData->toArray(), $factoryOptions["table"]);
    }


    /**
     * Link two tables in both directions.
     *
     * @param BaseModel|string $modelClass1 the model class to use and link
     * @param BaseModel|string $modelClass2 the model class to use and link
     * @param int $min the minimum relations
     * @param int $max the maximum relations
     */
    public function linkAll($modelClass1, $modelClass2, $min = 3, $max = 5)
    {
        $this->link($modelClass1, $modelClass2, $min, $max);
        $this->link($modelClass2, $modelClass1, $min, $max);
    }


    /**
     * Link two tables.
     *
     * @param BaseModel|string $modelClass1 the model class to use
     * @param BaseModel|string $modelClass2 the model class to link
     * @param int $min the minimum relations
     * @param int $max the maximum relations
     * @param callable $callback
     */
    public function link($modelClass1, $modelClass2, $min = 3, $max = 5, callable $callback = null)
    {
        $faker = Factory::create();

        // Build all necessary variables (tables, ids...)
        $models1 = $modelClass1::all();
        $model1Table = $modelClass1::getTableName();
        $model1Key = Str::singular($model1Table) . "_id";
        $model2Ids = $modelClass2::query()->get(["id"])
            ->pluck("id")
            ->toArray();
        $model2Table = $modelClass2::getTableName();
        $model2Key = Str::singular($model2Table) . "_id";

        if (strcmp($model1Table, $model2Table) < 0) {
            $linkTable = Str::singular($model1Table) . "_" . Str::singular($model2Table);
        } else {
            $linkTable = Str::singular($model2Table) . "_" . Str::singular($model1Table);
        }

        $this->output->writeln("<comment>Linking:</comment> $model1Table <=> $model2Table");

        // Build link data
        $linkData = [];
        foreach ($models1 as $model1) {
            $model1Id = $model1->id;

            $model2IdsToLinkWithModel1 = $faker->randomElements($model2Ids, $faker->numberBetween($min, $max));

            foreach ($model2IdsToLinkWithModel1 as $order => $model2Id) {
                if (is_callable($callback)) {
                    $linkData[] = $callback($order, [
                        $model1Key => $model1Id,
                        $model2Key => $model2Id
                    ]);
                } else {
                    $linkData[] = [
                        $model1Key => $model1Id,
                        $model2Key => $model2Id
                    ];
                }
            }
        }

        $this->seedFromArray($linkData, $linkTable);
    }
}
