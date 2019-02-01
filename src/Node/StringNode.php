<?php

namespace SimpleConfig\Node;

use SimpleConfig\Exception\InvalidDataException;

class StringNode extends Node implements ValidatedNodeInterface
{
    /** @var bool */
    private $required = false;

    /** @var string[]|null */
    private $allowedValues;

    /** @var int|null */
    private $minLength;

    /** @var int|null */
    private $maxLength;

    /** @var string|null */
    private $regExp;

    /** @var string|null */
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
     * @param string[] $allowedValues allowed values
     *
     * @return self
     */
    public function allowedValues(array $allowedValues)
    {
        $this->allowedValues = array_unique($allowedValues);

        return $this;
    }

    /**
     * Min length
     *
     * @param int $minLength min length
     *
     * @return self
     */
    public function minLength($minLength)
    {
        $this->minLength = $minLength;

        return $this;
    }

    /**
     * Max length
     *
     * @param int $maxLength max length
     *
     * @return self
     */
    public function maxLength($maxLength)
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    /**
     * Regular expression
     *
     * @param string $regExp regular expression
     *
     * @return self
     */
    public function regExp($regExp)
    {
        $this->regExp = $regExp;

        return $this;
    }

    /**
     * Default value
     *
     * @param string $defaultValue default value
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
        if (isset($data) && !is_string($data)) {
            throw new InvalidDataException(sprintf('%s: Value is not a string.', $path));
        }
        if ($this->required && empty($data)) {
            throw new InvalidDataException(sprintf('%s: Value is required.', $path));
        }
        if (!isset($data)) {
            return $this->defaultValue;
        }
        if (is_array($this->allowedValues) && !in_array($data, $this->allowedValues)) {
            throw new InvalidDataException(sprintf(
                '%s: Value should be one of "%s".', $path, implode('", "', $this->allowedValues)
            ));
        }
        if (isset($this->minLength) && mb_strlen($data) < $this->minLength) {
            throw new InvalidDataException(sprintf('%s: Value length is shorter than %d.', $path, $this->minLength));
        }
        if (isset($this->maxLength) && mb_strlen($data) > $this->maxLength) {
            throw new InvalidDataException(sprintf('%s: Value length is longer than %d.', $path, $this->maxLength));
        }
        if (isset($this->regExp) && !preg_match($this->regExp, $data)) {
            throw new InvalidDataException(sprintf('%s: Value doesn\'t match pattern %s.', $path, $this->regExp));
        }

        return $data;
    }
}
