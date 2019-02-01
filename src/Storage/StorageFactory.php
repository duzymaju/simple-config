<?php

namespace SimpleConfig\Storage;

use SimpleConfig\Exception\InvalidStorageException;

class StorageFactory
{
    /**
     * Create
     *
     * @param string $type   type
     * @param array  $config config
     *
     * @return StorageInterface
     */
    public function create($type, array $config)
    {
        switch ($type) {
            case 'file':
                $path = array_key_exists('path', $config) && is_string($config['path']) ? $config['path'] : '';
                $createIfNotExists = array_key_exists('createIfNotExists', $config) &&
                    is_bool($config['createIfNotExists']) ? $config['createIfNotExists'] : true;
                return new FileStorage($path, $createIfNotExists);

            default:
                throw new InvalidStorageException(sprintf('Storage type %s doesn\'t exist.', $type));
        }
    }
}
