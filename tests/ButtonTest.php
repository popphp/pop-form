<?php

namespace Pop\Form\Test;

use Pop\Form\Element\Button;
use PHPUnit\Framework\TestCase;

class ButtonTest extends TestCase
{

    public function testConstructor()
    {
        $button = new Button('my_button', 'Press This', '    ');
        $button->setValue('Press That');
        $this->assertInstanceOf('Pop\Form\Element\Button', $button);
        $this->assertEquals('Press That', $button->getValue());
        $this->assertEquals('button', $button->getType());
        $button->resetValue();
        $this->assertTrue(empty($button->getValue()));
        $this->assertTrue($button->validate());
    }

    public function testSubmit()
    {
        $button = new Button('submit', 'Press This');
        $this->assertInstanceOf('Pop\Form\Element\Button', $button);
    }

    public function testReset()
    {
        $button = new Button('reset', 'Press This');
        $this->assertInstanceOf('Pop\Form\Element\Button', $button);
    }

}