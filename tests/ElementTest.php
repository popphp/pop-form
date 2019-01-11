<?php

namespace Pop\Form\Test;

use Pop\Form\Element;
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase
{

    public function testConstructor()
    {
        $element = new Element\Input('my_input');
        $element->setRequired(true);
        $element->setDisabled(true);
        $element->setReadonly(true);
        $element->setErrorPre(true);
        $element->clearErrors();
        $this->assertInstanceOf('Pop\Form\Element\AbstractElement', $element);
        $this->assertTrue($element->isErrorPre());
        $this->assertEquals('my_input', $element->getName());
        $this->assertEquals('text', $element->getType());
        $this->assertTrue($element->isRequired());
        $this->assertTrue($element->isDisabled());
        $this->assertTrue($element->isReadonly());
        $this->assertEquals(0, count($element->getErrors()));
        $this->assertFalse($element->hasErrors());
    }

    public function testSetLabel()
    {
        $element = new Element\Input('my_input');
        $element->setLabel('my_label');
        $this->assertEquals('my_label', $element->getLabel());
    }

    public function testSetLabelWithAttributes()
    {
        $element = new Element\Input('my_input');
        $element->setLabel('my_label');
        $element->setLabelAttributes(['class' => 'my-label']);
        $this->assertEquals('my_label', $element->getLabel());
        $this->assertEquals(1, count($element->getLabelAttributes()));
    }

    public function testSetLabelAttributes()
    {
        $element = new Element\Input('my_input');
        $element->setLabelAttributes([
            'class' => 'my-label'
        ]);
        $this->assertEquals(1, count($element->getLabelAttributes()));
    }

    public function testSetHint()
    {
        $element = new Element\Input('my_input');
        $element->setHint('my_hint');
        $this->assertEquals('my_hint', $element->getHint());
    }

    public function testSetHintWithAttributes()
    {
        $element = new Element\Input('my_input');
        $element->setHint('my_hint');
        $element->setHintAttributes(['class' => 'my-hint']);
        $this->assertEquals('my_hint', $element->getHint());
        $this->assertEquals(1, count($element->getHintAttributes()));
    }

    public function testSetHintAttributes()
    {
        $element = new Element\Input('my_input');
        $element->setHintAttributes([
            'class' => 'my-hint'
        ]);
        $this->assertEquals(1, count($element->getHintAttributes()));
    }

    public function testSetValidators()
    {
        $element = new Element\Input('my_input');
        $element->setValidators([
            new \Pop\Validator\Email()
        ]);
        $this->assertEquals(1, count($element->getValidators()));
    }

    public function testAddValidator()
    {
        $element = new Element\Input('my_input');
        $element->addValidator(new \Pop\Validator\Email());
        $this->assertEquals(1, count($element->getValidators()));
    }

    public function testAddValidators()
    {
        $element = new Element\Input('my_input');
        $element->addValidators([
            new \Pop\Validator\Email(),
            new \Pop\Validator\NotEqual('bad@email.com')
        ]);
        $this->assertEquals(2, count($element->getValidators()));
    }

    public function testAddValidatorException()
    {
        $this->expectException('Pop\Form\Element\Exception');
        $element = new Element\Input('my_input');
        $element->addValidator('badvalidator');
    }

    public function testRender()
    {
        $element = new Element\Input('my_input');

        ob_start();
        echo $element;
        $result = ob_get_clean();

        $this->assertContains('<input', $result);
    }

    public function testValidate()
    {
        $element = new Element\Input('my_input');
        $element->addValidator(new \Pop\Validator\Email());
        $element->setValue('bademail');
        $this->assertFalse($element->validate());
    }

    public function testValidateRequired()
    {
        $element = new Element\Input('my_input');
        $element->setRequired(true);
        $this->assertFalse($element->validate());
    }

    public function testValidateNotEmpty()
    {
        $element = new Element\Input('my_input');
        $element->setErrorPre(true);
        $element->addValidator(new \Pop\Validator\NotEmpty());
        $this->assertFalse($element->validate());
        $this->assertEquals(1, count($element->getErrors()));
    }

    public function testValidateClosure()
    {
        $element = new Element\Input('my_input');
        $element->addValidator(function($value){
            return (null !== $value);
        });
        $this->assertFalse($element->validate());
        $this->assertEquals(1, count($element->getErrors()));
    }

}