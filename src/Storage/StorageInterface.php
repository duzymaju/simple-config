<?php

namespace SimpleConfig\Storage;

use SimpleConfig\Config;
use SimpleConfig\Schema;

interface StorageInterface
{
    /**
     * Set schema
     *
     * @param Schema $schema schema
     *
     * @return self
     */
    public function setSchema(Schema $schema);

    /**
     * Load
     *
     * @param bool $validateAgainstSchema validate against schema
     *
     * @return Config
     */
    public function load($validateAgainstSchema = true);

    /**
     * Save
     *
     * @param Config $config config
     *
     * @return self
     */
    public function save(Config $config);
}
