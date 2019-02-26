<?php

namespace SimpleConfig\Node;

abstract class Node
{
    /** @var string */
    protected $name;

    /** @var ArrayAncestorNodeInterface|ParamAncestorNodeInterface */
    private $parent;

    /**
     * Construct
     *
     * @param string                                                $name   name
     * @param ArrayAncestorNodeInterface|ParamAncestorNodeInterface $parent parent
     */
    public function __construct($name, $parent)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    /**
     * End
     *
     * @return ArrayAncestorNodeInterface|ParamAncestorNodeInterface
     */
    public function end()
    {
        return $this->parent;
    }
}
