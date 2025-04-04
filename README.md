pop-form
========

[![Build Status](https://github.com/popphp/pop-form/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-form/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-form)](http://cc.popphp.org/pop-form/)

[![Join the chat at https://discord.gg/TZjgT74U7E](https://media.popphp.org/img/discord.svg)](https://discord.gg/TZjgT74U7E)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Field Elements](#field-elements)
* [Field Configurations](#field-configurations)
    - [Fieldsets](#fieldsets)
    - [Legends](#legends) 
* [Field Containers](#field-containers)
* [Filtering](#filtering)
* [Validation](#validation)
* [Dynamic Fields](#dynamic-fields)
* [ACL Forms](#acl-forms)

Overview
--------
`pop-form` is a robust component for managing, rendering and validating HTML forms.
With it, you can have complete control over how a form looks and functions as well
as granular control over field validation. Features include:

* Field element creation and configuration
* Validation
    + Use any callable validation object, such as `pop-validator` or custom validators
* Filtering
* Dynamic field generation based on the fields of a database table

`pop-form`is a component of the [Pop PHP Framework](https://www.popphp.org/).

[Top](#pop-form)

Install
-------

Install `pop-form` using Composer.

    composer require popphp/pop-form

Or, require it in your composer.json file

    "require": {
        "popphp/pop-form" : "^4.2.2"
    }

[Top](#pop-form)

Quickstart
----------

The most basic way to wire up a form object is through a simple configuration.

```php
use Pop\Form\Form;

$fields = [
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
];

$form = Form::createFromConfig($fields);

if ($_POST) {
    $form->setFieldValues($_POST);
    if (!$form->isValid()) {
        echo $form; // Has errors
    } else {
        echo 'Valid!';
    }
} else {
    echo $form;
}
```

The form rendered will look like:

```html
<form action="#" method="post" id="pop-form" class="pop-form">
    <fieldset id="pop-form-fieldset-1" class="pop-form-fieldset">
        <dl>
            <dt>
                <label for="username" class="required">Username:</label>
            </dt>
            <dd>
                <input type="text" name="username" id="username" value="" required="required" />
            </dd>
            <dt>
                <label for="email">Email:</label>
            </dt>
            <dd>
                <input type="email" name="email" id="email" value="" />
            </dd>
            <dd>
                <input type="submit" name="submit" id="submit" value="SUBMIT" />
            </dd>
        </dl>
    </fieldset>
</form>
```

Upon submit, if the form values do not pass validation, the form will re-render with the errors
(note the error `div` under the username field):

```html
<form action="/" method="post" id="pop-form" class="pop-form">
    <fieldset id="pop-form-fieldset-1" class="pop-form-fieldset">
        <dl>
            <dt>
                <label for="username" class="required">Username:</label>
            </dt>
            <dd>
                <input type="text" name="username" id="username" value="" required="required" />
                <div class="error">This field is required.</div>
            </dd>
            <dt>
                <label for="email">Email:</label>
            </dt>
            <dd>
                <input type="email" name="email" id="email" value="test@test.com" />
            </dd>
            <dd>
                <input type="submit" name="submit" id="submit" value="SUBMIT" />
            </dd>
        </dl>
    </fieldset>
</form>
```

The form object will default to `POST` as the method and the current `REQUEST_URI`
as the action, but those values can be changed in a number of ways:

```php
$form = new Form($fields, , '/form-action', 'GET');
```

```php
$form = Form::createFromConfig($fields, '/form-action', 'GET');
```

```php
$form->setMethod('GET')
    ->setAction('/form-action');
```

[Top](#pop-form)

Field Elements
--------------

A form can be wired up by interfacing directly with form element objects and the form object itself.

```php
use Pop\Form\Form;
use Pop\Form\Element\Input;
use Pop\Validator;

$form = new Form();
$form->setAttribute('id', 'my-form');

$username = new Input\Text('username');
$username->setLabel('Username:')
    ->setRequired(true)
    ->setAttribute('size', 40)
    ->addValidator(new Validator\AlphaNumeric());

$email = new Input\Email('email');
$email->setLabel('Email:')
    ->setRequired(true)
    ->setAttribute('size', 40);

$submit = new Input\Submit('submit', 'SUBMIT');

// Add a single field
$form->addField($username);

// Add multiple fields
$form->addFields([$email, $submit]);

if ($_POST) {
    $form->setFieldValues($_POST);
    if (!$form->isValid()) {
        echo $form; // Has errors
    } else {
        echo 'Valid!';
    }
} else {
    echo $form;
}
```

There are number of different concepts happening in the above example:

1. We created the form object and gave it an `id` attribute.
2. We created the individual field elements setting their name, label, attributes, validators, etc.
3. We added the field elements to the form object.
4. We checked for a `$_POST` submission. If not detected, we just render the form for the first time.
5. If a `$_POST` submission is detected:
    1. Set the field values with the values in the $_POST array
    (a bad idea without any [filtering](#filtering))
    2. Check if the form object passes validation. If not, re-render the form with the errors.
    If it does pass, then you're good to go.


On the first pass, the form will render like this:

```html
<form action="/" method="post" id="my-form">
    <fieldset d="my-form-fieldset-1" class="my-form-fieldset"></fieldset>
        <dl>
            <dt>
                <label for="username" class="required">Username:</label>
            </dt>
            <dd>
                <input type="text" name="username" id="username" value="" required="required" size="40" />
            </dd>
            <dt>
                <label for="email" class="required">Email:</label>
            </dt>
            <dd>
                <input type="email" name="email" id="email" value="" required="required" size="40" />
            </dd>
            <dd>
                <input type="submit" name="submit" id="submit" value="SUBMIT" />
            </dd>
        </dl>
    </fieldset>
</form>
```

If it fails validation, it will render with the errors. In this case, the username was not alphanumeric:

```html
<form action="/" method="post" id="my-form">
    <fieldset d="my-form-fieldset-1" class="my-form-fieldset"></fieldset>
        <dl>
            <dt>
                <label for="username" class="required">Username:</label>
            </dt>
            <dd>
                <input type="text" name="username" id="username" value="" required="required" size="40" />
                <div class="error">The value must only contain alphanumeric characters.</div>
            </dd>
            <dt>
                <label for="email" class="required">Email:</label>
            </dt>
            <dd>
                <input type="email" name="email" id="email" value="" required="required" size="40" />
            </dd>
            <dd>
                <input type="submit" name="submit" id="submit" value="SUBMIT" />
            </dd>
        </dl>
    </fieldset>
</form>
```

[Top](#pop-form)

Field Configurations
--------------------

We can do the same thing as above with a field configuration array,
which helps streamline the process:

```php
use Pop\Form\Form;
use Pop\Validator;

$fields = [
    'username' => [
        'type'       => 'text',
        'label'      => 'Username:',
        'required'   => true,
        'attributes' => [
            'size' => 40
        ],
        'validators' => [
            new Validator\AlphaNumeric()
        ]
    ],
    'email' => [
        'type'       => 'email',
        'label'      => 'Email:',
        'required'   => true,
        'attributes' => [
            'size' => 40
        ]
    ],
    'submit' => [
        'type'  => 'submit',
        'value' => 'SUBMIT'
    ]
];

$form = Form::createFromConfig($fields);
$form->setAttribute('id', 'my-form');

if ($_POST) {
    $form->setFieldValues($_POST);
    if (!$form->isValid()) {
        echo $form; // Has errors
    } else {
        echo 'Valid!';
    }
} else {
    echo $form;
}
```

[Top](#pop-form)

### Fieldsets

Multiple fieldset configurations can be used to generate a larger form with more organized elements.
This requires the config to contain multiple arrays of field configurations:

```php
use Pop\Form\Form;

$fields = [
    [
        'username' => [
            'type'       => 'text',
            'label'      => 'Username:',
            'required'   => true,
        ],
        'email' => [
            'type'       => 'email',
            'label'      => 'Email:',
            'required'   => true,
        ],
        'submit' => [
            'type'  => 'submit',
            'value' => 'SUBMIT'
        ]
    ],
    [
        'first_name' => [
            'type'  => 'text',
            'label' => 'First Name:',
        ],
        'last_name' => [
            'type'  => 'text',
            'label' => 'Last Name:',
        ],
    ],
    [
        'submit' => [
            'type'  => 'submit',
            'value' => 'SUBMIT'
        ]
    ]
];

$form = Form::createFromFieldsetConfig($fields);
```

Which produces the following HTML with the appropriate `fieldset` grouping:

```html
<form action="#" method="post" id="my-form" class="pop-form">
    <fieldset id="my-form-fieldset-1" class="pop-form-fieldset">
        <dl>
            <dt>
                <label for="username" class="required">Username:</label>
            </dt>
            <dd>
                <input type="text" name="username" id="username" value="" required="required" />
            </dd>
            <dt>
                <label for="email" class="required">Email:</label>
            </dt>
            <dd>
                <input type="email" name="email" id="email" value="" required="required" />
            </dd>
            <dd>
                <input type="submit" name="submit" id="submit" value="SUBMIT" />
            </dd>
        </dl>
    </fieldset>
    <fieldset id="my-form-fieldset-2" class="pop-form-fieldset">
        <dl>
            <dt>
                <label for="first_name">First Name:</label>
            </dt>
            <dd>
                <input type="text" name="first_name" id="first_name" value="" />
            </dd>
            <dt>
                <label for="last_name">Last Name:</label>
            </dt>
            <dd>
                <input type="text" name="last_name" id="last_name" value="" />
            </dd>
        </dl>
    </fieldset>
    <fieldset id="my-form-fieldset-3" class="pop-form-fieldset">
        <dl>
            <dd>
                <input type="submit" name="submit" id="submit" value="SUBMIT" />
            </dd>
        </dl>
    </fieldset>
</form>
```

[Top](#pop-form)

### Legends

If you'd like to label each of the multiple fieldsets, that can be done by using `legend` values
as the array keys in the config:

```php
use Pop\Form\Form;

$fields = [
    'Account Info' => [
        'username' => [
            'type'       => 'text',
            'label'      => 'Username:',
            'required'   => true,
        ],
        'email' => [
            'type'       => 'email',
            'label'      => 'Email:',
            'required'   => true,
        ],
        'submit' => [
            'type'  => 'submit',
            'value' => 'SUBMIT'
        ]
    ],
    'Personal Info' => [
        'first_name' => [
            'type'  => 'text',
            'label' => 'First Name:',
        ],
        'last_name' => [
            'type'  => 'text',
            'label' => 'Last Name:',
        ],
    ],
    [
        'submit' => [
            'type'  => 'submit',
            'value' => 'SUBMIT'
        ]
    ]
];

$form = Form::createFromFieldsetConfig($fields);
```

Which produces the following HTML with the appropriate `fieldset` grouping:

```html
<form action="#" method="post" id="my-form" class="pop-form">
    <fieldset id="my-form-fieldset-1" class="pop-form-fieldset">
        <legend>Account Info</legend>
        <dl>
            <dt>
                <label for="username" class="required">Username:</label>
            </dt>
            <dd>
                <input type="text" name="username" id="username" value="" required="required" />
            </dd>
            <dt>
                <label for="email" class="required">Email:</label>
            </dt>
            <dd>
                <input type="email" name="email" id="email" value="" required="required" />
            </dd>
            <dd>
                <input type="submit" name="submit" id="submit" value="SUBMIT" />
            </dd>
        </dl>
    </fieldset>
    <fieldset id="my-form-fieldset-2" class="pop-form-fieldset">
        <legend>Personal Info</legend>
        <dl>
            <dt>
                <label for="first_name">First Name:</label>
            </dt>
            <dd>
                <input type="text" name="first_name" id="first_name" value="" />
            </dd>
            <dt>
                <label for="last_name">Last Name:</label>
            </dt>
            <dd>
                <input type="text" name="last_name" id="last_name" value="" />
            </dd>
        </dl>
    </fieldset>
    <fieldset id="my-form-fieldset-3" class="pop-form-fieldset">
        <dl>
            <dd>
                <input type="submit" name="submit" id="submit" value="SUBMIT" />
            </dd>
        </dl>
    </fieldset>
</form>
```

[Top](#pop-form)

Field Containers
----------------

The default fieldset HTML containers for the form elements is a combination of `dl`, `dt` and `dd` tags.
If alternate container tags are needed, you can set them like these examples below.

##### Using `table`:

```php
$form = Form::createFromConfig($fields, 'table');
```

```html
<form action="#" method="post" id="my-form" class="pop-form">
    <fieldset id="my-form-fieldset-1" class="pop-form-fieldset">
        <table>
            <tr>
                <td>
                    <label for="username" class="required">Username:</label>
                </td>
                <td>
                    <input type="text" name="username" id="username" value="" required="required" size="40" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="email" class="required">Email:</label>
                </td>
                <td>
                    <input type="email" name="email" id="email" value="" required="required" size="40" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="submit" id="submit" value="SUBMIT" />
                </td>
            </tr>
        </table>
    </fieldset>
</form>
```

##### Using `div` (or any other single element container):

```php
$form = Form::createFromConfig($fields, 'div');
```

```html
<form action="#" method="post" id="my-form" class="pop-form">
    <fieldset id="my-form-fieldset-1" class="pop-form-fieldset">
        <div>
            <label for="username" class="required">Username:</label>
            <input type="text" name="username" id="username" value="" required="required" size="40" />
        </div>
        <div>
            <label for="email" class="required">Email:</label>
            <input type="email" name="email" id="email" value="" required="required" size="40" />
        </div>
        <div>
            <input type="submit" name="submit" id="submit" value="SUBMIT" />
        </div>
    </fieldset>
</form>
```

[Top](#pop-form)

Filtering
---------

As mentioned above, when dealing user-submitted values, it's a bad idea to use them or
display them back on the screen without filtering them. A common set a filters to employ
would be `strip_tags` and `htmlentities`. So in the first example, we would add filters
to the $_POST block:

```php

use Pop\Filter\Filter;

/** ... Code to create form **/

if ($_POST) {
    $form->addFilter(new Filter('strip_tags'))
         ->addFilter(new Filter('htmlentities', [ENT_QUOTES, 'UTF-8']));
    $form->setFieldValues($_POST);
    if (!$form->isValid()) {
        echo $form; // Has errors
    } else {
        $form->clearFilters();
        $form->addFilter(new Filter('html_entity_decode', [ENT_QUOTES, 'UTF-8']));
        echo 'Valid!';
    }
} else {
    echo $form;
}
```

Of course, the `strip_tags` filter will strip out any possible malicious tags. The `htmlentities`
filter is useful if the form has to render with the values in it again:

```html
<input type="text" name="username" id="username" 
    value="Hello&quot;World&quot;" required="required" size="40" />
```

Without the `htmlentities` filter, the quotes within the value would break the HTML of the input field.
Of course, if you want to use the values after the form is validated, then you have to call `clearFilters()`
and filter the values with `html_entity_decode`.

[Top](#pop-form)

Validation
----------

Of course, one of the main reasons for using a form component such as this one is the leverage
the validation aspect of it. You've already seen the use of a basic validator from the `pop-validator`
component and those are easy enough to use. But, you can create your own custom validators by
either extending the `pop-validator` component with your own or just writing your own custom
callable validators. The only real rule that needs to be followed is that the custom validator
must return null on success or a string message on failure that is then used in error display.
Here are some examples:

##### Using a closure

```php
$username = new Input\Text('username');
$username->addValidator(function ($value) {
    if (strlen($value) < 6) {
        return 'The username value must be greater than or equal to 6.';
    }
});
```

##### Using a validator

```php
use Pop\Validator\AlphaNumeric;

$username = new Input\Text('username');
$username->addValidator(new AlphaNumeric());
```

##### Using a custom class

```php
class MyValidator
{
    public function validate($value)
    {
        if (strlen($value) < 6) {
            return 'The username value must be greater than or equal to 6';
        }
    }
}

$username = new Input\Text('username');
$username->addValidator([new MyValidator(), 'validate']);
```

##### Validation-only forms

There is a `FormValidator` class that is available for only validating a set of field values. The benefit
of this feature is to not be burdened with the concern of rendering an entire form object, and to only
return the appropriate validation messaging. This is useful for things like API calls, where the form
rendering might be handled by another piece of the application (and not the PHP server side). 

```php
use Pop\Form\FormValidator;
use Pop\Validator;

$validators = [
    'username' => new Validator\AlphaNumeric(),
    'password' => new Validator\LengthGte(6)
];

$form = new FormValidator($validators);
$form->setValues([
    'username' => 'admin$%^',
    'password' => '12345'
]);

if (!$form->validate()) {
    print_r($form->getErrors());
}
```

If the field values are bad, the `$form->getErrors()` will return an array of errors like this:

```text
Array
(
    [username] => Array
        (
            [0] => The value must only contain alphanumeric characters.
        )

    [password] => Array
        (
            [0] => The value length must be greater than or equal to 6.
        )

)
```

[Top](#pop-form)

Dynamic Fields
--------------

The `pop-form` comes with the functionality to very quickly wire up form fields that are mapped
to the columns in a database. It does require the installation of the `pop-db` component to work.
Consider that there is a database table class called `Users` that is mapped to the `users` table
in the database. It has six fields: `id`, `username`, `password`, `first_name`, `last_name` and `email`.

(For more information on using `pop-db` [click here](https://github.com/popphp/pop-db).)

```php
use Pop\Form\Form;
use Pop\Form\Fields;
use MyApp\Table\Users;

// The 4th parameter is an 'omit' to prevent certain fields from displaying
$config = Fields::getConfigFromTable(Users::getTableInfo(), null, null, 'id');
$form   = Form::createFromConfig($config);
echo $form;
```

This will render like:

```html
<form action="/" method="post" id="pop-form" class="pop-form">
    <fieldset id="pop-form-fieldset-1" class="pop-form-fieldset">
        <dl>
            <dt>
                <label for="username" class="required">Username:</label>
            </dt>
            <dd>
                <input type="text" name="username" id="username" value="" required="required" />
            </dd>
            <dt>
                <label for="password" class="required">Password:</label>
            </dt>
            <dd>
                <input type="password" name="password" id="password" value="" required="required" />
            </dd>
            <dt>
                <label for="first_name" class="required">First Name:</label>
            </dt>
            <dd>
                <input type="text" name="first_name" id="first_name" value="" required="required" />
            </dd>
            <dt>
                <label for="last_name" class="required">Last Name:</label>
            </dt>
            <dd>
                <input type="text" name="last_name" id="last_name" value="" required="required" />
            </dd>
            <dt>
                <label for="email" class="required">Email:</label>
            </dt>
            <dd>
                <input type="email" name="email" id="email" value="" required="required" />
            </dd>
        </dl>
    </fieldset>
</form>
```

You can set element-specific attributes and values, as well as set fields to omit, like
the 'id' parameter in the above examples. Any `TEXT` column type in the database is
created as textarea objects and then the rest are created as input text objects.

[Top](#pop-form)

ACL Forms
---------

ACL forms utilize the `pop-acl` component and are an extension of the regular form class
that take an ACL object with its roles and resources and enforce which form fields can
be seen and edited. Consider the following code below:

```php
use Pop\Form;
use Pop\Acl;

$acl      = new Acl\Acl();
$admin    = new Acl\AclRole('admin');
$editor   = new Acl\AclRole('editor');
$username = new Acl\AclResource('username');
$password = new Acl\AclResource('password');

$acl->addRoles([$admin, $editor]);
$acl->addResources([$username, $password]);

$acl->deny($editor, 'username', 'edit');
$acl->deny($editor, 'password', 'view');

$fields = [
    'username' => [
        'type'  => 'text',
        'label' => 'Username'
    ],
    'password' => [
        'type'  => 'password',
        'label' => 'Password'
    ],
    'first_name' => [
        'type'  => 'text',
        'label' => 'First Name'
    ],
    'last_name' => [
        'type'  => 'text',
        'label' => 'Last Name'
    ],
    'submit' => [
        'type'  => 'submit',
        'value' => 'Submit'
    ]
];

$form = Form\AclForm::createFromConfig($fields);
$form->setAcl($acl);
```

The `$admin` has no restrictions. However, the `$editor` role does have restrictions and
cannot edit the `username` field and cannot view the `password` field. Setting the `$editor`
as the form role and rendering the form will look like this:

```php
$form->addRole($editor);
echo $form;
```

```text
<form action="#" method="post" id="pop-form" class="pop-form">
    <fieldset id="pop-form-fieldset-1" class="pop-form-fieldset">
        <dl>
            <dt>
                <label for="username">Username</label>
            </dt>
            <dd>
                <input type="text" name="username" id="username" value="" readonly="readonly" />
            </dd>
            <dt>
                <label for="first_name">First Name</label>
            </dt>
            <dd>
                <input type="text" name="first_name" id="first_name" value="" />
            </dd>
            <dt>
                <label for="last_name">Last Name</label>
            </dt>
            <dd>
                <input type="text" name="last_name" id="last_name" value="" />
            </dd>
            <dd>
                <input type="submit" name="submit" id="submit" value="Submit" />
            </dd>
        </dl>
    </fieldset>
</form>
```

There is no `password` field and the `username` field has been made `readonly`. Switch the
role to `$admin` and the entire form will render with no restrictions:

```php
$form->addRole($admin);
echo $form;
```

```text
<form action="#" method="post" id="pop-form" class="pop-form">
    <fieldset id="pop-form-fieldset-1" class="pop-form-fieldset">
        <dl>
            <dt>
                <label for="username">Username</label>
            </dt>
            <dd>
                <input type="text" name="username" id="username" value="" />
            </dd>
            <dt>
                <label for="password">Password</label>
            </dt>
            <dd>
                <input type="password" name="password" id="password" value="" />
            </dd>
            <dt>
                <label for="first_name">First Name</label>
            </dt>
            <dd>
                <input type="text" name="first_name" id="first_name" value="" />
            </dd>
            <dt>
                <label for="last_name">Last Name</label>
            </dt>
            <dd>
                <input type="text" name="last_name" id="last_name" value="" />
            </dd>
            <dd>
                <input type="submit" name="submit" id="submit" value="Submit" />
            </dd>
        </dl>
    </fieldset>
</form>
```
