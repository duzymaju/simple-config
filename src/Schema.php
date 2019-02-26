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
     * @param mixed $data data
     *
     * @return mixed
     */
    public function validate($data)
    {
        $itemValues = new stdClass();
        foreach ($this->children as $name => $child) {
            $itemValue = $child->validate($name, isset($data->$name) ? $data->$name : null);
            if (isset($itemValue)) {
                $itemValues->$name = $itemValue;
            }
        }

        return $itemValues;
    }
}
