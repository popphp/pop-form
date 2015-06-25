<?php

namespace Pop\Form\Test;

use Pop\Form\Element\Button;

class ButtonTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $button = new Button('my_button', 'Press This');
        $this->assertInstanceOf('Pop\Form\Element\Button', $button);
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