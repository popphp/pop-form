<?php

namespace Pop\Form\Test;

use Pop\Form\Element\Textarea;

class TextareaTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $textarea = new Textarea('my_textarea', 'Type something');
        $textarea->setRequired(true);
        $this->assertInstanceOf('Pop\Form\Element\Textarea', $textarea);
    }

}