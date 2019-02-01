<?php

namespace SimpleConfig\Node;

use SimpleConfig\Exception\InvalidDataException;

class FloatNode extends Node implements ValidatedNodeInterface
{
    /** @var bool */
    private $required = false;

    /** @var float[]|null */
    private $allowedValues;

    /** @var float|null */
    private $minValue;

    /** @var float|null */
    private $maxValue;

    /** @var float|null */
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
     * @param int[]|float[] $allowedValues allowed values
     *
     * @return self
     */
    public function allowedValues(array $allowedValues)
    {
        $this->allowedValues = array_unique(array_map(function ($value) {
            return (float) $value;
        }, $allowedValues));

        return $this;
    }

    /**
     * Min value
     *
     * @param int|float $minValue min value
     *
     * @return self
     */
    public function minValue($minValue)
    {
        $this->minValue = (float) $minValue;

        return $this;
    }

    /**
     * Max value
     *
     * @param int|float $maxValue max value
     *
     * @return self
     */
    public function maxValue($maxValue)
    {
        $this->maxValue = (float) $maxValue;

        return $this;
    }

    /**
     * Default value
     *
     * @param int|float $defaultValue default value
     *
     * @return self
     */
    public function defaultValue($defaultValue)
    {
        $this->defaultValue = (float) $defaultValue;

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
        if (isset($data) && !is_float($data)) {
            throw new InvalidDataException(sprintf('%s: Value is not a float.', $path));
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
            throw new InvalidDataException(sprintf('%s: Value is lower than %f.', $path, $this->minValue));
        }
        if (isset($this->maxValue) && $data > $this->maxValue) {
            throw new InvalidDataException(sprintf('%s: Value is greater than %f.', $path, $this->maxValue));
        }

        return $data;
    }
}
