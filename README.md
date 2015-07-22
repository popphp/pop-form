pop-form
========

[![Build Status](https://travis-ci.org/popphp/pop-form.svg?branch=master)](https://travis-ci.org/popphp/pop-form)
[![Coverage Status](http://www.popphp.org/cc/coverage.php?comp=pop-form)](http://www.popphp.org/cc/pop-form/)

OVERVIEW
--------
`pop-form` is a robust component for managing, rendering and validating HTML forms.
With it, you can have complete control over how a form looks and functions as well
as granular control over field validation. Features include:

* Field configuration
* Validation
    + Use any callable validation object, such as `pop-validator` or custom validators
* Filtering
* Form templates
* Dynamic field generation based on the fields of a database table

`pop-form`is a component of the [Pop PHP Framework](http://www.popphp.org/).

INSTALL
-------

Install `pop-form` using Composer.

    composer require popphp/pop-form

## BASIC USAGE

* [Using field configurations](#using-field-configurations)
* [Using field objects](#using-field-objects)
* [Templates](#templates)
* [Filtering](#filtering)
* [Validation](#validation)
* [Dynamic fields from a database table](#dynamic-fields-from-a-database-table)

### Using field configurations

The form object will default to 'post' as the method and the current request URI as
the action otherwise changed by the user.

```php
use Pop\Form\Form;
use Pop\Validator;

$fields = [
    'username' => [
        'type'       => 'text',
        'label'      => 'Username:',
        'required'   => true,
        'attributes' => [
            'size'     => 40
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
            'size'     => 40
        ],
        'validators' => new Validator\Email()
    ],
    'submit' => [
        'type'  => 'submit',
        'value' => 'SUBMIT'
    ]
];

$form = new Form($fields);
$form->setAttribute('id', 'my-form');

if ($_POST) {
    $form->addFilter('strip_tags')
         ->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
         ->setFieldValues($_POST);
    if (!$form->isValid()) {
        echo $form;
    } else {
        // Form is valid.
        echo 'Valid!';
    }
} else {
    echo $form;
}
```

So a few different things are going on in the above example:
 
1. We set a `$fields` configuration array first, defining the field type, name, label, attributes, validators, etc.
2. We created the form object and gave it an 'id' attribute.
3. We checked for a $_POST submission. If not detected, we just render the form for the first time.
4. If a $_POST submission is detected:
    1. Add some filters (`strip tags` and `htmlentities`) and then set the field values with the values in the $_POST array.
    2. Check if the form object passes validation. If not, re-render the form with the errors. If it does pass, then you're good to go.

On the first pass, the form will render like this:

```html
<form action="/" method="post" id="my-form">
    <dl id="my-form-field-group-1" class="my-form-field-group">
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
</form>
```

If it fails validation, it will render with the errors, in this case, the username was not alphanumeric:

```html
<form action="/" method="post" id="my-form">
    <dl id="my-form-field-group-1" class="my-form-field-group">
    <dt>
        <label for="username" class="required">Username:</label>
    </dt>
    <dd>
        <input type="text" name="username" id="username" value="sdc@#$234" required="required" size="40" />
        <div class="error">The value must only contain alphanumeric characters.</div>
    </dd>
    <dt>
        <label for="email" class="required">Email:</label>
    </dt>
    <dd>
        <input type="email" name="email" id="email" value="test@test.com" required="required" size="40" />
    </dd>
    <dd>
        <input type="submit" name="submit" id="submit" value="SUBMIT" />
    </dd>
    </dl>
</form>
```

[Top](#basic-usage)

### Using field objects

[Top](#basic-usage)

### Templates

[Top](#basic-usage)

### Filtering

[Top](#basic-usage)

### Validation

[Top](#basic-usage)

### Dynamic fields from a database table

[Top](#basic-usage)
