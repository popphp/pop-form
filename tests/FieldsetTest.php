<?php

namespace Pop\Form\Test;

use Pop\Form\Fieldset;
use Pop\Form\Element;

class FieldsetTest extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals(2, count($fieldset->getAllFields()));
    }

}
