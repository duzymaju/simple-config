<?php

namespace SimpleConfig;

use JsonSerializable;
use SimpleConfig\Exception\InvalidJsonException;
use stdClass;

class Config implements JsonSerializable
{
    /** @var stdClass|null */
    private $data;

    /** @var Schema|null */
    private $schema;

    /**
     * Set data
     *
     * @param stdClass|string $data                  data
     * @param bool            $validateAgainstSchema validate against schema
     *
     * @return self
     */
    public function setData($data, $validateAgainstSchema = true)
    {
        if (!($data instanceof stdClass)) {
            if (!is_string($data)) {
                throw new InvalidJsonException('Invalid JSON format.');
            }
            $data = json_decode($data);
            if (!isset($data)) {
                throw new InvalidJsonException('An error occurred during configuration JSON parsing.');
            }
        }
        if ($validateAgainstSchema && isset($this->schema)) {
            $data = $this->schema->validate($data);
        }
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return stdClass|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get defaults
     *
     * @return stdClass
     */
    public function getDefaults()
    {
        return $this->schema->validate(new stdClass());
    }

    /**
     * Set schema
     *
     * @param Schema $schema schema
     *
     * @return self
     */
    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * JSON serialize
     *
     * @return string
     *
     * @throws InvalidJsonException
     */
    public function jsonSerialize()
    {
        if (!isset($this->data)) {
            throw new InvalidJsonException('Configuration data has to be defined.');
        }

        $json = json_encode($this->data);
        if ($json === false) {
            throw new InvalidJsonException('Configuration data cannot be serialized.');
        }

        return $json;
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->jsonSerialize();
    }
}
