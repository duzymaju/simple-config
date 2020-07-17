<?php

use PHPUnit\Framework\TestCase;
use SimpleConfig\Config;
use SimpleConfig\Exception\InvalidDataException;
use SimpleConfig\Exception\NoConfigException;
use SimpleConfig\Schema;

final class StructureAndConfigTest extends TestCase
{
    /** @var Schema */
    private $schema;

    /** @var stdClass */
    private $config;

    /** @var stdClass */
    private $defaultConfig;

    /** @var stdClass */
    private $validChangedConfig;

    /** @var stdClass */
    private $invalidChangedConfig;

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
                    ->maxLength(15)
                ->end()
                ->arrayNode('arrayInsideObject')
                    ->objectItems()
                        ->stringNode('stringInsideObjectInsideArray')->end()
                        ->floatNode('floatInsideObjectInsideArray')
                            ->allowedValues([0.3, 4, 5.17])
                        ->end()
                        ->booleanNode('booleanInsideObjectInsideArray')
                            ->defaultValue(true)
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('arrayNode')
                ->defaultValue([11])
                ->minLength(1)
                ->maxLength(3)
                ->integerItems()
                    ->minValue(10)
                    ->maxValue(20)
                    ->required()
                ->end()
            ->end()
        ;
        $this->config = (object) [
            'objectNode' => (object) [
                'stringInsideObject' => 'abc.123',
                'arrayInsideObject' => [
                    (object) [
                        'stringInsideObjectInsideArray' => 'def',
                        'booleanInsideObjectInsideArray' => false,
                    ],
                    (object) [
                        'floatInsideObjectInsideArray' => 0.3,
                        'booleanInsideObjectInsideArray' => true,
                    ],
                ]
            ],
            'arrayNode' => [
                12,
                14,
            ],
        ];
        $this->defaultConfig = (object) [
            'objectNode' => (object) [
                'stringInsideObject' => 'stringValue',
            ],
            'arrayNode' => [11],
        ];
        $this->validChangedConfig = json_decode(json_encode($this->config));
        $this->validChangedConfig->objectNode->arrayInsideObject[0]->floatInsideObjectInsideArray = 5.17;
        $this->invalidChangedConfig = json_decode(json_encode($this->config));
        $this->invalidChangedConfig->objectNode->arrayInsideObject[0]->floatInsideObjectInsideArray = 8.1;
    }

    /** Test getting default values */
    public function testGettingDefaultValues()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $this->assertEquals($this->defaultConfig, $config->getDefaults());
    }

    /** Test setting valid input object */
    public function testSettingValidInputObject()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $config->setData($this->config);
        $this->assertEquals($this->config, $config->getData());
    }

    /** Test setting valid input JSON */
    public function testSettingValidInputJson()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = '{
            "objectNode": {
                "stringInsideObject": "abc.123",
                "arrayInsideObject": [
                    {
                        "stringInsideObjectInsideArray": "def",
                        "booleanInsideObjectInsideArray": false
                    },
                    {
                        "floatInsideObjectInsideArray": 0.3,
                        "booleanInsideObjectInsideArray": true
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

    /** Test setting input object with param undefined in schema and validation */
    public function testSettingInputObjectWithValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = (object) [
            'objectNode' => (object) [
                'invalidNode' => 123,
            ],
        ];
        $config->setData($data);
        $this->assertEquals($this->defaultConfig, $config->getData());
    }

    /** Test setting input JSON with param undefined in schema and validation */
    public function testSettingInputJsonWithParamUndefinedInSchemaAndValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = '{
            "objectNode": {
                "invalidNode": 123
            }
        }';
        $config->setData($data);
        $this->assertEquals($this->defaultConfig, $config->getData());
    }

    /** Test setting invalid input object with validation */
    public function testSettingInvalidInputObjectWithValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = (object) [
            'objectNode' => (object) [
                'invalidNode' => 123,
                'stringInsideObject' => 456,
            ],
        ];
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('objectNode.stringInsideObject: Value is not a string.');
        $config->setData($data);
    }

    /** Test setting invalid input JSON with validation */
    public function testSettingInvalidInputJsonWithValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = '{
            "objectNode": {
                "invalidNode": 123,
                "stringInsideObject": 456
            }
        }';
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('objectNode.stringInsideObject: Value is not a string.');
        $config->setData($data);
    }

    /** Test setting invalid input object without validation */
    public function testSettingInvalidInputObjectWithoutValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = (object) [
            'objectNode' => (object) [
                'invalidNode' => 123
            ],
        ];
        $config->setData($data, false);
        $this->assertEquals($data, $config->getData());
    }

    /** Test setting invalid input JSON without validation */
    public function testSettingInvalidInputJsonWithoutValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = '{
            "objectNode": {
                "invalidNode": 123
            }
        }';
        $config->setData($data, false);
        $this->assertEquals(json_decode($data), $config->getData());
    }

    /** Test adding input without setting it first */
    public function testAddingInputWithoutSettingItFirst()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = (object) [
            'arrayNode' => [12, 13, 14],
        ];
        $this->expectException(NoConfigException::class);
        $this->expectExceptionMessage('Configuration data has to be defined before change.');
        $config->changeData($data);
    }

    /** Test adding invalid input object with validation */
    public function testAddingValidInputObjectWithValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $config->setData($this->config);
        $data = (object) [
            'objectNode' => (object) [
                'arrayInsideObject' => (object) [
                    0 => (object) [
                        'floatInsideObjectInsideArray' => 5.17,
                    ],
                ],
            ],
        ];
        $config->changeData($data);
        $this->assertEquals($this->validChangedConfig, $config->getData());
    }

    /** Test adding invalid input JSON with validation */
    public function testAddingValidInputJsonWithValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $config->setData($this->config);
        $data = '{
            "objectNode": {
                "arrayInsideObject": {
                    "0": {
                        "floatInsideObjectInsideArray": 5.17
                    }
                }
            }
        }';
        $config->changeData($data);
        $this->assertEquals($this->validChangedConfig, $config->getData());
    }

    /** Test adding invalid input object with validation */
    public function testAddingInvalidInputObjectWithValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $config->setData($this->config);
        $data = (object) [
            'objectNode' => (object) [
                'arrayInsideObject' => (object) [
                    0 => (object) [
                        'floatInsideObjectInsideArray' => 8.1,
                    ],
                ],
            ],
        ];
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('objectNode.arrayInsideObject[0].floatInsideObjectInsideArray: Value should be one of "0.3", "4", "5.17".');
        $config->changeData($data);
    }

    /** Test adding invalid input JSON with validation */
    public function testAddingInvalidInputJsonWithValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $config->setData($this->config);
        $data = '{
            "objectNode": {
                "arrayInsideObject": {
                    "0": {
                        "floatInsideObjectInsideArray": 8.1
                    }
                }
            }
        }';
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('objectNode.arrayInsideObject[0].floatInsideObjectInsideArray: Value should be one of "0.3", "4", "5.17".');
        $config->changeData($data);
    }

    /** Test adding invalid input object without validation */
    public function testAddingInvalidInputObjectWithoutValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $config->setData($this->config);
        $data = (object) [
            'objectNode' => (object) [
                'arrayInsideObject' => (object) [
                    0 => (object) [
                        'floatInsideObjectInsideArray' => 8.1,
                    ],
                ],
            ],
        ];
        $config->changeData($data, false);
        $this->assertEquals($this->invalidChangedConfig, $config->getData());
    }

    /** Test adding invalid input JSON without validation */
    public function testAddingInvalidInputJsonWithoutValidation()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $config->setData($this->config);
        $data = '{
            "objectNode": {
                "arrayInsideObject": {
                    "0": {
                        "floatInsideObjectInsideArray": 8.1
                    }
                }
            }
        }';
        $config->changeData($data, false);
        $this->assertEquals($this->invalidChangedConfig, $config->getData());
    }

    /** Test getting existing params */
    public function testGettingExistingParams()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $config->setData($this->config);
        $this->assertEquals('abc.123', $config->getParam('objectNode.stringInsideObject'));
        $this->assertEquals('def',
            $config->getParam('objectNode.arrayInsideObject.0.stringInsideObjectInsideArray'));
        $this->assertEquals(false,
            $config->getParam('objectNode.arrayInsideObject.0.booleanInsideObjectInsideArray'));
        $this->assertEquals(0.3,
            $config->getParam('objectNode.arrayInsideObject.1.floatInsideObjectInsideArray'));
        $this->assertEquals(true,
            $config->getParam('objectNode.arrayInsideObject.1.booleanInsideObjectInsideArray'));
        $this->assertEquals(12, $config->getParam('arrayNode.0'));
        $this->assertEquals(14, $config->getParam('arrayNode.1'));
    }

    /** Test getting not existing params */
    public function testGettingNotExistingParams()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $config->setData($this->config);
        $this->assertEquals(null, $config->getParam('notExistedParam.abc'));
        $this->assertEquals(123, $config->getParam('notExistedParam.abc', 123));
    }

    /** Test JSON serialization of default data */
    public function testJsonSerializationOfDefaultData()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $this->assertEquals(json_encode($this->defaultConfig), $config->jsonSerialize());
    }

    /** Test JSON serialization of defined data */
    public function testJsonSerializationOfDefinedData()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = json_decode(json_encode($this->defaultConfig));
        $data->arrayNode = [12, 13];
        $config->setData($data);
        $this->assertEquals(json_encode($data), $config->jsonSerialize());
    }

    /** Test converting to string of default data */
    public function testConvertingToStringOfDefaultData()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $this->assertEquals(json_encode($this->defaultConfig), (string) $config);
    }

    /** Test converting to string of defined data */
    public function testConvertingToStringOfDefinedData()
    {
        $config = new Config();
        $config->setSchema($this->schema);
        $data = json_decode(json_encode($this->defaultConfig));
        $data->arrayNode = [12, 13];
        $config->setData($data);
        $this->assertEquals(json_encode($data), (string) $config);
    }

    /** Test converting to string of defined data without compression */
    public function testConvertingToStringOfDefinedDataWithoutCompression()
    {
        $config = new Config();
        $config->setCompression(false);
        $config->setSchema($this->schema);
        $data = json_decode(json_encode($this->defaultConfig));
        $data->arrayNode = [12, 13];
        $config->setData($data);
        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), (string) $config);
    }
}
