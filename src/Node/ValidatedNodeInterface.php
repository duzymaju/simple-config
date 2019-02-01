<?php

namespace SimpleConfig\Node;

use SimpleConfig\Exception\InvalidDataException;

interface ValidatedNodeInterface
{
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
    public function validate($path, $data);
}
