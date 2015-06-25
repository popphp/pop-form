<?php

namespace Pop\Form\Test;

use Pop\Form\Form;
use Pop\Form\Element;

class FormTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'file' => [
                'type'  => 'file',
                'label' => 'File:'
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

    public function testFieldGroupConfig()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            [
                'username' => [
                    'type'     => 'text',
                    'label'    => 'Username:',
                    'required' => true
                ],
                'file' => [
                    'type'  => 'file',
                    'label' => 'File:'
                ]
            ],
            [
                'submit' => [
                    'type'  => 'submit',
                    'value' => 'SUBMIT'
                ]
            ]
        ]);
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

    public function testAddFieldConfig()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]);
        $form->addFieldConfig('submit', [
            'type'  => 'submit',
            'value' => 'SUBMIT'
        ]);
        $form->addFieldConfig('username', [
            'type'     => 'text',
            'label'    => 'Username:',
            'required' => true
        ]);
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

    public function testAddMultipleFieldConfig()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]);
        $form->addFieldConfigs([
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ],
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]);
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

    public function testInsertFieldConfigBefore()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]);
        $form->insertFieldConfigBefore('username', 'file', [
            'type'  => 'file',
            'label' => 'File:'
        ]);
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

    public function testInsertFieldConfigBeforeException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]);
        $form->insertFieldConfigBefore('badfield', 'file', [
            'type'  => 'file',
            'label' => 'File:'
        ]);
    }

    public function testInsertFieldConfigAfter()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]);
        $form->insertFieldConfigAfter('username', 'file', [
            'type'  => 'file',
            'label' => 'File:'
        ]);
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

    public function testInsertFieldConfigAfterException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]);
        $form->insertFieldConfigAfter('badfield', 'file', [
            'type'  => 'file',
            'label' => 'File:'
        ]);
    }

    public function testInsertGroupConfigBefore()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([[
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]]);
        $form->insertGroupConfigBefore(0, [[ 'file' => [
            'type'  => 'file',
            'label' => 'File:'
        ]]]);
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

    public function testInsertGroupConfigBeforeException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([[
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]]);
        $form->insertGroupConfigBefore(5, [[ 'file' => [
            'type'  => 'file',
            'label' => 'File:'
        ]]]);
    }

    public function testInsertGroupConfigAfter()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([[
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]]);
        $form->insertGroupConfigAfter(0, [[ 'file' => [
            'type'  => 'file',
            'label' => 'File:'
        ]]]);
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

    public function testInsertGroupConfigAfterException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([[
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]]);
        $form->insertGroupConfigAfter(5, [[ 'file' => [
            'type'  => 'file',
            'label' => 'File:'
        ]]]);
    }

    public function testSetFieldValues()
    {

        $fields = [
            'username' => [
                'type'       => 'text',
                'label'      => 'Username:'
            ],
            'email' => [
                'type'       => 'text',
                'label'      => 'Email:'
            ],
            'cars' => [
                'type'  => 'radio',
                'label' => 'Cars',
                'value' => [
                    'Chevy'   => 'Chevy',
                    'Ford' => 'Ford'
                ],
                'marked' => 'Chevy'
            ],
            'fruit' => [
                'type'  => 'select',
                'label' => 'Fruit',
                'value' => [
                    'Apples'   => 'Apples',
                    'Oranges' => 'Oranges'
                ],
                'marked' => 'Oranges'
            ],
            'colors' => [
                'type'  => 'radio',
                'label' => 'Colors',
                'value' => [
                    'Red'   => 'Red',
                    'Green' => 'Green',
                    'Blue'  => 'Blue'
                ],
                'marked' => 'Red'
            ],
            'comments' => [
                'type'       => 'textarea',
                'label'      => 'Comments:'
            ],
            'submit' => [
                'type'       => 'submit',
                'value'      => 'SUBMIT'
            ]
        ];

        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form($fields);
        $form->setErrorDisplay('div', ['class' => 'error'], true);
        $form->setFieldValues([
            'username' => 'my_username',
            'email'    => 'test@test.com',
            'cars'     => 'Ford',
            'fruit'    => 'Apples',
            'colors'   => ['Green', 'Blue']
        ]);
        $this->assertEquals('my_username', $form->username);
        $this->assertEquals('test@test.com', $form->email);
    }

    public function testInsertElementBefore()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]);
        $form->insertElementBefore('username', new Element\Input('my_input'));
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

    public function testInsertElementBeforeException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->insertElementBefore('username', new Element\Input('my_input'));
    }

    public function testInsertElementAfter()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ]
        ]);
        $form->insertElementAfter('username', new Element\Input('my_input'));
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

    public function testInsertElementAfterException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->insertElementAfter('username', new Element\Input('my_input'));
    }

    public function testAddElements()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->addElements([
            new Element\CheckboxSet('my_checkbox', ['Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'])
        ]);
        $this->assertEquals(1, count($form->elements()));
    }

    public function testAddElementsException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->addElements(['badelement']);
    }

    public function testGetElement()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->addElements([
            new Element\CheckboxSet('my_checkbox', ['Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'])
        ]);
        $this->assertInstanceOf('Pop\Form\Element\CheckboxSet', $form->element('my_checkbox'));
    }

    public function testGetElementIndex()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->addElements([
            new Element\CheckboxSet('my_checkbox', ['Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'])
        ]);
        $this->assertEquals(0, $form->getElementIndex('my_checkbox'));
    }

    public function testRemoveElement()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->addElements([
            new Element\Input('my_input'),
            new Element\CheckboxSet('my_checkbox', ['Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'])
        ]);
        $form->removeElement('my_checkbox');
        $form->removeElement('my_input');
        $this->assertEquals(0, count($form->elements()));
    }

}
