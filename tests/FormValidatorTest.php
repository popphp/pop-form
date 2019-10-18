<?php

namespace Pop\Form\Test;

use Pop\Form\Filter\Filter;
use Pop\Form\FormValidator;
use Pop\Validator;
use PHPUnit\Framework\TestCase;

class FormValidatorTest extends TestCase
{

    public function testConstructor()
    {
        $validators = [
            'username' => [
                new Validator\LengthGte(6),
                new Validator\NotContains(['$', '?'])
            ],
            'password' => new Validator\LengthGte(8),
            'email'    => new Validator\Email()
        ];
        $required = ['username', 'password'];
        $values   = [
            'username' => 'admin',
            'password' => 'password',
            'email'    => 'test@test.com'
        ];
        $formValidator = new FormValidator($validators, $required, $values);
        $this->assertInstanceOf('Pop\Form\FormValidator', $formValidator);
        $this->assertTrue($formValidator->hasValidators());
        $this->assertTrue($formValidator->isRequired('username'));
        $this->assertEquals($values, $formValidator->getValues());
    }

    public function testGettersAndSetters()
    {
        $validators = [
            'username' => [
                new Validator\LengthGte(6),
                new Validator\NotContains(['$', '?'])
            ],
            'password' => new Validator\LengthGte(8),
            'email'    => new Validator\Email()
        ];
        $required = ['username', 'password'];
        $values   = [
            'username' => 'admin',
            'password' => 'password',
            'email'    => 'test@test.com'
        ];
        $formValidator = new FormValidator($validators, $required, $values);

        $formValidator->setRequired('email');
        $this->assertTrue($formValidator->isRequired('email'));
        $formValidator->removeRequired('email');
        $this->assertFalse($formValidator->isRequired('email'));

        $this->assertEquals(3, count($formValidator->getValidators()));
        $this->assertEquals(2, count($formValidator->getValidators('username')));
        $this->assertNull($formValidator->getValidators('foo'));
        $this->assertTrue($formValidator->hasValidators('username'));
        $this->assertFalse($formValidator->hasValidators('foo'));
        $this->assertTrue($formValidator->hasValidator('username', 0));
        $this->assertInstanceOf('Pop\Validator\LengthGte', $formValidator->getValidator('username', 0));

        $this->assertTrue($formValidator->hasValidator('username', 1));
        $formValidator->removeValidator('username', 1);
        $this->assertFalse($formValidator->hasValidator('username', 1));
        $this->assertTrue($formValidator->hasValidators('password'));
        $formValidator->removeValidators('password');
        $this->assertFalse($formValidator->hasValidators('password'));
        $this->assertTrue($formValidator->hasValidators());
        $formValidator->removeValidators();
        $this->assertFalse($formValidator->hasValidators());
    }

    public function testFilters()
    {
        $validators = [
            'username' => [
                new Validator\LengthGte(6),
                new Validator\NotContains(['$', '?'])
            ],
            'password' => new Validator\LengthGte(8),
            'email'    => new Validator\Email()
        ];
        $required = ['username', 'password'];
        $values   = [
            'username' => '<b>admin</b>',
            'password' => '<i>password</i>',
            'email'    => '<strong>test@test.com</strong>'
        ];

        $filter  = new Filter('strip_tags');
        $filters = [
            new Filter('strip_tags'),
            new Filter('htmlentities')
        ];

        $formValidator = new FormValidator($validators, $required, $values, $filter);
        $formValidator = new FormValidator($validators, $required, $values, $filters);
        $this->assertEquals('<b>admin</b>', $formValidator->username);
        $formValidator->filterValues();
        $this->assertEquals('admin', $formValidator['username']);
    }

    public function testFilterValues()
    {
        $validators = [
            'username' => [
                new Validator\LengthGte(6),
                new Validator\NotContains(['$', '?'])
            ],
            'password' => new Validator\LengthGte(8),
            'email'    => new Validator\Email()
        ];
        $required = ['username', 'password'];
        $values   = [
            'username' => '<b>admin</b>',
            'password' => '<i>password</i>',
            'email'    => '<strong>test@test.com</strong>'
        ];

        $filter  = new Filter('strip_tags');
        $filters = [
            new Filter('strip_tags'),
            new Filter('htmlentities')
        ];

        $formValidator = new FormValidator($validators, $required, $values, $filter);
        $formValidator = new FormValidator($validators, $required, $values, $filters);
        $this->assertEquals('<b>admin</b>', $formValidator->username);
        $formValidator->filterValues([
            'username' => '<b>admin</b>',
            'password' => '<i>password</i>',
            'email'    => '<strong>test@test.com</strong>'
        ]);
        $this->assertEquals('admin', $formValidator['username']);
    }

    public function testFilterValueException()
    {
        $this->expectException('Pop\Form\Exception');
        $formValidator = new FormValidator();
        $formValidator->filterValue('username');
    }

    public function testValidate()
    {
        $validators = [
            'username' => [
                new Validator\LengthGte(6),
                new Validator\NotContains(['$', '?'])
            ],
            'password' => new Validator\LengthGte(8),
            'email'    => function($value) {
                if ($value != 'test@test.com') {
                    return 'The email must be test@test.com';
                }
            }
        ];
        $required = ['username', 'password', 'last_name'];
        $values   = [
            'username' => 'adin$',
            'password' => 'password',
            'email'    => 'testtest.com'
        ];
        $formValidator = new FormValidator($validators, $required, $values);
        $formValidator->validate();
        $this->assertTrue($formValidator->hasErrors());
        $this->assertTrue($formValidator->hasErrors('email'));
        $this->assertEquals(3, count($formValidator->getErrors()));
        $this->assertEquals(1, count($formValidator->getErrors('email')));
        $this->assertEquals('The email must be test@test.com', $formValidator->getError('email', 0));
    }

    public function testMagicMethods()
    {
        $formValidator = new FormValidator();
        $formValidator->username   = 'admin';
        $formValidator['password'] = 'password';
        $this->assertTrue(isset($formValidator->username));
        $this->assertTrue(isset($formValidator['password']));
        $this->assertEquals(2, $formValidator->count());
        $this->assertEquals(2, count($formValidator));
        $this->assertEquals(2, count($formValidator->toArray()));
        unset($formValidator->username);
        unset($formValidator['password']);
        $this->assertEquals(0, $formValidator->count());
        $this->assertEquals(0, count($formValidator));

    }

}
