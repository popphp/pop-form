<?php

namespace Pop\Form\Test;

use Pop\Form\Element\CheckboxSet;
use Pop\Validator;
use PHPUnit\Framework\TestCase;

class CheckboxSetTest extends TestCase
{

    public function testConstructor()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], ['Red'], '    ');
        $checkbox->setCheckboxAttribute('tabindex', 10);
        $checkbox->setCheckboxAttributes(['class' => 'checkbox-class']);
        $checkbox->setChecked(['White']);
        $checkbox->setLegend('Checkbox Legend');
        $checkbox->setAttribute('class', 'checkbox-btn');
        $checkbox->setAttribute('tabindex', '1');
        $this->assertInstanceOf('Pop\Form\Element\CheckboxSet', $checkbox);
        $this->assertEquals(['White'], $checkbox->getChecked());
        $this->assertEquals('checkbox', $checkbox->getType());
        $this->assertEquals('Checkbox Legend', $checkbox->getLegend());
        $checkbox->resetValue();
        $this->assertTrue(empty($checkbox->getValue()));
        $this->assertStringContainsString('Checkbox Legend', $checkbox->render());
    }



    public function testValidate()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $checkbox->addValidator(new Validator\NotContains('Red'));
        $checkbox->addValidator(function($value){
            return 'This is wrong.';
        });

        $checkbox->setValue(['Red']);
        $this->assertFalse($checkbox->validate());
        $this->assertEquals(2, count($checkbox->getErrors()));
    }

    public function testValidateRequired()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $checkbox->setRequired(true);
        $this->assertFalse($checkbox->validate());
        $this->assertEquals(1, count($checkbox->getErrors()));
    }

    public function testDisabled()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $checkbox->setDisabled(true);
        $this->assertTrue($checkbox->isDisabled());
    }

    public function testRemoveDisabled()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $checkbox->setDisabled(false);
        $this->assertFalse($checkbox->isDisabled());
    }

    public function testReadonly()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $checkbox->setReadonly(true);
        $this->assertTrue($checkbox->isReadonly());
    }

    public function testRemoveReadonly()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $checkbox->setReadonly(false);
        $this->assertFalse($checkbox->isReadonly());
    }

    public function testSetTabIndex()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $checkbox->setCheckboxAttributes(['tabindex' => 1]);
        $this->assertEquals(3, $checkbox->getChild(4)->getAttribute('tabindex'));
    }

}