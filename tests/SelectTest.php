<?php

namespace Pop\Form\Test;

use Pop\Form\Element\Select;

class SelectTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $select->setAttribute('class', 'select-menu');
        $select->setAsMultiple(true);
        $select->setRequired(true);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
    }

    public function testMultiple()
    {
        $select = new Select('my_select[]', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $select->setAttribute('multiple', 'multiple');
        $select->setAsMultiple(false);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
    }

    public function testMarked()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], null, ['marked' => 'Red']);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
    }

    public function testMarkedMultiple()
    {
        $select = new Select('my_select[]', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], null, ['marked' => ['Red', 'Blue'], 'multiple' => true]);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
    }

    public function testOptGroup()
    {
        $select = new Select('my_select', [
            'Colors' => ['Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'],
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
    }

    public function testOptGroupMarked()
    {
        $select = new Select('my_select', [
            'Colors' => ['Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'],
        ], null, ['marked' => 'Red']);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
    }

    public function testOptGroupMarkedMultiple()
    {
        $select = new Select('my_select', [
            'Colors' => ['Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'],
        ], null, ['marked' => ['Red', 'Blue'], 'multiple' => true]);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
    }

    public function testParseYear1()
    {
        $select = new Select('my_select', 'YEAR_1900_2000');
        $this->assertEquals(101, count($select->getValue()));

        $select = new Select('my_select', 'YEAR_2000_1900');
        $this->assertEquals(101, count($select->getValue()));
    }

    public function testParseYear2()
    {
        $select = new Select('my_select', 'YEAR_1900');
        $this->assertEquals(((date('Y') - 1900) + 1), count($select->getValue()));

        $select = new Select('my_select', 'YEAR_' . (date('Y') + 5));
        $this->assertEquals(6, count($select->getValue()));
    }

    public function testParseYear3()
    {
        $select = new Select('my_select', 'YEAR');
        $this->assertEquals(11, count($select->getValue()));
    }

    public function testParseMonthsShort()
    {
        $select = new Select('my_select', 'MONTHS_SHORT');
        $this->assertEquals(12, count($select->getValue()));
    }

    public function testParseDaysOfMonths()
    {
        $select = new Select('my_select', 'DAYS_OF_MONTH');
        $this->assertEquals(31, count($select->getValue()));
    }

    public function testParseHours24()
    {
        $select = new Select('my_select', 'HOURS_24');
        $this->assertEquals(24, count($select->getValue()));
    }

    public function testParseMinutes()
    {
        $select = new Select('my_select', 'MINUTES');
        $this->assertEquals(60, count($select->getValue()));
    }

    public function testParseMinutes5()
    {
        $select = new Select('my_select', 'MINUTES_5');
        $this->assertEquals(12, count($select->getValue()));
    }

    public function testParseMinutes10()
    {
        $select = new Select('my_select', 'MINUTES_10');
        $this->assertEquals(6, count($select->getValue()));
    }

    public function testParseMinutes15()
    {
        $select = new Select('my_select', 'MINUTES_15');
        $this->assertEquals(4, count($select->getValue()));
    }

    public function testParseData()
    {
        $select = new Select('my_select', 'MONTHS_LONG', null, ['data' => true]);
        $this->assertEquals(12, count($select->getValue()));
    }

}