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
            'type'  => 'checkbox',
            'value' => 1
        ]);
        $checkboxSet = Fields::create('checkbox', [
            'type'   => 'checkbox-set',
            'values' => [1, 2, 3]
        ]);
        $radio = Fields::create('radio', [
            'type'  => 'radio',
            'value' => 1
        ]);
        $radioSet = Fields::create('radio', [
            'type'   => 'radio-set',
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
        $this->assertInstanceOf('Pop\Form\Element\Input\Checkbox', $checkbox);
        $this->assertInstanceOf('Pop\Form\Element\CheckboxSet', $checkboxSet);
        $this->assertInstanceOf('Pop\Form\Element\Input\Radio', $radio);
        $this->assertInstanceOf('Pop\Form\Element\RadioSet', $radioSet);
        $this->assertInstanceOf('Pop\Form\Element\Input\Button', $inputButton);
        $this->assertInstanceOf('Pop\Form\Element\Input\Datalist', $dataList);
        $this->assertInstanceOf('Pop\Form\Element\Input\DateTime', $dateTime);
        $this->assertInstanceOf('Pop\Form\Element\Input\DateTimeLocal', $dateTimeLocal);
        $this->assertInstanceOf('Pop\Form\Element\Input\Number', $number);
        $this->assertInstanceOf('Pop\Form\Element\Input\Range', $range);
    }

    public function testCreateCheckedCheckboxAndRadioFromConfig()
    {
        $checkbox = Fields::create('subscribe', [
            'type'    => 'checkbox',
            'value'   => 'yes',
            'checked' => true
        ]);
        $radio = Fields::create('color', [
            'type'    => 'radio',
            'value'   => 'red',
            'checked' => 'red'
        ]);
        $this->assertTrue($checkbox->isChecked());
        $this->assertTrue($radio->isChecked());
    }

    public function testCreateWithPrependAppendDisabledReadonly()
    {
        $field = Fields::create('amount', [
            'type'     => 'text',
            'prepend'  => '$',
            'append'   => '.00',
            'disabled' => true,
            'readonly' => true
        ]);
        $this->assertEquals('$', $field->getPrepend());
        $this->assertEquals('.00', $field->getAppend());
        $this->assertTrue($field->isDisabled());
        $this->assertTrue($field->isReadonly());
    }

    public function testCreateCheckboxSetAndRadioSetAttributesAndLegendFromConfig()
    {
        $checkboxSet = Fields::create('colors', [
            'type'       => 'checkbox-set',
            'values'     => ['red' => 'Red', 'blue' => 'Blue'],
            'legend'     => 'Colors',
            'attributes' => ['class' => 'color-checkbox']
        ]);
        $radioSet = Fields::create('size', [
            'type'       => 'radio-set',
            'values'     => ['sm' => 'Small', 'lg' => 'Large'],
            'legend'     => 'Size',
            'attributes' => ['class' => 'size-radio']
        ]);

        $this->assertEquals('Colors', $checkboxSet->getLegend());
        $this->assertEquals('Size', $radioSet->getLegend());
        $this->assertStringContainsString('color-checkbox', (string)$checkboxSet);
        $this->assertStringContainsString('size-radio', (string)$radioSet);
    }

    public function testCreateCsrf()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $csrf = Fields::create('csrf', [
            'type' => 'csrf',
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\Csrf', $csrf);
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

    public function testTypeCannotResolveToNonElementClassInSameNamespace()
    {
        // 'Exception' is a real class in the Pop\Form\Element\Input namespace, but it's
        // not a form element. Fields::create() must not be able to instantiate it via
        // an attacker/config-supplied 'type' string.
        $this->expectException('Pop\Form\Exception');
        Fields::create('bad', [
            'type' => 'exception'
        ]);
    }

    public function testCreateFileWithAllowedTypesAndMaxSize()
    {
        $file = Fields::create('upload', [
            'type'          => 'file',
            'allowed-types' => ['pdf', '.docx'],
            'max-size'      => 2000000
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\File', $file);
        $this->assertEquals(['pdf', 'docx'], $file->getAllowedTypes());
        $this->assertEquals(2000000, $file->getMaxSize());
    }

    public function testGetConfigFromTable()
    {
        TestAsset\Users::setDb(Db\Db::sqliteConnect(['database' => __DIR__ . '/tmp/db.sqlite']));
        $fields = Fields::getConfigFromTable(TestAsset\Users::getTableInfo(), null, null, ['id']);
        $this->assertEquals(4, count($fields));
    }

    public function testGetConfigFromTableWithScalarOmit()
    {
        TestAsset\Users::setDb(Db\Db::sqliteConnect(['database' => __DIR__ . '/tmp/db.sqlite']));
        $fields = Fields::getConfigFromTable(TestAsset\Users::getTableInfo(), null, null, 'id');
        $this->assertEquals(4, count($fields));
        $this->assertArrayNotHasKey('id', $fields);
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
