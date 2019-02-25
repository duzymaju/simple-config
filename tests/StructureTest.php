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
                ->stringNode('stringIntoObject')
                    ->defaultValue('stringValue')
                    ->regExp('#[a-z](\.[0-9]+)?#i')
                    ->minLength(3)
                    ->maxLength(10)
                ->end()
                ->arrayNode('arrayIntoObject')
                    ->stringNode('stringIntoObjectIntoArray')->end()
                    ->floatNode('floatIntoObjectIntoArray')
                        ->allowedValues([ 0.3, 4, 5.17 ])
                    ->end()
                ->end()
            ->end()
            ->arrayNode('arrayNode')
                ->defaultValue([])
                ->integerNode('integerIntoArray')
                    ->minValue(10)
                    ->maxValue(20)
                    ->required()
                ->end()
                ->booleanNode('booleanIntoArray')
                    ->defaultValue(true)
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
                'stringIntoObject' => 'stringValue',
            ],
            'arrayNode' => [],
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
                'stringIntoObject' => 'abc.123',
                'arrayIntoObject' => [
                    (object) [
                        'stringIntoObjectIntoArray' => 'def',
                        'floatIntoObjectIntoArray' => 5.17,
                    ],
                    (object) [
                        'stringIntoObjectIntoArray' => 'ghi',
                        'floatIntoObjectIntoArray' => 0.3,
                    ],
                ]
            ],
            'arrayNode' => [
                (object) [
                    'integerIntoArray' => 12,
                    'booleanIntoArray' => false,
                ],
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
                "stringIntoObject": "abc.123",
                "arrayIntoObject": [
                    {
                        "stringIntoObjectIntoArray": "def",
                        "floatIntoObjectIntoArray": 5.17
                    },
                    {
                        "stringIntoObjectIntoArray": "ghi",
                        "floatIntoObjectIntoArray": 0.3
                    }
                ]
            },
            "arrayNode": [
                {
                    "integerIntoArray": 12,
                    "booleanIntoArray": false
                }
            ]
        }';
        $config->setData($data);
        $this->assertEquals(json_decode($data), $config->getData());
    }
}
