<?php

namespace SimpleConfig\Node;

use SimpleConfig\Exception\NodeDuplicationException;

trait AncestorNodeTrait
{
    /** @var ValidatedNodeInterface[] */
    protected $children = [];

    /**
     * Array node
     *
     * @param string $name name
     *
     * @return ArrayNode
     */
    public function arrayNode($name)
    {
        $node = new ArrayNode($name, $this);
        $this->addNodeIfUnique($name, $node);

        return $node;
    }

    /**
     * Boolean node
     *
     * @param string $name name
     *
     * @return BooleanNode
     */
    public function booleanNode($name)
    {
        $node = new BooleanNode($name, $this);
        $this->addNodeIfUnique($name, $node);

        return $node;
    }

    /**
     * Float node
     *
     * @param string $name name
     *
     * @return FloatNode
     */
    public function floatNode($name)
    {
        $node = new FloatNode($name, $this);
        $this->addNodeIfUnique($name, $node);

        return $node;
    }

    /**
     * Integer node
     *
     * @param string $name name
     *
     * @return IntegerNode
     */
    public function integerNode($name)
    {
        $node = new IntegerNode($name, $this);
        $this->addNodeIfUnique($name, $node);

        return $node;
    }

    /**
     * Object node
     *
     * @param string $name name
     *
     * @return ObjectNode
     */
    public function objectNode($name)
    {
        $node = new ObjectNode($name, $this);
        $this->addNodeIfUnique($name, $node);

        return $node;
    }

    /**
     * String node
     *
     * @param string $name name
     *
     * @return StringNode
     */
    public function stringNode($name)
    {
        $node = new StringNode($name, $this);
        $this->addNodeIfUnique($name, $node);

        return $node;
    }

    /**
     * Add node if unique
     *
     * @param string                 $name name
     * @param ValidatedNodeInterface $node node
     *
     * @throws NodeDuplicationException
     */
    public function addNodeIfUnique($name, ValidatedNodeInterface $node)
    {
        if (array_key_exists($name, $this->children)) {
            throw new NodeDuplicationException(sprintf('Node "%s" already exists.', $name));
        }
        $this->children[$name] = $node;
    }
}
