<?php

namespace SimpleConfig\Node;

use SimpleConfig\Exception\InvalidDataException;
use stdClass;

class ObjectNode extends Node implements ParamAncestorNodeInterface, ValidatedNodeInterface
{
    use ParamAncestorNodeTrait;

    /** @var bool */
    private $required = false;

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
        if (isset($data) && !is_object($data)) {
            throw new InvalidDataException(sprintf('%s: Value is not an object.', $path));
        }
        if ($this->required && !isset($data)) {
            throw new InvalidDataException(sprintf('%s: Value is required.', $path));
        }
        $itemValues = new stdClass();
        foreach ($this->children as $name => $child) {
            $value = isset($data) && isset($data->$name) ? $data->$name : null;
            $validValue = $child->validate(sprintf('%s.%s', $path, $name), $value);
            if (isset($validValue)) {
                $itemValues->$name = $validValue;
            }
        }

        return $itemValues;
    }
}
