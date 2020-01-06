<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Database;

use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JsonSerializable;
use Mathrix\Lumen\Zero\Models\BaseModel;
use RuntimeException;
use const FILE_IGNORE_NEW_LINES;
use const FILE_SKIP_EMPTY_LINES;
use const JSON_THROW_ON_ERROR;
use function array_combine;
use function array_fill_keys;
use function array_keys;
use function array_merge;
use function collect;
use function count;
use function database_path;
use function factory;
use function file;
use function file_get_contents;
use function is_int;
use function is_string;
use function json_decode;
use function json_encode;
use function str_getcsv;

class BaseTableSeeder extends Seeder
{
    public const DEFAULT_PROGRESS_BAR_FORMAT = '[%bar%] %percent:3s%% (%current%/%max%) ETA: %estimated:-6s%';

    /** @var OutputStyle */
    protected $output;

    /**
     * Set the console command instance.
     * Allow to get the Output object which is useful when creating progress bars
     *
     * @param Command $command
     *
     * @return Seeder
     */
    public function setCommand(Command $command)
    {
        $this->output = $command->getOutput();

        return parent::setCommand($command);
    }

    /**
     * Send data to database using a raw array.
     *
     * @param array  $rawData   The data as an array of models
     * @param string $table     The destination SQL table
     * @param int    $chunkSize The chunk size used for insertions
     */
    public function seedFromArray(array $rawData, string $table, int $chunkSize = 100): void
    {
        $progressBar = $this->output->createProgressBar(count($rawData));
        $progressBar->setFormat(self::DEFAULT_PROGRESS_BAR_FORMAT);

        $data = collect($rawData);
        $keys = $data->reduce(fn($carry, $item) => [...$carry, ...array_keys($item)], []);
        $data = $data
            // fill non-existing keys with null
            ->map(fn($model) => array_merge(array_fill_keys($keys, null), $model))
            // parse the array value as json
            ->map(static function ($model) {
                foreach ($model as $column => $val) {
                    if (!($model[$column] instanceof JsonSerializable)) {
                        continue;
                    }

                    $model[$column] = json_encode($model[$column], JSON_THROW_ON_ERROR, 512);
                }
            });

        $data->chunk($chunkSize)
            ->each(static function (Collection $chunkData) use ($table, $progressBar) {
                DB::table($table)->insert($chunkData->toArray());
                $progressBar->advance($chunkData->count());
            });

        // Finish the progress bar
        $progressBar->finish();
        $this->output->write("\n");
    }

    /**
     * Seed data using a json file.
     * By default, it will search in database path in the raw directory but you can also pass an absolute path.
     *
     * @param string      $filename The json file name, without the trailing .json
     * @param string|null $table    If null, same as filename
     */
    public function seedFromJson(string $filename, ?string $table = null): void
    {
        $table  ??= $filename;
        $path     = database_path("raws/$filename.json");
        $jsonData = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        $this->output->writeln("<comment>Seeding:</comment> $table from json");
        $this->seedFromArray($jsonData, $table);
    }

    /**
     * Seed data using a csv file.
     *
     * @param string      $filename The csv file name, without the trailing .csv
     * @param string|null $table    If null, same as filename
     */
    public function seedFromCsv(string $filename, ?string $table = null): void
    {
        $table ??= $filename;
        $path    = database_path("raws/$filename.csv");

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!$lines) {
            throw new RuntimeException("Error while reading CSV file at path: $path");
        }

        $data = collect($lines)->map(fn($line) => str_getcsv($line));

        // Since the csv data has no array keys, extract the first line assuming this is the heading and combine
        // them with the data values
        $headings = $data->shift();
        $data->map(fn($values) => array_combine($headings, $values));

        $this->output->writeln("<comment>Seeding:</comment> $table from csv");
        $this->seedFromArray($data->toArray(), $table);
    }

    /**
     * Seed from sub-factory.
     *
     * @param string $modelClass
     * @param string $subFactory
     * @param int    $count
     * @param array  $factoryOptions
     */
    public function seedFromSubFactory(
        string $modelClass,
        string $subFactory,
        int $count,
        array $factoryOptions = []
    ): void {
        $factoryOptions = array_merge(
            [
                'subFactory' => $subFactory,
                'count'      => $count,
            ],
            $factoryOptions
        );

        $this->seedFromFactory($modelClass, $factoryOptions);
    }

    /**
     * Seed from factory.
     *
     * @param string    $modelClass
     * @param array|int $factoryOptions
     */
    public function seedFromFactory(string $modelClass, $factoryOptions): void
    {
        $defaultFactoryOptions = [
            'subFactory' => null,
            'count'      => -1,
            'table'      => null,
            'override'   => [],
        ];

        if (is_int($factoryOptions)) {
            $factoryOptions = ['count' => $factoryOptions];
        }

        $factoryOptions = array_merge($defaultFactoryOptions, $factoryOptions);

        if ($factoryOptions['table'] === null) {
            /** @var Model $model */
            $model                   = new $modelClass();
            $factoryOptions['table'] = $model->getTable();
        }

        $factoryName = $factoryOptions['table'] .
            ($factoryOptions['subFactory'] !== null ? ":{$factoryOptions['subFactory']}" : '');

        $this->output->writeln("<comment>Seeding:</comment> $factoryName from factory");

        if (is_string($factoryOptions['subFactory'])) {
            /** @var Collection $factoryData */
            $factoryData = factory(
                $modelClass,
                $factoryOptions['subFactory'],
                $factoryOptions['count']
            )->make($factoryOptions['override']);
        } else {
            /** @var Collection $factoryData */
            $factoryData = factory(
                $modelClass,
                $factoryOptions['count']
            )->make($factoryOptions['override']);
        }

        /**
         * @var int       $k
         * @var BaseModel $model
         */
        foreach ($factoryData as $k => $model) {
            $factoryData[$k] = $model->getAttributes();
        }

        $this->seedFromArray($factoryData->toArray(), $factoryOptions['table']);
    }
}
