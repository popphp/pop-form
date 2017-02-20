<?php

namespace Pop\Form\Test;

use Pop\Form\Element\Select;
use Pop\Form\Element\SelectMultiple;

class SelectTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $select = new Select('my_select', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $select->setAttribute('class', 'select-menu');
        $select->setRequired(true);
        $this->assertInstanceOf('Pop\Form\Element\Select', $select);
    }

    public function testMultiple()
    {
        $select = new SelectMultiple('my_select[]', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\SelectMultiple', $select);
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

}