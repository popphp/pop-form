<?php

namespace Pop\Form\Test;

use Pop\Form\FormConfig;
use Pop\Validator;
use PHPUnit\Framework\TestCase;

class FormConfigTest extends TestCase
{

    public function testToJson1()
    {
        $config = new FormConfig([
            'username' => [
                'type'       => 'text',
                'label'      => 'Username',
                'required'   => true,
                'validators' => [
                    new Validator\RegEx('/[a-zA-Z0-9]/', 'It has to be alphanumeric'),
                    function ($value) {
                        return null;
                    }
                ],
                'attributes' => [
                    'size' => 40
                ]
            ],
            'email' => [
                'type'       => 'email',
                'label'      => 'Email',
                'required'   => true,
                'validators' => new Validator\Email(),
                'attributes' => [
                    'size' => 40
                ]
            ],
            'submit' => [
                'type'  => 'submit',
                'label' => '&nbsp;',
                'value' => 'Submit'
            ]
        ]);

        $json = json_decode($config->jsonSerialize(JSON_PRETTY_PRINT), true);
        $this->assertTrue(isset($json['username']));
        $this->assertTrue(isset($json['username']['validators']));
        $this->assertTrue(isset($json['username']['validators'][0]));
        $this->assertEquals('RegEx', $json['username']['validators'][0]['type']);
    }

    public function testToJson2()
    {
        $config = new FormConfig([
            [
                'username' => [
                    'type'       => 'text',
                    'label'      => 'Username',
                    'required'   => true,
                    'validators' => [
                        new Validator\RegEx('/[a-zA-Z0-9]/', 'It has to be alphanumeric'),
                        function ($value) {
                            return null;
                        }
                    ],
                    'attributes' => [
                        'size' => 40
                    ]
                ]
            ],
            [
                'email' => [
                    'type'       => 'email',
                    'label'      => 'Email',
                    'required'   => true,
                    'validators' => new Validator\Email(),
                    'attributes' => [
                        'size' => 40
                    ]
                ]
            ],
            [
                'submit' => [
                    'type'  => 'submit',
                    'label' => '&nbsp;',
                    'value' => 'Submit'
                ]
            ]
        ]);

        $json = json_decode($config->jsonSerialize(JSON_PRETTY_PRINT), true);
        $this->assertTrue(isset($json[0]['username']));
        $this->assertTrue(isset($json[0]['username']['validators']));
        $this->assertTrue(isset($json[0]['username']['validators'][0]));
        $this->assertEquals('RegEx', $json[0]['username']['validators'][0]['type']);
    }

    public function testFromJson1()
    {
        $config = new FormConfig([
            'username' => [
                'type'       => 'text',
                'label'      => 'Username',
                'required'   => true,
                'validators' => [
                    new Validator\RegEx('/[a-zA-Z0-9]/', 'It has to be alphanumeric'),
                    function ($value) {
                        return null;
                    }
                ],
                'attributes' => [
                    'size' => 40
                ]
            ],
            'email' => [
                'type'       => 'email',
                'label'      => 'Email',
                'required'   => true,
                'validators' => new Validator\Email(),
                'attributes' => [
                    'size' => 40
                ]
            ],
            'submit' => [
                'type'  => 'submit',
                'label' => '&nbsp;',
                'value' => 'Submit'
            ]
        ]);

        $formConfig = FormConfig::createFromJson($config->jsonSerialize(JSON_PRETTY_PRINT));
        $this->assertTrue(isset($formConfig['username']));
        $this->assertTrue(isset($formConfig['username']['validators']));
        $this->assertTrue(isset($formConfig['username']['validators'][0]));
        $this->assertInstanceOf('Pop\Validator\RegEx', $formConfig['username']['validators'][0]);
    }



    public function testFromJson2()
    {
        $config = new FormConfig([
            [
                'username' => [
                    'type'       => 'text',
                    'label'      => 'Username',
                    'required'   => true,
                    'validators' => [
                        new Validator\RegEx('/[a-zA-Z0-9]/', 'It has to be alphanumeric'),
                        function ($value) {
                            return null;
                        }
                    ],
                    'attributes' => [
                        'size' => 40
                    ]
                ]
            ],
            [
                'email' => [
                    'type'       => 'email',
                    'label'      => 'Email',
                    'required'   => true,
                    'validators' => new Validator\Email(),
                    'attributes' => [
                        'size' => 40
                    ]
                ]
            ],
            [
                'submit' => [
                    'type'  => 'submit',
                    'label' => '&nbsp;',
                    'value' => 'Submit'
                ]
            ]
        ]);

        $formConfig = FormConfig::createFromJson($config->jsonSerialize(JSON_PRETTY_PRINT));
        $this->assertTrue(isset($formConfig[0]['username']));
        $this->assertTrue(isset($formConfig[0]['username']['validators']));
        $this->assertTrue(isset($formConfig[0]['username']['validators'][0]));
        $this->assertInstanceOf('Pop\Validator\RegEx', $formConfig[0]['username']['validators'][0]);
    }

}
