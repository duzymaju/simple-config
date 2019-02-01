<?php

namespace SimpleConfig\Node;

use SimpleConfig\Exception\InvalidDataException;
use stdClass;

class ArrayNode extends Node implements AncestorNodeInterface, ValidatedNodeInterface
{
    use AncestorNodeTrait;

    /** @var bool */
    private $required = false;

    /** @var int|null */
    private $minLength;

    /** @var int|null */
    private $maxLength;

    /** @var array|null */
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
     * Default value
     *
     * @param array $defaultValue default value
     *
     * @return self
     */
    public function defaultValue(array $defaultValue)
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
     * @return array|null
     *
     * @throws InvalidDataException
     */
    public function validate($path, $data)
    {
        if (isset($data) && !is_array($data)) {
            throw new InvalidDataException(sprintf('%s: Value is not an array.', $path));
        }
        if ($this->required && !isset($data)) {
            throw new InvalidDataException(sprintf('%s: Value is required.', $path));
        }
        if (!isset($data)) {
            return $this->defaultValue;
        }
        if (isset($this->minLength) && count($data) < $this->minLength) {
            throw new InvalidDataException(sprintf('%s: Array length is shorter than %d.', $path, $this->minLength));
        }
        if (isset($this->maxLength) && count($data) > $this->maxLength) {
            throw new InvalidDataException(sprintf('%s: Array length is longer than %d.', $path, $this->maxLength));
        }

        return array_map(function ($item, $i) use ($path) {
            $itemValues = new stdClass();
            foreach ($this->children as $name => $child) {
                $itemValue = $child->validate(sprintf('%s[%s].%s', $path, $i, $name), $item->$name);
                if (isset($itemValue)) {
                    $itemValues->$name = $itemValue;
                }
            }
            return $itemValues;
        }, $data, array_keys($data));
    }
}
