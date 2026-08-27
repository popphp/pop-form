pop-form
========

[![Build Status](https://github.com/popphp/pop-form/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-form/actions)
[![Coverage Status](https://cc.popphp.org/coverage.php?comp=pop-form)](https://cc.popphp.org/pop-form/)

[![Join the chat at https://discord.gg/TZjgT74U7E](https://media.popphp.org/img/discord.svg)](https://discord.gg/TZjgT74U7E)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Field Elements](#field-elements)
* [Field Configurations](#field-configurations)
    - [Fieldsets](#fieldsets)
    - [Legends](#legends) 
* [Field Containers](#field-containers)
* [Rendering Into Your Own View](#rendering-into-your-own-view)
* [Accessibility](#accessibility)
* [Filtering](#filtering)
* [Validation](#validation)
* [CSRF Protection](#csrf-protection)
* [File Uploads](#file-uploads)
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
        "popphp/pop-form" : "^5.0.0"
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
                <input type="text" name="username" id="username" value="" required="required" aria-invalid="true" aria-describedby="username-error" />
                <div class="error" id="username-error" role="alert">
                    <span>This field is required.</span>
                </div>
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

Note the `aria-invalid`/`aria-describedby` on the input and the `role="alert"` on the error `div` — these are
wired automatically whenever a field has errors or a hint. See [Accessibility](#accessibility) below.

The form object will default to `POST` as the method and the current `REQUEST_URI`
as the action, but those values can be changed in a number of ways:

```php
$form = Form::createFromConfig($fields, null, '/form-action', 'GET');
```

```php
$form->setMethod('GET')
    ->setAction('/form-action');
```

(Note: `new Form(...)`'s first argument expects an array of already-built field element objects, not a
config array — see [Field Elements](#field-elements) below for that form of construction.)

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

If it fails validation, it will render with the errors. In this case, the username was not alphanumeric
(note that the submitted value is echoed back into the field, which is exactly why [filtering](#filtering)
submitted values matters):

```html
<form action="/" method="post" id="my-form">
    <fieldset d="my-form-fieldset-1" class="my-form-fieldset"></fieldset>
        <dl>
            <dt>
                <label for="username" class="required">Username:</label>
            </dt>
            <dd>
                <input type="text" name="username" id="username" value="admin$%^" required="required" size="40" aria-invalid="true" aria-describedby="username-error" />
                <div class="error" id="username-error" role="alert">
                    <span>The value must only contain alphanumeric characters.</span>
                </div>
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

Rendering Into Your Own View
----------------------------

Everything up to this point has let `pop-form` build and output the whole `<form>...</form>` markup itself,
via `echo $form` (or `(string)$form`) — it wraps every field in one of the [containers](#field-containers)
above and handles the labels, hints and errors for you. That's the easy path, but sometimes you need the
individual pieces instead — the form markup has to fit inside an existing template, use a container structure
pop-form doesn't provide out of the box, or be handed off field-by-field to a front-end framework/templating
engine. For that, call `prepareForView()` instead of echoing the form:

```php
if ($_POST) {
    $form->setFieldValues($_POST);
    $form->isValid();
}

$fields = $form->prepareForView();
```

`prepareForView()` doesn't render any container markup at all — it returns a flat array of the already-rendered
pieces for every field, keyed off each field's name, so you can drop them wherever you want in your own
template. For a field named `username` with a hint that fails validation, `$fields` looks like:

```php
[
    'username_label'  => '<label for="username" class="required">Username:</label>',
    'username_hint'   => '<span id="username-hint">Letters and numbers only.</span>',
    'username_errors' => ['This field is required.'],
    'username'        => '<input type="text" name="username" id="username" value="" required="required" aria-invalid="true" aria-describedby="username-hint username-error" />',
    // ...one set of keys per field
]
```

* `$fields['{name}']` — the rendered field element itself (always present).
* `$fields['{name}_label']` — the rendered `<label>`, only present if the field has one.
* `$fields['{name}_hint']` — the rendered hint `<span>`, only present if the field has a hint.
* `$fields['{name}_errors']` — a plain array of error message strings, only present if the field failed validation.

You're then free to lay those pieces out however your template needs, for example:

```html
<form action="/" method="post">
    <div class="form-row">
        <?=$fields['username_label'] ?? ''?>
        <?=$fields['username']?>
        <?php if (isset($fields['username_hint'])): ?>
            <?=$fields['username_hint']?>
        <?php endif; ?>
        <?php foreach ($fields['username_errors'] ?? [] as $error): ?>
            <div class="error"><?=$error?></div>
        <?php endforeach; ?>
    </div>
    <div class="form-row">
        <?=$fields['email_label'] ?? ''?>
        <?=$fields['email']?>
    </div>
    <?=$fields['submit']?>
</form>
```

The same `aria-invalid`/`aria-describedby` wiring described in [Accessibility](#accessibility) below still
happens on each field, whether it ends up rendered by `pop-form` or laid out by hand this way — `prepareForView()`
just leaves the surrounding container markup up to you.

`prepareForView()` is available on the `Form` object (merging fields across all of its fieldsets) as well as
on an individual `Fieldset` object, if you're working with one fieldset at a time.

[Top](#pop-form)

Accessibility
-------------

Whenever a field has a hint and/or validation errors, `pop-form` automatically wires up the ARIA attributes
that connect them for assistive technology — there's nothing extra to configure. Given a required field with
a hint that fails validation:

```php
$fields = [
    'username' => [
        'type'     => 'text',
        'label'    => 'Username:',
        'required' => true,
        'hint'     => 'Letters and numbers only.'
    ]
];
```

it renders like this once it has an error:

```html
<input type="text" name="username" id="username" value="" required="required" aria-invalid="true" aria-describedby="username-hint username-error" />
<span id="username-hint">Letters and numbers only.</span>
<div class="error" id="username-error" role="alert">
    <span>This field is required.</span>
</div>
```

* The hint and error containers get predictable, stable ids (`{name}-hint` / `{name}-error`).
* The field itself gets `aria-describedby`, listing whichever of those ids apply, and `aria-invalid="true"`
  while it has errors — both are removed again once the field is valid, so a corrected field doesn't keep
  announcing itself as invalid on re-render.
* The error container gets `role="alert"`, so screen readers announce it as soon as it appears.
* `aria-invalid` is intentionally left off `CheckboxSet`/`RadioSet` (they render as a `<fieldset>`, and
  `aria-invalid` isn't a valid ARIA state for a grouping element) — `aria-describedby` is still applied there.

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
    'password' => new Validator\LengthGreaterThanEqual(6)
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

CSRF Protection
---------------

A CSRF token field can be added to a form just like any other field, using the `csrf` type:

```php
use Pop\Form\Form;

$fields = [
    'csrf_token' => [
        'type' => 'csrf'
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
        echo $form; // Has errors — may include an invalid/missing/expired CSRF token
    } else {
        echo 'Valid!';
    }
} else {
    echo $form;
}
```

This renders as a hidden input whose value is a cryptographically random token, stored server-side in the
session (starting one automatically, if needed) and validated on submission with a timing-safe comparison —
a mismatched or missing token fails validation with the message `The security token does not match.`.

A few things worth knowing:

* Tokens are namespaced in the session by the field's name, so multiple CSRF-protected forms/fields can
  coexist in the same session (and page) without clobbering each other's token.
* Tokens expire after 300 seconds by default; pass a different number of seconds via the `expire` config key
  (or the `Csrf` element's constructor) to change that. A value of `0` or less disables expiration entirely.
* Call `$form->clearTokens()` to clear all stored CSRF tokens from the session — a reasonable thing to do
  after a successful, sensitive submission.
* There is no `captcha` field type. A math/image CAPTCHA is trivial for modern bots to defeat and offers
  little real protection; if you need to filter out unsophisticated form spam, a honeypot field (a hidden
  input real users never fill in) is a lighter-weight, more effective alternative to build at the application
  level.

[Top](#pop-form)

File Uploads
------------

The `file` field type accepts two optional, purpose-built validation options beyond the standard config keys:

```php
use Pop\Form\Form;

$fields = [
    'avatar' => [
        'type'          => 'file',
        'label'         => 'Avatar:',
        'allowed-types' => ['jpg', 'jpeg', 'png', 'gif'],
        'max-size'      => 2000000 // 2 MB, in bytes
    ],
    'submit' => [
        'type'  => 'submit',
        'value' => 'SUBMIT'
    ]
];

$form = Form::createFromConfig($fields);
```

* `allowed-types` is an extension allowlist (case-insensitive; a leading dot is optional, so `'jpg'` and
  `'.jpg'` are equivalent). It's checked against the client-submitted filename's extension only — **not** the
  file's actual content, so it's not a hard content-type guarantee (a client can rename any file). Pair it
  with real content validation (e.g. `finfo`/magic-byte sniffing) at the point you actually store the file, if
  that distinction matters for your use case.
* `max-size` is the maximum allowed upload size in bytes, checked against the size PHP itself reports for the
  upload. The resulting error message is human-readable (e.g. `The file size must be less than or equal to
  2 MB.`) rather than a raw byte count.
* `Form` automatically sets `enctype="multipart/form-data"` on the `<form>` tag at render time whenever it
  contains a `file` field — there's nothing extra to configure for that.

Both of these can also be set directly on the element itself, via `setAllowedTypes(array $extensions)` and
`setMaxSize(int $bytes)`.

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
