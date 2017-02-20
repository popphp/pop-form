<?php

namespace Pop\Form\Test;

use Pop\Form\Fields;
use Pop\Db;

class FieldsTest extends \PHPUnit_Framework_TestCase
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
        $dateTime = Fields::create('datetime', [
            'type'  => 'datetime'
        ]);
        $dateTimeLocal = Fields::create('datetime', [
            'type'  => 'datetime-local'
        ]);
        $number = Fields::create('number', [
            'type'  => 'number',
            'min'   => 1,
            'max'   => 10
        ]);
        $range = Fields::create('range', [
            'type'  => 'range',
            'min'   => 1,
            'max'   => 10
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Button', $button);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
        $this->assertInstanceOf('Pop\Form\Element\SelectMultiple', $selectMultiple);
        $this->assertInstanceOf('Pop\Form\Element\Textarea', $textarea);
        $this->assertInstanceOf('Pop\Form\Element\CheckboxSet', $checkbox);
        $this->assertInstanceOf('Pop\Form\Element\RadioSet', $radio);
        $this->assertInstanceOf('Pop\Form\Element\Input\Button', $inputButton);
        $this->assertInstanceOf('Pop\Form\Element\Input\DateTime', $dateTime);
        $this->assertInstanceOf('Pop\Form\Element\Input\DateTimeLocal', $dateTimeLocal);
        $this->assertInstanceOf('Pop\Form\Element\Input\Number', $number);
        $this->assertInstanceOf('Pop\Form\Element\Input\Range', $range);
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
        $fields = Fields::getConfigFromTable(TestAsset\Users::getTableInfo());
        $this->assertEquals(4, count($fields));
    }

}
