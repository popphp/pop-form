<?php

namespace Pop\Form\Test;

use Pop\Form\Fieldset;
use Pop\Form\Element;
use PHPUnit\Framework\TestCase;

class FieldsetTest extends TestCase
{

    public function testConstructor()
    {
        $username = new Element\Input('username');
        $email    = new Element\Input\Email('email');
        $submit   = new Element\Input\Submit('submit', 'SUBMIT');
        $fieldset = new Fieldset([$username, $email, $submit]);
        $this->assertInstanceOf('Pop\Form\Fieldset', $fieldset);
        $this->assertInstanceOf('Pop\Form\Element\AbstractElement', $fieldset->getField('username'));
    }

    public function testCreateFromConfig()
    {
        $fieldset = Fieldset::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'file' => [
                'type'  => 'file',
                'label' => 'File:'
            ]
        ]);
        $fieldset->setCurrent(1);
        $fieldset->setFieldValues(['username' => 'my_username']);
        $this->assertEquals(2, count($fieldset->getAllFields()));
        $this->assertEquals(1, $fieldset->getCurrent());
        $fieldset->createGroup();
        $this->assertEquals(2, $fieldset->getCurrent());
        $this->assertEquals(2, count($fieldset->getFields(0)));
        $this->assertEquals(3, count($fieldset->getFieldGroups()));
        $this->assertEquals('my_username', $fieldset->getFieldValue('username'));
        $this->assertEquals('my_username', $fieldset['username']);
        foreach ($fieldset as $field => $value) {
            $this->assertNotEmpty($field);
        }
        unset($fieldset['username']);
        $this->assertNull($fieldset->username);

    }

}
