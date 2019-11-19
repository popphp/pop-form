<?php

namespace Pop\Form\Test;

use Pop\Form\Element\Select;
use Pop\Form\Element\SelectMultiple;
use Pop\Validator;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{

    public function testConstructor()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], 'Red', null, '    ');
        $select->setAttribute('class', 'select-menu');
        $select->setRequired(true);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
        $this->assertEquals('select', $select->getType());
        $select->resetValue();
        $this->assertTrue(empty($select->getValue()));
    }


    public function testConstructorMultiple()
    {
        $select = new SelectMultiple('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], 'Red', null, '    ');
        $select->setAttribute('class', 'select-menu');
        $select->setRequired(true);
        $select->setValue(['White', 'Blue']);
        $this->assertInstanceOf('Pop\Form\Element\SelectMultiple', $select);
        $this->assertEquals('select', $select->getType());
        $select->resetValue();
        $this->assertTrue(empty($select->getValue()));
    }

    public function testOptGroup()
    {
        $select = new Select('my_select', [
            'Colors 1' => ['Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'],
            'Colors 2' => ['Yellow' => 'Yellow', 'Green' => 'Green', 'Purple' => 'Purple']
        ], 'Red', null, '    ');
        $select->setValue('Yellow');
        $this->assertEquals(6, count($select->getOptions()));
        $this->assertEquals(6, count($select->getOptionsAsArray()));
        $this->assertEquals('Yellow', $select->getSelected());
        $select->resetValue();
        $this->assertTrue(empty($select->getValue()));
    }

    public function testOptGroupAddOptions()
    {
        $optGroup = new Select\Optgroup();
        $optGroup->addOptions([
            new Select\Option('Red', 'Red')
        ]);
        $optGroup->addOption(new Select\Option('White', 'White'));
        $this->assertEquals(2, count($optGroup->getOptions()));
    }

    public function testOptGroupMultiple()
    {
        $select = new SelectMultiple('my_select', [
            'Colors 1' => ['Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'],
            'Colors 2' => ['Yellow' => 'Yellow', 'Green' => 'Green', 'Purple' => 'Purple']
        ], ['Red'], null, '    ');
        $select->setValue(['Yellow']);
        $this->assertEquals(6, count($select->getOptions()));
        $this->assertFalse($select->getOptions()[0]->isSelected());
        $this->assertEquals(6, count($select->getOptionsAsArray()));
        $this->assertEquals(['Yellow'], $select->getSelected());
        $select->resetValue();
        $this->assertTrue(empty($select->getValue()));
    }

    public function testSelected()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], 'Red');
        $this->assertEquals('Red', $select->getSelected());
    }

    public function testMultipleSelected()
    {
        $select = new SelectMultiple('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], ['Red', 'Blue']);
        $this->assertEquals(['Red', 'Blue'], $select->getSelected());
    }

    public function testValidate()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $select->addValidator(new Validator\NotEqual('Blue'));
        $select->addValidator(function($value){
            return 'This is wrong.';
        });

        $select->setValue('Blue');
        $this->assertFalse($select->validate());
        $this->assertEquals(2, count($select->getErrors()));
    }

    public function testValidateRequired()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $select->setRequired(true);
        $this->assertFalse($select->validate());
        $this->assertEquals(1, count($select->getErrors()));
    }

    public function testParseYear1()
    {
        $select = new Select('my_select', 'YEAR_1900_2000');
        $this->assertEquals(101, count($select->getOptions()));

        $select = new Select('my_select', 'YEAR_2000_1900');
        $this->assertEquals(101, count($select->getOptions()));
    }

    public function testParseYear2()
    {
        $select = new Select('my_select', 'YEAR_1900');
        $this->assertEquals(((date('Y') - 1900) + 1), count($select->getOptions()));

        $select = new Select('my_select', 'YEAR_' . (date('Y') + 5));
        $this->assertEquals(6, count($select->getOptions()));
    }

    public function testParseYear3()
    {
        $select = new Select('my_select', 'YEAR');
        $this->assertEquals(11, count($select->getOptions()));
    }

    public function testParseMonthsShort()
    {
        $select = new Select('my_select', 'MONTHS_SHORT');
        $this->assertEquals(12, count($select->getOptions()));
    }

    public function testParseDaysOfMonths()
    {
        $select = new Select('my_select', 'DAYS_OF_MONTH');
        $this->assertEquals(31, count($select->getOptions()));
    }

    public function testParseHours24()
    {
        $select = new Select('my_select', 'HOURS_24');
        $this->assertEquals(24, count($select->getOptions()));
    }

    public function testParseMinutes()
    {
        $select = new Select('my_select', 'MINUTES');
        $this->assertEquals(60, count($select->getOptions()));
    }

    public function testParseMinutes5()
    {
        $select = new Select('my_select', 'MINUTES_5');
        $this->assertEquals(12, count($select->getOptions()));
    }

    public function testParseMinutes10()
    {
        $select = new Select('my_select', 'MINUTES_10');
        $this->assertEquals(6, count($select->getOptions()));
    }

    public function testParseMinutes15()
    {
        $select = new Select('my_select', 'MINUTES_15');
        $this->assertEquals(4, count($select->getOptions()));
    }

    public function testParseData()
    {
        $select = new Select('my_select', 'MONTHS_LONG');
        $this->assertEquals(12, count($select->getOptions()));
    }

    public function testRemoveRequired()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $select->setRequired(false);
        $this->assertFalse($select->isRequired());
    }

    public function testDisabled()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $select->setDisabled(true);
        $this->assertTrue($select->isDisabled());
    }

    public function testRemoveDisabled()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $select->setDisabled(false);
        $this->assertFalse($select->isDisabled());
    }

    public function testReadonly()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], 'White');
        $select->setReadonly(true);
        $this->assertTrue($select->isReadonly());
    }

    public function testRemoveReadonly()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $select->setReadonly(false);
        $this->assertFalse($select->isReadonly());
    }

}