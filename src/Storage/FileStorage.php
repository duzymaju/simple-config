<?php

namespace SimpleConfig\Storage;

use SimpleConfig\Config;
use SimpleConfig\Exception\InvalidResourceException;
use SimpleConfig\Schema;
use stdClass;

class FileStorage implements StorageInterface
{
    /** @var string */
    private $filePath;

    /** @var bool */
    private $createIfNotExists;

    /** @var Schema|null */
    private $schema;

    /**
     * Construct
     *
     * @param string $filePath          file path
     * @param bool   $createIfNotExists create if not exists
     *
     * @throws InvalidResourceException
     */
    public function __construct($filePath, $createIfNotExists = true)
    {
        if (empty($filePath)) {
            throw new InvalidResourceException('File path is not defined.');
        }

        $this->filePath = $filePath;
        $this->createIfNotExists = $createIfNotExists;
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
     * Load
     *
     * @param bool $validateAgainstSchema validate against schema
     *
     * @return Config
     *
     * @throws InvalidResourceException
     */
    public function load($validateAgainstSchema = true)
    {
        $config = new Config();
        if (isset($this->schema)) {
            $config->setSchema($this->schema);
        }

        if ($this->createIfNotExists && !file_exists($this->filePath)) {
            $config->setData(new stdClass(), $validateAgainstSchema);
            return $config;
        }

        $data = file_get_contents($this->filePath);
        if ($data === false) {
            throw new InvalidResourceException(sprintf('Content from file %s isn\'t readable.', $this->filePath));
        }
        $config->setData($data, $validateAgainstSchema);

        return $config;
    }

    /**
     * Save
     *
     * @param Config $config config
     *
     * @return self
     *
     * @throws InvalidResourceException
     */
    public function save(Config $config)
    {
        if (!file_put_contents($this->filePath, (string) $config)) {
            throw new InvalidResourceException(
                sprintf('An error occurred during writing to file %s.', $this->filePath)
            );
        }

        return $this;
    }
}
