<?php

namespace SimpleConfig;

use SimpleConfig\Node\AncestorNodeInterface;
use SimpleConfig\Node\AncestorNodeTrait;
use stdClass;

class Schema implements AncestorNodeInterface
{
    use AncestorNodeTrait;

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
            $itemValue = $child->validate($name, $data->$name);
            if (isset($itemValue)) {
                $itemValues->$name = $itemValue;
            }
        }

        return $itemValues;
    }
}
