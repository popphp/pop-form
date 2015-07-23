pop-form
========

[![Build Status](https://travis-ci.org/popphp/pop-form.svg?branch=master)](https://travis-ci.org/popphp/pop-form)
[![Coverage Status](http://www.popphp.org/cc/coverage.php?comp=pop-form)](http://www.popphp.org/cc/pop-form/)

OVERVIEW
--------
`pop-form` is a robust component for managing, rendering and validating HTML forms.
With it, you can have complete control over how a form looks and functions as well
as granular control over field validation. Features include:

* Field element configuration and creation
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

* [Using field element objects](#using-field-element-objects)
* [Using field configurations](#using-field-configurations)
* [Templates](#templates)
* [Filtering](#filtering)
* [Validation](#validation)
* [Dynamic fields from a database table](#dynamic-fields-from-a-database-table)

### Using field element objects

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

$form->addElements([$username, $email, $submit]);

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

So a few  things are going on in the above example:

1. We created the form object and gave it an 'id' attribute.
2. We created the individual field elements setting their name, label, attributes, validators, etc.
3. We added the field elements to the form object
4. We checked for a $_POST submission. If not detected, we just render the form for the first time.
5. If a $_POST submission is detected:
    1. Set the field values with the values in the $_POST array
    (a bad idea without any [filtering](#filtering))
    2. Check if the form object passes validation. If not, re-render the form with the errors.
    If it does pass, then you're good to go.

Just as a note, the form object will default to 'post' as the method and the
current request URI as the action otherwise changed by the user.

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

If it fails validation, it will render with the errors. In this case, the username was not alphanumeric:

```html
<form action="/" method="post" id="my-form">
    <dl id="my-form-field-group-1" class="my-form-field-group">
    <dt>
        <label for="username" class="required">Username:</label>
    </dt>
    <dd>
        <input type="text" name="username" id="username" value="sdcsdc#$2345" required="required" size="40" />
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

### Using field configurations

We can do the same thing as above with a field configuration array,
which helps streamline the process a bit:

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

$form = new Form($fields);
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

[Top](#basic-usage)

### Templates

By default, the form object will render using a DL element with a 1:1 matching DT and DD
elements for field labels and elements. However, you can easily expand your control over
the rendering and display of the form object by using templates.

#### Using a stream template

Consider the following stream template for the above example `form.html`:

```html
    <table>
        <tr>
            <td>
                [{username_label}]
            </td>
            <td>
                [{username}]
            </td>
        </tr>
        <tr>
            <td>
                [{email_label}]
            </td>
            <td>
                [{email}]
            </td>
        </tr>
        <tr>
            <td>
                &nbsp;
            </td>
            <td>
                [{submit}]
            </td>
        </tr>
    </table>
```

We can then set the template for the form object like this:

```php
$form->setTemplate('form.html');
```

And it will render like this:

```html
<form action="/" method="post" id="my-form">
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
            <td>
                &nbsp;
            </td>
            <td>
                <input type="submit" name="submit" id="submit" value="SUBMIT" />
            </td>
        </tr>
    </table>
</form>
```

#### Using a file template

Similarly, you could use a PHP file template. Consider the PHTML file `form.phtml`:

```php
<form action="<?php echo $action; ?>" method="<?php echo $method; ?>">
    <table>
        <tr>
            <td>
                <?php echo $username_label . PHP_EOL; ?>
            </td>
            <td>
                <?php echo $username . PHP_EOL; ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $email_label . PHP_EOL; ?>
            </td>
            <td>
                <?php echo $email . PHP_EOL; ?>
            </td>
        </tr>
        <tr>
            <td>
                &nbsp;
            </td>
            <td>
                <?php echo $submit . PHP_EOL; ?>
            </td>
        </tr>
    </table>
</form>
```

Set that as the template:

```php
$form->setTemplate('form.phtml');
```

and it would render like this:

```html
<form action="/" method="post">
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
            <td>
                &nbsp;
            </td>
            <td>
                <input type="submit" name="submit" id="submit" value="SUBMIT" />
            </td>
        </tr>
    </table>
</form>
```

[Top](#basic-usage)

### Filtering

[Top](#basic-usage)

### Validation

[Top](#basic-usage)

### Dynamic fields from a database table

[Top](#basic-usage)
