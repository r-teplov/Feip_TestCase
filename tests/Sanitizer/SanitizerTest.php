<?php

use FeipTestCase\Sanitizer\Rules\Exceptions\UnknownRuleException;
use FeipTestCase\Sanitizer\Sanitizer;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    /**
     * @var Sanitizer
     */
    private Sanitizer $sanitizer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->sanitizer = new Sanitizer();
    }

    /**
     * @dataProvider stringDataProvider
     * @param array $fields
     * @param array $payload
     * @param array $expected
     * @return void
     * @throws UnknownRuleException
     */
    public function testString(array $fields, array $payload, array $expected): void
    {
        $this->sanitizer->run($fields, $payload);
        $this->assertEquals($expected, $this->sanitizer->getExpandedValues());
    }

    public function stringDataProvider(): array
    {
        return [
            [
                [
                    'f1' => 'string',
                    'f2' => 'string',
                    'f3' => 'string',
                    'f4' => 'array:string',
                    'f5' => [
                        'f1' => 'string',
                        'f2' => 'string',
                        'f3' => [
                            'f1' => 'string',
                            'f2' => 'string',
                        ],
                    ],
                ],
                [
                    'f1' => 'some string value',
                    'f2' => '125',
                    'f3' => 1000,
                    'f4' => ['str1', 2, true, 'str4'],
                    'f5' => [
                        'f1' => false,
                        'f2' => 'false',
                        'f3' => [
                            'f1' => 'null',
                            'f2' => 'boolean',
                        ],
                    ],
                ],
                [
                    'f1' => 'some string value',
                    'f2' => '125',
                    'f4' => [0 => 'str1', 3 => 'str4'],
                    'f5' => [
                        'f2' => 'false',
                        'f3' => [
                            'f1' => 'null',
                            'f2' => 'boolean',
                        ],
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider integerDataProvider
     * @param array $fields
     * @param array $payload
     * @param array $expected
     * @return void
     * @throws UnknownRuleException
     */
    public function testInteger(array $fields, array $payload, array $expected): void
    {
        $this->sanitizer->run($fields, $payload);
        $this->assertEquals($expected, $this->sanitizer->getExpandedValues());

    }

    public function integerDataProvider(): array
    {
        return [
            [
                [
                    'f1' => 'integer',
                    'f2' => 'integer',
                    'f3' => 'integer',
                    'f4' => 'array:integer',
                    'f5' => [
                        'f1' => 'integer',
                        'f2' => 'array:integer',
                        'f3' => [
                            'f1' => [
                                'f1' => 'integer'
                            ],
                        ],
                    ],
                ],
                [
                    'f1' => 1000,
                    'f2' => '5000',
                    'f3' => 10.5,
                    'f4' => [100, 200, '300', '400qwerty'],
                    'f5' => [
                        'f1' => null,
                        'f2' => [false, 10_000, '5000'],
                    ],
                ],
                [
                    'f1' => 1000,
                    'f2' => 5000,
                    'f4' => [100, 200, 300],
                    'f5' => [
                        'f2' => [1 => 10000, 2 => 5000],
                    ],
                ]
            ],
        ];
    }

    /**
     * @dataProvider floatDataProvider
     * @param array $fields
     * @param array $payload
     * @param array $expected
     * @throws UnknownRuleException
     * @return void
     */
    public function testFloat(array $fields, array $payload, array $expected): void
    {
        $this->sanitizer->run($fields, $payload);
        $this->assertEquals($expected, $this->sanitizer->getExpandedValues());
    }

    public function floatDataProvider(): array
    {
        return [
            [
                [
                    'f1' => 'float',
                    'f2' => 'float',
                    'f3' => 'float',
                    'f4' => [
                        'f1' => 'float',
                        'f2' => 'array:float',
                        'f3' => 'float',
                        'f4' => [
                            'f1' => 'float'
                        ],
                    ],
                ],
                [
                    'f1' => 100,
                    'f2' => 10.3,
                    'f3' => '500',
                    'f4' => [
                        'f1' => null,
                        'f2' => [1e5, false, 'true', 0],
                        'f3' => 10_000,
                        'f4' => [
                            'f1' => '125.10',
                        ],
                    ],
                ],
                [
                    'f1' => 100,
                    'f2' => 10.3,
                    'f3' => 500,
                    'f4' => [
                        'f2' => [100000, 3 => 0],
                        'f3' => 10000,
                        'f4' => [
                            'f1' => 125.10,
                        ],
                    ],
                ]
            ]
        ];
    }

    /**
     * @dataProvider telNumberDataProvider
     * @param array $fields
     * @param array $payload
     * @param array $expected
     * @return void
     * @throws UnknownRuleException
     */
    public function testTelNumber(array $fields, array $payload, array $expected): void
    {
        $this->sanitizer->run($fields, $payload);
        $this->assertEquals($expected, $this->sanitizer->getExpandedValues());
    }

    public function telNumberDataProvider(): array
    {
        return [
            [
                [
                    'f1' => 'telNumber',
                    'f2' => 'telNumber',
                    'f3' => 'telNumber',
                    'f4' => 'telNumber',
                    'f5' => 'array:telNumber',
                    'f6' => [
                        'f1' => 'telNumber',
                    ],
                    'f7' => [
                        'f1' => [
                            'f1' => 'telNumber',
                        ]
                    ]
                ],
                [
                    'f1' => '84242553862',
                    'f2' => '+79001234567',
                    'f3' => null,
                    'f4' => 79241112233,
                    'f5' => ['+7 (921) 999-88-77', '690-690 - завхоз', ' 8 (42423) 66-777'],
                    'f6' => [
                        'f1' => 'true'
                    ],
                    'f7' => [
                        'f1' => [
                            'f1' => '+7 921 555 00 00',
                        ]
                    ]
                ],
                [
                    'f1' => '74242553862',
                    'f2' => '79001234567',
                    'f4' => '79241112233',
                    'f5' => ['79219998877', 2 => '74242366777'],
                    'f7' => [
                        'f1' => [
                            'f1' => '79215550000'
                        ],
                    ],
                ]
            ]
        ];
    }
}