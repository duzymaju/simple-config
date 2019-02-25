<?php

namespace SimpleConfig\Node;

use SimpleConfig\Exception\InvalidDataException;

class IntegerNode extends Node implements ValidatedNodeInterface
{
    /** @var bool */
    private $required = false;

    /** @var int[]|null */
    private $allowedValues;

    /** @var int|null */
    private $minValue;

    /** @var int|null */
    private $maxValue;

    /** @var int|null */
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
     * @param int[] $allowedValues allowed values
     *
     * @return self
     */
    public function allowedValues(array $allowedValues)
    {
        $this->allowedValues = array_unique($allowedValues);

        return $this;
    }

    /**
     * Min value
     *
     * @param int $minValue min value
     *
     * @return self
     */
    public function minValue($minValue)
    {
        $this->minValue = $minValue;

        return $this;
    }

    /**
     * Max value
     *
     * @param int $maxValue max value
     *
     * @return self
     */
    public function maxValue($maxValue)
    {
        $this->maxValue = $maxValue;

        return $this;
    }

    /**
     * Default value
     *
     * @param int $defaultValue default value
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
        if (isset($data) && !is_int($data)) {
            throw new InvalidDataException(sprintf('%s: Value is not an integer.', $path));
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
                    return $value == 0 ? '0' : (string) $value;
                }, $this->allowedValues))
            ));
        }
        if (isset($this->minValue) && $data < $this->minValue) {
            throw new InvalidDataException(sprintf('%s: Value is lower than %d.', $path, $this->minValue));
        }
        if (isset($this->maxValue) && $data > $this->maxValue) {
            throw new InvalidDataException(sprintf('%s: Value is greater than %d.', $path, $this->maxValue));
        }

        return $data;
    }
}
