<?php

namespace Pop\Form\Test;

use Pop\Form\Element;

class ElementTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $element = new Element\Input('my_input');
        $element->setRequired(true);
        $element->setErrorPre(true);
        $element->setErrorPost(true);
        $element->setErrorDisplay('div', ['class' => 'error']);
        $element->clearErrors();
        $this->assertInstanceOf('Pop\Form\Element\AbstractElement', $element);
        $this->assertEquals('div', $element->getErrorDisplay()['container']);
        $this->assertEquals('my_input', $element->getName());
        $this->assertEquals('input', $element->getType());
        $this->assertNull($element->getMarked());
        $this->assertTrue($element->isRequired());
        $this->assertEquals(0, count($element->getErrors()));
        $this->assertFalse($element->hasErrors());
    }

    public function testSetLabel()
    {
        $element = new Element\Input('my_input');
        $element->setLabel('my_label');
        $element->setErrorDisplay('div');
        $this->assertEquals('my_label', $element->getLabel());
    }

    public function testSetLabelWithAttributes()
    {
        $element = new Element\Input('my_input');
        $element->setLabel(['my_label' => ['class' => 'my-label']]);
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
        $this->setExpectedException('Pop\Form\Element\Exception');
        $element = new Element\Input('my_input');
        $element->addValidator('badvalidator');
    }

    public function testRender()
    {
        $element = new Element\Input('my_input');

        ob_start();
        $element->render();
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
        $this->assertContains('class="error"', $element->render(true));
    }

    public function testValidateClosure()
    {
        $element = new Element\Input('my_input');
        $element->addValidator(function($value){
            return (null !== $value);
        });
        $this->assertFalse($element->validate());
        $this->assertContains('class="error"', $element->render(true));
    }

}