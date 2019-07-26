<?php

namespace Mathrix\Lumen\Zero\Testing;

use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Testing\Dictionaries\Dictionary;

/**
 * Class ModelMockFactory.
 * Custom factory to make model mocks, since we use it in a non-standard way.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
class ModelMockFactory
{
    /** @var string $namespace The model namespace. */
    private $namespace = "App\\Models";
    /** @var string $name The model name. */
    private $name;
    /** @var array $properties The properties of the mock. */
    private $properties = [];
    /** @var array $methods The methods of the mock. */
    private $methods = [];
    /** @var string $code The actual code of the mock. */
    private $code;


    /**
     * Make a new ModelMockFactory
     *
     * @return ModelMockFactory
     */
    public static function make(): self
    {
        return new self();
    }


    /**
     * Set the mock namespace and class name
     *
     * @param string $fullyQualifiedName
     *
     * @return ModelMockFactory
     */
    public function setFullyQualifiedName(string $fullyQualifiedName): self
    {
        $class = class_basename($fullyQualifiedName);
        $namespace = str_replace("\\$class", "", $fullyQualifiedName);

        $this->setNamespace($namespace);
        $this->setName($class);

        return $this;
    }


    /**
     * Set the mock namespace.
     *
     * @param string $namespace
     *
     * @return $this
     */
    public function setNamespace(string $namespace): self
    {
        $this->namespace = trim($namespace, "\\");

        return $this;
    }


    /**
     * Set the mock name. If set to null, we will use the Dictionary to generate a random model name.
     *
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name = null): self
    {
        $this->name = $name ?? (new Dictionary())->random();

        return $this;
    }


    /**
     * Set the mock property.
     *
     * @param string $visibility
     * @param string $name
     * @param $value
     *
     * @return $this
     */
    public function setProperty(string $visibility, string $name, $value): self
    {
        $this->properties[] = [$visibility, $name, $value];

        return $this;
    }


    /**
     * Set the mock method.
     *
     * @param string $visibility
     * @param string $name
     * @param $value
     *
     * @return $this
     */
    public function setMethod(string $visibility, string $name, $value): self
    {
        $this->methods[] = [$visibility, $name, $value];

        return $this;
    }


    /**
     * Compile the code.
     *
     * @return $this
     */
    public function compile(): self
    {
        $baseModel = BaseModel::class;

        $code = "namespace $this->namespace;\n\n";
        $code .= "class $this->name extends \\$baseModel {\n";

        // Implements properties
        foreach ($this->properties as $property) {
            [$visibility, $name, $value] = $property;
            $value = var_export($value, true);
            $code .= "    $visibility \$$name = $value;\n";
        }

        // Implements methods
        foreach ($this->methods as $method) {
            [$visibility, $name, $value] = $method;
            $value = var_export($value, true);
            $code .= "\n    $visibility function $name()\n";
            $code .= "    {\n";
            $code .= "        return $value;\n";
            $code .= "    }\n";
        }
        $code .= "}\n";

        $this->code = $code;

        return $this;
    }


    /**
     * Eval the generated code.
     *
     * @return $this
     */
    public function exec(): self
    {
        eval($this->code);

        return $this;
    }


    /**
     * Get the mock actual code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }


    /**
     * Get the mock actual full class name.
     *
     * @return string
     */
    public function getClass(): string
    {
        return "$this->namespace\\$this->name";
    }
}
