<?php

namespace SimpleConfig\Node;

interface ParamAncestorNodeInterface
{
    /**
     * Array node
     *
     * @param string $name name
     *
     * @return ArrayNode
     */
    public function arrayNode($name);

    /**
     * Boolean node
     *
     * @param string $name name
     *
     * @return BooleanNode
     */
    public function booleanNode($name);

    /**
     * Float node
     *
     * @param string $name name
     *
     * @return FloatNode
     */
    public function floatNode($name);

    /**
     * Integer node
     *
     * @param string $name name
     *
     * @return IntegerNode
     */
    public function integerNode($name);

    /**
     * Object node
     *
     * @param string $name name
     *
     * @return ObjectNode
     */
    public function objectNode($name);

    /**
     * String node
     *
     * @param string $name name
     *
     * @return StringNode
     */
    public function stringNode($name);

    /**
     * End
     *
     * @return self
     */
    public function end();
}
