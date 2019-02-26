<?php

use PHPUnit\Framework\TestCase;
use SimpleConfig\Config;
use SimpleConfig\Schema;

final class StructureTest extends TestCase
{
    /** @var Schema */
    private $schema;

    /** @before */
    public function setupSchema()
    {
        $this->schema = new Schema();
        $this->schema
            ->stringNode('stringNode')->end()
            ->objectNode('objectNode')
                ->stringNode('stringInsideObject')
                    ->defaultValue('stringValue')
                    ->regExp('#[a-z](\.[0-9]+)?#i')
                    ->minLength(3)
                    ->maxLength(10)
                ->end()
                ->arrayNode('arrayInsideObject')
                    ->objectItems()
                        ->stringNode('stringInsideObjectInsideArray')->end()
                        ->floatNode('floatInsideObjectInsideArray')
                            ->allowedValues([ 0.3, 4, 5.17 ])
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('arrayNode')
                ->defaultValue([ 11 ])
                ->minLength(1)
                ->maxLength(3)
                ->integerItems()
                    ->minValue(10)
                    ->maxValue(20)
                    ->required()
                ->end()
            ->end()
        ;
    }

    /**
     * Test default values
     */
    public function testDefaultValues()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $this->assertEquals((object) [
            'objectNode' => (object) [
                'stringInsideObject' => 'stringValue',
            ],
            'arrayNode' => [ 11 ],
        ], $config->getDefaults());
    }

    /**
     * Test valid input object
     */
    public function testValidInputObject()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = (object) [
            'objectNode' => (object) [
                'stringInsideObject' => 'abc.123',
                'arrayInsideObject' => [
                    (object) [
                        'stringInsideObjectInsideArray' => 'def',
                        'floatInsideObjectInsideArray' => 5.17,
                    ],
                    (object) [
                        'stringInsideObjectInsideArray' => 'ghi',
                        'floatInsideObjectInsideArray' => 0.3,
                    ],
                ]
            ],
            'arrayNode' => [
                12,
                14,
            ],
        ];
        $config->setData($data);
        $this->assertEquals($data, $config->getData());
    }

    /**
     * Test valid input JSON
     */
    public function testValidInputJson()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = '{
            "objectNode": {
                "stringInsideObject": "abc.123",
                "arrayInsideObject": [
                    {
                        "stringInsideObjectInsideArray": "def",
                        "floatInsideObjectInsideArray": 5.17
                    },
                    {
                        "stringInsideObjectInsideArray": "ghi",
                        "floatInsideObjectInsideArray": 0.3
                    }
                ]
            },
            "arrayNode": [
                12,
                14
            ]
        }';
        $config->setData($data);
        $this->assertEquals(json_decode($data), $config->getData());
    }
}
