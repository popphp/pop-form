<?php

namespace Pop\Form\Test;

use Pop\Form\Fields;
use Pop\Db;
use Pop\Validator;
use PHPUnit\Framework\TestCase;

class FieldsTest extends TestCase
{

    public function testCreate()
    {
        $button = Fields::create('button', [
            'type'  => 'button',
            'value' => 'Click Me!'
        ]);
        $select = Fields::create('select', [
            'type'   => 'select',
            'values' => [1, 2, 3]
        ]);
        $selectMultiple = Fields::create('select', [
            'type'   => 'select-multiple',
            'values' => [1, 2, 3]
        ]);
        $textarea = Fields::create('textarea', [
            'type'   => 'textarea',
            'value' => 'Hello'
        ]);
        $checkbox = Fields::create('checkbox', [
            'type'   => 'checkbox',
            'values' => [1, 2, 3]
        ]);
        $radio = Fields::create('radio', [
            'type'   => 'radio',
            'values' => [1, 2, 3]
        ]);
        $inputButton = Fields::create('input-button', [
            'type'  => 'input-button',
            'value' => 'Click Me!'
        ]);
        $dataList = Fields::create('datalist', [
            'type'  => 'datalist',
            'values' => [
                'Books',
                'Bugs'
            ]
        ]);
        $dateTime = Fields::create('datetime', [
            'type'  => 'datetime'
        ]);
        $dateTimeLocal = Fields::create('datetime', [
            'type'  => 'datetime-local'
        ]);
        $number = Fields::create('number', [
            'type'  => 'number',
            'min'   => 1,
            'max'   => 10,
            'validators' => new Validator\LessThan(2)
        ]);
        $range = Fields::create('range', [
            'type'  => 'range',
            'min'   => 1,
            'max'   => 10,
            'label-attributes' => [
                'class' => 'label'
            ],
            'hint' => 'This is a hint',
            'hint-attributes' => [
                'class' => 'hint'
            ],
            'attributes' => [
                'class' => 'element'
            ],
            'validators' => [
                new Validator\LessThan(2)
            ]
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Button', $button);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
        $this->assertInstanceOf('Pop\Form\Element\SelectMultiple', $selectMultiple);
        $this->assertInstanceOf('Pop\Form\Element\Textarea', $textarea);
        $this->assertInstanceOf('Pop\Form\Element\CheckboxSet', $checkbox);
        $this->assertInstanceOf('Pop\Form\Element\RadioSet', $radio);
        $this->assertInstanceOf('Pop\Form\Element\Input\Button', $inputButton);
        $this->assertInstanceOf('Pop\Form\Element\Input\Datalist', $dataList);
        $this->assertInstanceOf('Pop\Form\Element\Input\DateTime', $dateTime);
        $this->assertInstanceOf('Pop\Form\Element\Input\DateTimeLocal', $dateTimeLocal);
        $this->assertInstanceOf('Pop\Form\Element\Input\Number', $number);
        $this->assertInstanceOf('Pop\Form\Element\Input\Range', $range);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateCsrfAndCaptcha()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $csrf = Fields::create('csrf', [
            'type'   => 'csrf'
        ]);
        $captcha = Fields::create('captcha', [
            'type'   => 'captcha'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\Csrf', $csrf);
        $this->assertInstanceOf('Pop\Form\Element\Input\Captcha', $captcha);
    }

    public function testTypeNotSetException()
    {
        $this->expectException('Pop\Form\Exception');
        $number = Fields::create('number', [
            'min'   => 1,
            'max'   => 10
        ]);
    }

    public function testClassDoesNotExistException()
    {
        $this->expectException('Pop\Form\Exception');
        $number = Fields::create('number', [
            'type'  => 'Bad'
        ]);
    }

    public function testGetConfigFromTable()
    {
        TestAsset\Users::setDb(Db\Db::sqliteConnect(['database' => __DIR__ . '/tmp/db.sqlite']));
        $fields = Fields::getConfigFromTable(TestAsset\Users::getTableInfo(), null, null, ['id']);
        $this->assertEquals(4, count($fields));
    }

    public function testTableNameNotSetException()
    {
        $this->expectException('Pop\Form\Exception');
        $fields = Fields::getConfigFromTable([]);
    }

    public function testGetConfigFromTableAttribsAndConfig()
    {
        $attribs = [
            'text' => [
                'class' => 'text-field'
            ]
        ];
        $config = [
            'info' => [
                'type' => 'textarea',
                'validators' => new Validator\NotEmpty()
            ]
        ];
        $fields = Fields::getConfigFromTable(TestAsset\Users::getTableInfo(), $attribs, $config);
        $this->assertEquals(5, count($fields));
    }

    public function testGetConfigFromTableConfig()
    {
        $config = [
            'email' => [
                'validators' => new Validator\NotEmpty()
            ]
        ];
        $fields = Fields::getConfigFromTable(TestAsset\Users::getTableInfo(), null, $config);
        $this->assertEquals(5, count($fields));
    }

}
