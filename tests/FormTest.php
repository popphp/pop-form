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

    public function testActionAndMethod()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->setAction('/my-process')
             ->setMethod(('post'));
        $this->assertEquals('/my-process', $form->getAction());
        $this->assertEquals('post', $form->getMethod());
    }

    public function testMagicMethods()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->username = 'my_username';
        $this->assertEquals('my_username', $form->username);
        $this->assertTrue(isset($form->username));
        unset($form->username);
        $this->assertFalse(isset($form->username));
    }

    public function testOffsetMethods()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form['username'] = 'my_username';
        $this->assertEquals('my_username', $form['username']);
        $this->assertTrue(isset($form['username']));
        unset($form['username']);
        $this->assertFalse(isset($form['username']));
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

    public function testRemoveElementFromGroup()
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
        $form->setFieldValues([
            'username' => 'my_username'
        ]);
        $form->removeElement('file');
        $this->assertEquals(2, count($form->elements()));
    }

    public function testIsValid()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            [
                'username' => [
                    'type'     => 'text',
                    'label'    => 'Username:',
                    'required' => true
                ]
            ],
            [
                'submit' => [
                    'type'  => 'submit',
                    'value' => 'SUBMIT'
                ]
            ]
        ]);

        $this->assertFalse($form->isValid());
        $this->assertTrue($form->hasErrors());
        $this->assertEquals(1, count($form->getErrors()));
        $this->assertEquals(1, count($form->getErrors('username')));
    }

    public function testRenderForm()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            [
                'username' => [
                    'type'     => 'text',
                    'label'    => 'Username:',
                    'required' => true
                ],
                'password' => [
                    'type'     => 'password',
                    'label'    => 'Password:',
                    'required' => true
                ],
                'file' => [
                    'type'  => 'file',
                    'label' => 'File:'
                ],
                'colors' => [
                    'type' => 'checkbox',
                    'label' => 'Colors',
                    'value' => [
                        'Red'   => 'Red',
                        'White' => 'White',
                        'Blue'  => 'Blue'
                    ]
                ]
            ],
            [
                'submit' => [
                    'type'  => 'submit',
                    'value' => 'SUBMIT'
                ]
            ]
        ]);

        $form->getElement('colors')->setLabelAttributes([
            'class' => 'label-class'
        ]);

        ob_start();
        $form->renderForm();
        $result = ob_get_clean();
        $string = (string)$form;

        $this->assertTrue($form->hasFile());
        $this->assertContains('<form', $result);
        $this->assertContains('<form', $form->renderForm(true));
        $this->assertContains('<form', $string);
        $this->assertContains('action="/process"', $result);
        $this->assertContains('action="/process"', $form->renderForm(true));
        $this->assertContains('action="/process"', $string);
        $this->assertContains('id="username"', $result);
        $this->assertContains('id="username"', $form->renderForm(true));
        $this->assertContains('id="username"', $string);
        $this->assertContains('enctype="multipart/form-data"', $result);
        $this->assertContains('enctype="multipart/form-data"', $form->renderForm(true));
        $this->assertContains('enctype="multipart/form-data"', $string);
    }

    public function testRenderFormNoElementsException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $_SERVER['REQUEST_URI'] = '/process';
        $form   = new Form();
        $result = $form->renderForm(true);
    }

    public function testAddFilter()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            [
                'username' => [
                    'type' => 'text',
                    'label' => 'Username:',
                    'required' => true,
                    'value' => 'my<script></script>"username"'
                ]
            ],
            [
                'submit' => [
                    'type' => 'submit',
                    'value' => 'SUBMIT'
                ]
            ]
        ]);

        $form->addFilter('strip_tags');
        $form->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8']);
        $form->filter();
        $this->assertEquals('my&quot;username&quot;', $form->username);
    }

    public function testAddFilters()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            [
                'username' => [
                    'type' => 'text',
                    'label' => 'Username:',
                    'required' => true,
                    'value' => 'my<script></script>"username"'
                ],
                'colors' => [
                    'type' => 'checkbox',
                    'label' => 'Colors',
                    'value' => [
                        'Red'   => 'Red',
                        'White' => 'White',
                        'Blue'  => 'Blue'
                    ],
                    'marked' => [
                        'Red', 'White'
                    ]
                ]
            ],
            [
                'submit' => [
                    'type' => 'submit',
                    'value' => 'SUBMIT'
                ]
            ]
        ]);

        $form->addFilters([
            ['call' => 'strip_tags'],
            ['call' => 'htmlentities', 'params' => [ENT_QUOTES, 'UTF-8']]
        ]);
        $form->filter();
        $this->assertEquals('my&quot;username&quot;', $form->username);
    }

    public function testAddFiltersNoCallException()
    {
        $this->setExpectedException('Pop\Form\Exception');
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            [
                'username' => [
                    'type' => 'text',
                    'label' => 'Username:',
                    'required' => true,
                    'value' => 'my<script></script>"username"'
                ]
            ],
            [
                'submit' => [
                    'type' => 'submit',
                    'value' => 'SUBMIT'
                ]
            ]
        ]);

        $form->addFilters([
            ['strip_tags']
        ]);
    }

    public function testClearFilters()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            [
                'username' => [
                    'type' => 'text',
                    'label' => 'Username:',
                    'required' => true,
                    'value' => 'my<script></script>"username"'
                ]
            ],
            [
                'submit' => [
                    'type' => 'submit',
                    'value' => 'SUBMIT'
                ]
            ]
        ]);

        $form->addFilters([
            ['call' => 'strip_tags'],
            ['call' => 'htmlentities', 'params' => [ENT_QUOTES, 'UTF-8']]
        ]);
        $form->clearFilters();
        $form->filter();
        $this->assertEquals('my<script></script>"username"', $form->username);
        $this->assertEquals(2, count($form->getFields()));
        $this->assertEquals('Username:', $form->getFieldConfig('username')['label']);
        $this->assertEquals(2, count($form->getFieldConfig()));
        $this->assertTrue($form->hasFieldGroupConfig());
        $this->assertEquals(2, count($form->getFieldGroupConfig()));
    }

    /**
     * @runInSeparateProcess
     */
    public function testClear()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->clear();
        $_SESSION['pop_csrf']    = 'test';
        $_SESSION['pop_captcha'] = 'test';
        $form->clear();
        $this->assertFalse(isset($_SESSION['pop_csrf']));
        $this->assertFalse(isset($_SESSION['pop_captcha']));
    }

}
