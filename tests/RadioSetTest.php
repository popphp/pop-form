<?php

namespace Pop\Form\Test;

use Pop\Form\Element\RadioSet;
use Pop\Validator;
use PHPUnit\Framework\TestCase;

class RadioSetTest extends TestCase
{

    public function testConstructor()
    {
        $radio = new RadioSet('my_radio', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], 'Red', '    ');
        $radio->setRadioAttribute('tabindex', 10);
        $radio->setRadioAttributes(['class' => 'radio-class']);
        $radio->setChecked('White');
        $radio->setLegend('Radio Legend');
        $radio->setAttribute('class', 'radio-btn');
        $radio->setAttribute('tabindex', '1');
        $this->assertInstanceOf('Pop\Form\Element\RadioSet', $radio);
        $this->assertEquals('White', $radio->getChecked());
        $this->assertEquals('radio', $radio->getType());
        $this->assertEquals('Radio Legend', $radio->getLegend());
        $radio->resetValue();
        $this->assertTrue(empty($radio->getValue()));
        $this->assertStringContainsString('Radio Legend', $radio->render());
    }



    public function testValidate()
    {
        $radio = new RadioSet('my_radio', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $radio->addValidator(new Validator\NotEqual('Red'));
        $radio->addValidator(function($value){
            return 'This is wrong.';
        });

        $radio->setValue('Red');
        $this->assertFalse($radio->validate());
        $this->assertEquals(2, count($radio->getErrors()));
    }

    public function testValidateRequired()
    {
        $radio = new RadioSet('my_radio', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $radio->setRequired(true);
        $this->assertFalse($radio->validate());
        $this->assertEquals(1, count($radio->getErrors()));
    }

    public function testDisabled()
    {
        $radio = new RadioSet('my_radio', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $radio->setDisabled(true);
        $this->assertTrue($radio->isDisabled());
    }

    public function testRemoveDisabled()
    {
        $radio = new RadioSet('my_radio', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $radio->setDisabled(false);
        $this->assertFalse($radio->isDisabled());
    }

    public function testReadonly()
    {
        $radio = new RadioSet('my_radio', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $radio->setReadonly(true);
        $this->assertTrue($radio->isReadonly());
    }

    public function testRemoveReadonly()
    {
        $radio = new RadioSet('my_radio', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $radio->setReadonly(false);
        $this->assertFalse($radio->isReadonly());
    }

    public function testSetTabIndex()
    {
        $radio = new RadioSet('my_radio', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $radio->setRadioAttributes(['tabindex' => 1]);
        $this->assertEquals(3, $radio->getChild(4)->getAttribute('tabindex'));
    }

}