<?php

namespace SimpleConfig\Node;

use SimpleConfig\Exception\InvalidDataException;

class BooleanNode extends Node implements ValidatedNodeInterface
{
    /** @var bool */
    private $required = false;

    /** @var bool[]|null */
    private $allowedValues;

    /** @var bool|null */
    private $defaultValue;

    /**
     * Required
     *
     * @return self
     */
    public function required()
    {
        $this->required = true;

        return $this;
    }

    /**
     * Allowed values
     *
     * @param bool[] $allowedValues allowed values
     *
     * @return self
     */
    public function allowedValues(array $allowedValues)
    {
        $this->allowedValues = array_unique($allowedValues);

        return $this;
    }

    /**
     * Default value
     *
     * @param bool $defaultValue default value
     *
     * @return self
     */
    public function defaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Validate
     *
     * @param string $path path
     * @param mixed  $data data
     *
     * @return string|null
     *
     * @throws InvalidDataException
     */
    public function validate($path, $data)
    {
        if (isset($data) && !is_bool($data)) {
            throw new InvalidDataException(sprintf('%s: Value is not a boolean.', $path));
        }
        if ($this->required && !isset($data)) {
            throw new InvalidDataException(sprintf('%s: Value is required.', $path));
        }
        if (!isset($data)) {
            return $this->defaultValue;
        }
        if (is_array($this->allowedValues) && !in_array($data, $this->allowedValues)) {
            throw new InvalidDataException(sprintf(
                '%s: Value should be one of "%s".', $path, implode('", "', array_map(function ($value) {
                    return $value === true ? 'true' : 'false';
                }, $this->allowedValues))
            ));
        }

        return $data;
    }
}
