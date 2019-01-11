<?php

namespace Pop\Form\Test;

use Pop\Form\Element\Textarea;
use Pop\Validator;
use PHPUnit\Framework\TestCase;

class TextareaTest extends TestCase
{

    public function testConstructor()
    {
        $textarea = new Textarea('my_textarea', 'Type something', '    ');
        $textarea->setRequired(true);
        $textarea->setValue('Something...');
        $this->assertInstanceOf('Pop\Form\Element\Textarea', $textarea);
        $this->assertEquals('textarea', $textarea->getType());
        $this->assertEquals('Something...', $textarea->getValue());
        $textarea->resetValue();
        $this->assertTrue(empty($textarea->getValue()));
    }

    public function testValidate()
    {
        $textarea = new Textarea('my_textarea', 'Type something', '    ');
        $textarea->addValidator(new Validator\NotEqual('Foo'));
        $textarea->addValidator(function($value){
            return 'This is wrong.';
        });

        $textarea->setValue('Foo');
        $this->assertFalse($textarea->validate());
        $this->assertEquals(2, count($textarea->getErrors()));
    }

    public function testValidateRequired()
    {
        $textarea = new Textarea('my_textarea');
        $textarea->setRequired(true);
        $this->assertFalse($textarea->validate());
        $this->assertEquals(1, count($textarea->getErrors()));
    }

}