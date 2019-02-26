<?php

namespace SimpleConfig\Node;

interface ArrayAncestorNodeInterface
{
    /**
     * Array items
     *
     * @return ArrayNode
     */
    public function arrayItems();

    /**
     * Boolean node
     *
     * @return BooleanNode
     */
    public function booleanItems();

    /**
     * Float node
     *
     * @return FloatNode
     */
    public function floatItems();

    /**
     * Integer node
     *
     * @return IntegerNode
     */
    public function integerItems();

    /**
     * Object node
     *
     * @return ObjectNode
     */
    public function objectItems();

    /**
     * String node
     *
     * @return StringNode
     */
    public function stringItems();

    /**
     * End
     *
     * @return self
     */
    public function end();
}
