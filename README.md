pop-form
========

[![Build Status](https://travis-ci.org/popphp/pop-form.svg?branch=master)](https://travis-ci.org/popphp/pop-form)
[![Coverage Status](http://www.popphp.org/cc/coverage.php?comp=pop-form)](http://www.popphp.org/cc/pop-form/)

OVERVIEW
--------
`pop-form` is a robust component for managing, rendering and validating HTML forms.
With it, you can have complete control over how a form looks and functions as well
as granular control over field validation. Features include:

* Field element creation and configuration
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
3. We added the field elements to the form object.
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
<form action="<?=$action; ?>" method="<?=$method; ?>">
    <table>
        <tr>
            <td>
                <?=$username_label . PHP_EOL; ?>
            </td>
            <td>
                <?=$username . PHP_EOL; ?>
            </td>
        </tr>
        <tr>
            <td>
                <?=$email_label . PHP_EOL; ?>
            </td>
            <td>
                <?=$email . PHP_EOL; ?>
            </td>
        </tr>
        <tr>
            <td>
                &nbsp;
            </td>
            <td>
                <?=$submit . PHP_EOL; ?>
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

As mentioned above, when dealing user-submitted values, it's a bad idea to use them or
display them back on the screen without filtering them. A common set a filters to employ
would be `strip_tags` and `htmlentities`. So in the first example, we would add filters
to the $_POST block:

```php
if ($_POST) {
    $form->addFilter('strip_tags')
         ->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8']);
    $form->setFieldValues($_POST);
    if (!$form->isValid()) {
        echo $form; // Has errors
    } else {
        $form->clearFilters();
        $form->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8']);
        echo 'Valid!';
    }
} else {
    echo $form;
}
```

Of course, the `strip_tags` filter will strip out and possible malicious tags. The `htmlentities`
filter is useful if the form has to render with the values in it again:

```html
<input type="text" name="username" id="username" value="Hello&quot;World&quot;" required="required" size="40" />
```

Without the `htmlentities` filter, the quotes within the value would break the HTML of the input field.
Of course, if you want to use the values after the form is validated, then you have to call `clearFilters()`
and filter the values with `html_entity_decode`.

[Top](#basic-usage)

### Validation

Of course, one of the main reasons of using a form component such as this one is the leverage
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

##### Using a custom class

```php
class MyValidator
{
    public function validate($value)
    {
        if (strlen($value) < 6) {
            return 'The password value must be greater than or equal to 6';
        }
    }
}

$username = new Input\Text('username');
$username->addValidator([new MyValidator(), 'validate']);
```

[Top](#basic-usage)

### Dynamic fields from a database table

The `pop-form` comes with the functionality to very quickly wire up form fields that are mapped
to the columns in a database. It does require the installation of the `pop-db` component to work.
Consider that there is a database table class called `Users` that is mapped to the `users` table
in the database.

For more information on using `pop-db` [click here](https://github.com/popphp/pop-db).

```php
use Pop\Form\Form;
use Pop\Form\Fields;

$fields = new Fields(Users::getTableInfo(), null, null, 'id');
$fields->submit = [
    'type'  => 'submit',
    'value' => 'SUBMIT'
];

$form = new Form($fields->getFields());
echo $form;
```

This will render like:

```html
<form action="/fields2.php" method="post">
    <dl id="pop-form-field-group-1" class="pop-form-field-group">
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
    <dd>
        <input type="submit" name="submit" id="submit" value="SUBMIT" />
    </dd>
    </dl>
</form>
```

You can set element-specific attributes and values, as well as set fields to omit, like
the 'id' parameter in the above examples. Any `TEXT` column type in the database is 
created as textarea objects and then the rest are created as input text objects.

[Top](#basic-usage)
