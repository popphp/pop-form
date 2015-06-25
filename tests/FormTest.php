<?php

namespace Pop\Form\Test;

use Pop\Form\Form;

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

}
