<?php

namespace Pop\Form\Test;

use Pop\Form\Fields;
use Pop\Validator;

class FieldsTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $fields = new Fields([
            'username' => [
                'type' => 'text',
                'label' => 'Username:',
                'required' => true,
                'value' => 'my<script></script>"username"'
            ],
            'colors' => [
                'type' => 'checkbox',
                'label' => 'Colors',
                'value' => [
                    'Red'   => 'Red',
                    'White' => 'White',
                    'Blue'  => 'Blue'
                ],
                'marked' => [
                    'Red', 'White'
                ]
            ],
            'submit' => [
                'type' => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $this->assertInstanceOf('Pop\Form\Fields', $fields);
        $this->assertEquals(3, count($fields->getFields()));
    }

    public function testConstructorAddFieldsFromTable()
    {
        $fields = new Fields(TestAsset\Users::getTableInfo());
        $this->assertInstanceOf('Pop\Form\Fields', $fields);
        $this->assertEquals(4, count($fields->getFields()));
    }

    public function testSetFieldAttribute()
    {
        $field = new Fields([
            'username' => [
                'type' => 'text',
                'label' => 'Username:',
                'required' => true
            ]
        ]);
        $field->setFieldAttribute('username', 'style', 'display: block;');
    }

    public function testSetFieldAttributes()
    {
        $field = new Fields([
            'username' => [
                'type' => 'text',
                'label' => 'Username:',
                'required' => true
            ]
        ]);
        $field->setFieldAttributes('username', ['style' => 'display: block;']);
    }

    public function testFactory()
    {
        $field = Fields::factory('username',  [
            'type'     => 'text',
            'label'    => 'Username:',
            'required' => true,
            'error'    => [
                'div' => ['class' => 'error-class'],
                'pre' => true
            ],
            'attributes' => [
                'style' => 'display: block;'
            ],
            'validators' => new Validator\AlphaNumeric()
        ], ['username' => 'Username here...']);
        $this->assertInstanceOf('Pop\Form\Element\AbstractElement', $field);
    }

    public function testFactoryMultipleValidators()
    {
        $field = Fields::factory('username',  [
            'type'     => 'text',
            'label'    => 'Username:',
            'required' => true,
            'error'    => [
                'div' => ['class' => 'error-class'],
                'pre' => true
            ],
            'attributes' => [
                'style' => 'display: block;'
            ],
            'validators' => [
                new Validator\AlphaNumeric(),
                new Validator\LengthGte(6)
            ]
        ]);
        $this->assertInstanceOf('Pop\Form\Element\AbstractElement', $field);
    }

    public function testFactoryWithValues()
    {
        $field = Fields::factory('colors',  [
            'type'   => 'checkbox',
            'label'  => 'Colors',
            'marked' => 'White',
            'value'  => [
                'Red'   => 'Red',
                'White' => 'White',
                'Blue'  => 'Blue'
            ]
        ], [ 'colors' => [
            'Red'   => 'Red',
            'White' => 'White'
        ]]);
        $this->assertInstanceOf('Pop\Form\Element\CheckboxSet', $field);
    }

    public function testFactoryButton()
    {
        $field = Fields::factory('my_button',  [
            'type'     => 'button',
            'value'    => 'MY BUTTON'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Button', $field);
    }

    public function testFactoryInputButton()
    {
        $field = Fields::factory('my_button',  [
            'type'     => 'input-button',
            'value'    => 'MY BUTTON'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\Button', $field);
    }

    /**
     * @runInSeparateProcess
     */
    public function testFactoryCsrf()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $field = Fields::factory('my_csrf',  [
            'type'     => 'csrf',
            'value'    => 'CSRF'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\Csrf', $field);
    }

    /**
     * @runInSeparateProcess
     */
    public function testFactoryCaptcha()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $field = Fields::factory('my_captcha',  [
            'type'     => 'captcha',
            'value'    => 'CAPTCHA'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\Captcha', $field);
    }

    public function testFactoryNoFieldTypeException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $field = Fields::factory('colors',  [
            'label'  => 'Colors',
        ]);
    }

    public function testFactoryNoFieldClassException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $field = Fields::factory('bad_field',  [
            'type'   => 'badfield',
            'label'  => 'Colors',
        ]);
    }

    public function testAddFieldsFromTable()
    {
        $attribs = ['text' => ['class' => 'input-field']];
        $values  = ['username' => 'username', 'email' => 'email'];
        $fields  = new Fields();
        $fields->addFieldsFromTable(TestAsset\Users::getTableInfo(), $attribs, $values, 'id');
        $this->assertInstanceOf('Pop\Form\Fields', $fields);
        $this->assertEquals(3, count($fields->getFields()));
    }

    public function testAddFieldsFromTableType()
    {
        $attribs = ['textarea' => ['class' => 'input-field']];
        $values  = ['username' => ['type' => 'textarea']];
        $fields  = new Fields();
        $fields->addFieldsFromTable(TestAsset\Users::getTableInfo(), $attribs, $values, 'id');
        $this->assertInstanceOf('Pop\Form\Fields', $fields);
        $this->assertEquals(3, count($fields->getFields()));
    }

    public function testAddFieldsFromTableHidden()
    {
        $attribs = ['text' => ['class' => 'input-field']];
        $values  = ['username' => ['type' => 'hidden']];
        $fields  = new Fields();
        $fields->addFieldsFromTable(TestAsset\Users::getTableInfo(), $attribs, $values, 'id');
        $this->assertInstanceOf('Pop\Form\Fields', $fields);
        $this->assertEquals(3, count($fields->getFields()));
    }

    public function testAddFieldsFromTableException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $table   = TestAsset\Users::getTableInfo();
        unset($table['tableName']);
        $fields  = new Fields();
        $fields->addFieldsFromTable($table);
    }

    public function testMagicMethods()
    {
        $fields = new Fields();
        $fields->username = [
            'type' => 'text',
            'label' => 'Username:'
        ];
        $this->assertEquals('text', $fields->username['type']);
        $this->assertTrue(isset($fields->username));
        unset($fields->username);
        $this->assertFalse(isset($fields->username));
    }

    public function testOffsetMethods()
    {
        $fields = new Fields();
        $fields['username'] = [
            'type' => 'text',
            'label' => 'Username:'
        ];
        $this->assertEquals('text', $fields['username']['type']);
        $this->assertTrue(isset($fields['username']));
        unset($fields['username']);
        $this->assertFalse(isset($fields['username']));
    }

}
