<?php

namespace SimpleConfig;

use JsonSerializable;
use SimpleConfig\Exception\InvalidJsonException;
use SimpleConfig\Exception\NoConfigException;
use SimpleConfig\Exception\NoSchemaException;
use SimpleConfig\Tools\CollectionTools;
use stdClass;

class Config implements JsonSerializable
{
    /** @var stdClass|null */
    private $data;

    /** @var Schema|null */
    private $schema;

    /** @var bool */
    private $compression = true;

    /**
     * Set data
     *
     * @param stdClass|string $data     data
     * @param bool            $validate validate
     *
     * @return self
     */
    public function setData($data, $validate = true)
    {
        $preparedData = $this->prepareData($data);
        $this->data = $this->validate($preparedData, $validate);

        return $this;
    }

    /**
     * Change data
     *
     * @param stdClass|string $patch    patch
     * @param bool            $validate validate
     *
     * @return self
     */
    public function changeData($patch, $validate = true)
    {
        if (!$this->hasData()) {
            throw new NoConfigException('Configuration data has to be defined before change.');
        }
        $preparedPatch = $this->prepareData($patch);
        $preparedData = $this->deepMerge($this->data, $preparedPatch);
        $this->data = $this->validate($preparedData, $validate);

        return $this;
    }

    /**
     * Has data
     *
     * @return bool
     */
    public function hasData()
    {
        return isset($this->data);
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
     * Get param
     *
     * @param string $path         path
     * @param mixed  $defaultValue default value
     *
     * @return mixed
     */
    public function getParam($path, $defaultValue = null)
    {
        if (!$this->hasData()) {
            return $defaultValue;
        }

        $target = $this->data;
        foreach (explode('.', $path) as $key) {
            if (is_object($target) && isset($target->$key)) {
                $target = $target->$key;
                continue;
            } elseif (is_array($target) && array_key_exists($key, $target)) {
                $target = $target[$key];
                continue;
            }
            return $defaultValue;
        }

        return $target;
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
     * Set compression
     *
     * @param bool $compression compression
     *
     * @return self
     */
    public function setCompression($compression)
    {
        $this->compression = $compression;

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
        $data = $this->hasData() ? $this->data : $this->getDefaults();

        $options = 0;
        if (!$this->compression) {
            $options |= JSON_PRETTY_PRINT;
        }
        $json = json_encode($data, $options);
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

    /**
     * Prepare data
     *
     * @param stdClass|string $data data
     *
     * @return stdClass
     *
     * @throws InvalidJsonException
     */
    private function prepareData($data)
    {
        if ($data instanceof stdClass) {
            return $data;
        }

        if (!is_string($data)) {
            throw new InvalidJsonException('Invalid JSON format.');
        }
        $decodedData = json_decode($data);
        if (!($decodedData instanceof stdClass)) {
            throw new InvalidJsonException('An error occurred during configuration JSON parsing.');
        }

        return $decodedData;
    }

    /**
     * Deep merge
     *
     * @param stdClass $data  data
     * @param stdClass $patch patch
     *
     * @return stdClass
     */
    private function deepMerge($data, $patch)
    {
        return CollectionTools::deepMerge($data, $patch);
    }

    /**
     * Validate
     *
     * @param stdClass $data     data
     * @param bool     $validate validate
     *
     * @return stdClass
     *
     * @throws NoSchemaException
     */
    private function validate($data, $validate = true)
    {
        if (!$validate) {
            return $data;
        }
        if (!isset($this->schema)) {
            throw new NoSchemaException('Schema has to be defined to validate configuration.');
        }

        return $this->schema->validate($data);
    }
}
