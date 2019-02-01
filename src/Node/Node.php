<?php

namespace SimpleConfig\Node;

abstract class Node
{
    /** @var string */
    protected $name;

    /** @var AncestorNodeInterface */
    private $parent;

    /**
     * Construct
     *
     * @param string                $name   name
     * @param AncestorNodeInterface $parent parent
     */
    public function __construct($name, AncestorNodeInterface $parent)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    /**
     * End
     *
     * @return AncestorNodeInterface
     */
    public function end()
    {
        return $this->parent;
    }
}
