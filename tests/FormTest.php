<?php

namespace Pop\Form\Test;

use Pop\Form\Form;
use Pop\Filter\Filter;
use Pop\Form\Element;
use Pop\Validator;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{

    public function testConstructor()
    {
        $username = new Element\Input('username');
        $email    = new Element\Input\Email('email');
        $submit   = new Element\Input\Submit('submit', 'SUBMIT');
        $form = new Form([$username, $email, $submit], '/process', 'post');
        $form->username = 'admin';
        $this->assertInstanceOf('Pop\Form\Form', $form);
        $this->assertInstanceOf('Pop\Form\Element\AbstractElement', $form->getField('username'));
        $this->assertEquals('/process', $form->getAction());
        $this->assertEquals('post', $form->getMethod());
        $this->assertTrue(isset($form->username));
        $this->assertEquals('admin', $form->username);
        unset($form->username);
        $this->assertEmpty($form->username);
        $this->assertFalse($username->hasHintAttributes());
    }

    public function testSetAttributes()
    {
        $form = new Form();
        $form->setAttributes([
            'class' => 'form-class',
            'id'    => 'form-id'
        ]);
        $this->assertEquals('form-class', $form->getAttribute('class'));
        $this->assertEquals('form-id', $form->getAttribute('id'));
    }

    public function testSetCurrent()
    {
        $form = new Form();
        $form->setCurrent(0);
        $this->assertEquals(0, $form->getCurrent());
    }

    public function testCreateFromConfig1()
    {
        $form = Form::createFromConfig([
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
        $form->addFieldFromConfig('submit', [
            'type'  => 'submit',
            'value' => 'SUBMIT'
        ]);
        $this->assertInstanceOf('Pop\Form\Form', $form);
        $this->assertEquals(3, count($form->getFields()));
    }

    public function testCreateFromConfig2()
    {
        $form = new Form();
        $form->addFieldsFromConfig([
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
        $this->assertEquals(3, count($form->getFields()));
    }

    public function testCreateFromFieldsetConfig1()
    {
        $form = Form::createFromFieldsetConfig([
            [
                'username' => [
                    'type'             => 'text',
                    'label'            => 'Username:',
                    'required'         => true,
                    'required_message' => 'Hey! The username field is required!',
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
        $this->assertEquals(3, count($form->getFields()));
        $this->assertTrue($form->getField('username')->hasRequiredMessage());
        $this->assertEquals('Hey! The username field is required!', $form->getField('username')->getRequiredMessage());
    }

    public function testCreateFromFieldsetConfig2()
    {
        $form = new Form();
        $form->addFieldsetsFromConfig([
            'Top of Form' => [
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
            'Bottom of Form' => [
                'submit' => [
                    'type'  => 'submit',
                    'value' => 'SUBMIT'

                ]
            ]
        ]);
        $this->assertInstanceOf('Pop\Form\Form', $form);
        $this->assertEquals(3, count($form->getFields()));
    }

    public function testCreateAndRemoveFieldset()
    {
        $form = new Form();
        $form->createFieldset('Fieldset 1', 'table');
        $this->assertEquals('Fieldset 1', $form->getFieldset()->getLegend());
        $form->setLegend('Fieldset 1 Rev');
        $this->assertEquals('Fieldset 1 Rev', $form->getLegend());
        $this->assertEquals('table', $form->getFieldset()->getContainer());
        $form->createFieldset('Fieldset 2');
        $form->removeFieldset(1);
        $form->setCurrent(0);
        $this->assertEquals(0, $form->getCurrent());
    }

    public function testGetFieldsets()
    {
        $form = new Form();
        $form->createFieldset('Fieldset 1', 'table');
        $form->createFieldset('Fieldset 2', 'table');
        $this->assertTrue($form->hasFieldsets());
        $this->assertEquals(2, count($form->getFieldsets()));
    }

    public function testRemoveField()
    {
        $form = Form::createFromConfig([
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
        $this->assertTrue($form->hasFields());
        $this->assertTrue($form->hasField('username'));
        $form->removeField('username');
        $this->assertFalse($form->hasField('username'));
    }

    public function testFilterValue()
    {
        $form = new Form();
        $form->addFilter(new Filter('strip_tags'));
        $this->assertEquals('admin', $form->filterValue('<b>admin</b>'));

    }

    public function testInsertAfter()
    {
        $username = new Element\Input('username');
        $submit   = new Element\Input\Submit('submit', 'SUBMIT');
        $form = new Form([$username, $submit]);
        $form->insertFieldAfter('username', new Element\Input\Email('email'));
        $this->assertEquals(3, count($form->getFields()));
    }

    public function testInsertBefore()
    {
        $username = new Element\Input('username');
        $submit   = new Element\Input\Submit('submit', 'SUBMIT');
        $form = new Form([$username, $submit]);
        $form->insertFieldBefore('submit', new Element\Input\Email('email'));
        $this->assertEquals(3, $form->count());
    }

    public function testToArray()
    {
        $form = Form::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'email' => [
                'type'  => 'email',
                'label' => 'Email:'
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $this->assertEquals(3, count($form->toArray()));
    }

    public function testIterator()
    {
        $form = Form::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'email' => [
                'type'  => 'email',
                'label' => 'Email:'
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $i = 0;
        foreach ($form as $key => $value) {
            $i++;
        }

        $this->assertEquals(3, $i);
    }

    public function testAddFilter()
    {
        $form = Form::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'email' => [
                'type'  => 'email',
                'label' => 'Email:'
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $form->addFilter(new Filter('htmlentities', [ENT_QUOTES, 'UTF-8']));
        $form->addFilter(new Filter('strip_tags', null, 'email', 'email'));
        $form->setFieldValues(['username' => '<h1>admin</h1>', 'email' => 'admin@admin.com']);
        $this->assertEquals('&lt;h1&gt;admin&lt;/h1&gt;', $form->username);
        $form->clearFilters();
    }

    public function testAddColumn1()
    {
        $form = Form::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'email' => [
                'type'  => 'email',
                'label' => 'Email:'
            ],
            'upload' => [
                'type' => 'file',
                'label' => 'Upload File'
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $form->addColumn([1, 2], 'left-column');
        $form->addColumn([3, 4],'right-column');
        $this->assertTrue($form->hasColumn('left-column'));
        $this->assertEquals(2, count($form->getColumn('left-column')));
        $form->removeColumn('right-column');
        $this->assertFalse($form->hasColumn('right-column'));
        $this->assertStringContainsString('left-column', $form->render());
        $this->assertStringContainsString('enctype="multipart/form-data"', $form->render());
    }

    public function testAddColumn2()
    {
        $form = Form::createFromConfig([
            [
                'username' => [
                    'type'     => 'text',
                    'label'    => 'Username:',
                    'required' => true
                ]
            ],
            [
                'email' => [
                    'type'  => 'email',
                    'label' => 'Email:'
                ]
            ],
            [
                'submit' => [
                    'type'  => 'submit',
                    'value' => 'SUBMIT'
                ]
            ]
        ]);
        $form->addColumn([1, 2]);
        $form->addColumn(3);
        $this->assertTrue($form->hasColumn(1));
        $this->assertEquals(2, count($form->getColumn(1)));
        $form->removeColumn(2);
        $this->assertFalse($form->hasColumn(2));
    }

    public function testIsValid()
    {
        $form = Form::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'email' => [
                'type'  => 'email',
                'label' => 'Email:'
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $form->setFieldValues(['email' => 'admin@admin.com']);
        $this->assertFalse($form->isValid());
        $this->assertEquals(1, count($form->getErrors('username')));
        $this->assertEquals(1, count($form->getAllErrors()));
        $form->reset();
    }


    public function testValidateCallable()
    {
        $form = Form::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'password' => [
                'type'  => 'password',
                'label' => 'Password:',
                'validators' => function($value, array $formValues = []) {
                    if (!empty($formValues['username'])) {
                        return new Validator\LengthGte(8);
                    }
                    return null;
                }
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $form->setFieldValues(['username' => 'admin', 'password' => 1234]);
        $this->assertFalse($form->isValid());
        $this->assertEquals(1, count($form->getErrors('password')));
        $this->assertTrue($form->getField('password')->hasValidators());
    }

    public function testValidateCallables()
    {
        $form = Form::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'password' => [
                'type'  => 'password',
                'label' => 'Password:',
                'validators' =>                 function($value, array $formValues = []) {
                    if (!empty($formValues['username'])) {
                        return [
                            new Validator\LengthGte(8),
                            new Validator\AlphaNumeric()
                        ];
                    }
                    return null;
                }
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $form->setFieldValues(['username' => 'admin', 'password' => '!@#']);
        $this->assertFalse($form->isValid());
        $this->assertEquals(2, count($form->getErrors('password')));
    }

    public function testToString()
    {
        $form = Form::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'email' => [
                'type'  => 'email',
                'label' => 'Email:'
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        ob_start();
        echo $form;
        $result = ob_get_clean();

        $this->assertStringContainsString('<form', $result);
    }

    #[runInSeparateProcess]
    public function testClear()
    {
        $form = Form::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'email' => [
                'type'  => 'email',
                'label' => 'Email:'
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $form->clearTokens();
        $this->assertInstanceOf('Pop\Form\Form', $form);
    }

}
