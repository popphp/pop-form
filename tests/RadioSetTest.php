<?php

namespace Pop\Form\Test;

use Pop\Form\Element\RadioSet;
use Pop\Validator;

class RadioSetTest extends \PHPUnit_Framework_TestCase
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
        $this->assertContains('Radio Legend', $radio->render());
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
}