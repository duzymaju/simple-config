<?php

namespace SimpleConfig;

use SimpleConfig\Node\ParamAncestorNodeInterface;
use SimpleConfig\Node\ParamAncestorNodeTrait;
use stdClass;

class Schema implements ParamAncestorNodeInterface
{
    use ParamAncestorNodeTrait;

    /**
     * End
     *
     * @return self
     */
    public function end()
    {
        return $this;
    }

    /**
     * Validate
     *
     * @param stdClass $data data
     *
     * @return stdClass
     */
    public function validate($data)
    {
        $itemValues = new stdClass();
        foreach ($this->children as $name => $child) {
            $value = isset($data) && isset($data->$name) ? $data->$name : null;
            $validValue = $child->validate($name, $value);
            if (isset($validValue)) {
                $itemValues->$name = $validValue;
            }
        }

        return $itemValues;
    }
}
