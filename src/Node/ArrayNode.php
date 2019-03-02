<?php

namespace SimpleConfig\Node;

use SimpleConfig\Exception\InvalidDataException;
use SimpleConfig\Exception\InvalidSchemaException;
use stdClass;

class ArrayNode extends Node implements ArrayAncestorNodeInterface, ValidatedNodeInterface
{
    /** @var ValidatedNodeInterface */
    protected $items;

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
     * Array items
     *
     * @return ArrayNode
     */
    public function arrayItems()
    {
        $this->items = new ArrayNode('', $this);

        return $this->items;
    }

    /**
     * Boolean node
     *
     * @return BooleanNode
     */
    public function booleanItems()
    {
        $this->items = new BooleanNode('', $this);

        return $this->items;
    }

    /**
     * Float node
     *
     * @return FloatNode
     */
    public function floatItems()
    {
        $this->items = new FloatNode('', $this);

        return $this->items;
    }

    /**
     * Integer node
     *
     * @return IntegerNode
     */
    public function integerItems()
    {
        $this->items = new IntegerNode('', $this);

        return $this->items;
    }

    /**
     * Object node
     *
     * @return ObjectNode
     */
    public function objectItems()
    {
        $this->items = new ObjectNode('', $this);

        return $this->items;
    }

    /**
     * String node
     *
     * @return StringNode
     */
    public function stringItems()
    {
        $this->items = new StringNode('', $this);

        return $this->items;
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
        if ($data instanceof stdClass) {
            $arrayData = (array) $data;
            $keys = array_keys($arrayData);
            $notNumericKeys = array_filter($keys, function ($key) {
                return !is_numeric($key);
            });
            if (count($notNumericKeys) === 0) {
                $data = $arrayData;
            }
        }
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
            return $this->items->validate(sprintf('%s[%s]', $path, $i), $item);
        }, $data, array_keys($data));
    }

    /**
     * End
     *
     * @return ArrayAncestorNodeInterface|ParamAncestorNodeInterface
     *
     * @throws InvalidSchemaException
     */
    public function end()
    {
        if (!isset($this->items)) {
            throw new InvalidSchemaException('Array items have to be defined.');
        }

        return parent::end();
    }
}
