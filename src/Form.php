<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form;

use Pop\Dom\Child;
use Pop\Form\Element\AbstractElement;
use Pop\Form\Element\Input\Checkbox;
use Pop\Form\Element\Input\Radio;
use Pop\Form\Element\Input\File;

/**
 * Form class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.6
 */

class Form extends Child implements FormInterface, \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Trait declaration
     */
    use FormTrait;

    /**
     * Field fieldsets
     * @var array
     */
    protected array $fieldsets = [];

    /**
     * Form columns
     * @var array
     */
    protected array $columns = [];

    /**
     * Current field fieldset
     * @var int
     */
    protected int $current = 0;

    /**
     * Constructor
     *
     * Instantiate the form object
     *
     * @param  ?array  $fields
     * @param  ?string $action
     * @param  string  $method
     */
    public function __construct(?array $fields = null, ?string $action = null, string $method = 'post')
    {
        if ($action === null) {
            $action = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '#';
        }

        parent::__construct('form');
        $this->setAction($action);
        $this->setMethod($method);

        if ($fields !== null) {
            $this->addFields($fields);
        }
    }

    /**
     * Method to create form object and fields from config
     *
     * @param  array|FormConfig $config
     * @param  ?string           $container
     * @param  ?string          $action
     * @param  string           $method
     * @return Form
     */
    public static function createFromConfig(
        array|FormConfig $config, ?string $container = null, ?string $action = null, string $method = 'post'
    ): Form
    {
        $form = new static(null, $action, $method);
        $form->addFieldsFromConfig($config, $container);
        return $form;
    }

    /**
     * Method to create form object and fields from config
     *
     * @param  array|FormConfig $config
     * @param  ?string          $container
     * @param  ?string          $action
     * @param  string           $method
     * @return Form
     */
    public static function createFromFieldsetConfig(
        array|FormConfig $config, ?string $container = null, ?string $action = null, string $method = 'post'
    ): Form
    {
        $form = new static(null, $action, $method);
        $form->addFieldsetsFromConfig($config, $container);
        return $form;
    }

    /**
     * Method to create a new fieldset object
     *
     * @param  ?string  $legend
     * @param  ?string  $container
     * @return Fieldset
     */
    public function createFieldset(?string $legend = null, ?string $container = null): Fieldset
    {
        $fieldset = new Fieldset();
        if ($legend !== null) {
            $fieldset->setLegend($legend);
        }
        if ($container !== null) {
            $fieldset->setContainer($container);
        }

        $this->addFieldset($fieldset);

        $id = ($this->getAttribute('id') !== null) ?
            $this->getAttribute('id') . '-fieldset-' . ($this->current + 1) : 'pop-form-fieldset-' . ($this->current + 1);

        $class = ($this->getAttribute('class') !== null) ?
            $this->getAttribute('id') . '-fieldset' : 'pop-form-fieldset';

        $fieldset->setAttribute('id', $id);
        $fieldset->setAttribute('class', $class);

        return $fieldset;
    }

    /**
     * Method to set action
     *
     * @param  string $action
     * @return Form
     */
    public function setAction(string $action): Form
    {
        $this->setAttribute('action', str_replace(['?captcha=1', '&captcha=1'], ['', ''], $action));
        return $this;
    }

    /**
     * Method to set method
     *
     * @param  string $method
     * @return Form
     */
    public function setMethod(string $method): Form
    {
        $this->setAttribute('method', $method);
        return $this;
    }

    /**
     * Method to get action
     *
     * @return string|null
     */
    public function getAction(): string|null
    {
        return $this->getAttribute('action');
    }

    /**
     * Method to get method
     *
     * @return string|null
     */
    public function getMethod(): string|null
    {
        return $this->getAttribute('method');
    }

    /**
     * Method to set an attribute
     *
     * @param  string $name
     * @param  mixed  $value
     * @return Form
     */
    public function setAttribute(string $name, mixed $value = null): Form
    {
        parent::setAttribute($name, $value);

        if ($name == 'id') {
            foreach ($this->fieldsets as $i => $fieldset) {
                $id = $value . '-fieldset-' . ($i + 1);
                $fieldset->setAttribute('id', $id);
            }

        } else if ($name == 'class') {
            foreach ($this->fieldsets as $i => $fieldset) {
                $class = $value . '-fieldset';
                $fieldset->setAttribute('class', $class);
            }
        }

        return $this;
    }

    /**
     * Method to set attributes
     *
     * @param  array $attributes
     * @return Form
     */
    public function setAttributes(array $attributes): Form
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
        return $this;
    }

    /**
     * Method to add fieldset
     *
     * @param  Fieldset $fieldset
     * @return Form
     */
    public function addFieldset(Fieldset $fieldset): Form
    {
        $this->fieldsets[] = $fieldset;
        $this->current     = count($this->fieldsets) - 1;
        return $this;
    }

    /**
     * Method to remove fieldset
     *
     * @param  int $i
     * @return Form
     */
    public function removeFieldset(int $i): Form
    {
        if (isset($this->fieldsets[(int)$i])) {
            unset($this->fieldsets[(int)$i]);
        }
        $this->fieldsets = array_values($this->fieldsets);
        if (!isset($this->fieldsets[$this->current])) {
            $this->current = (count($this->fieldsets) > 0) ? count($this->fieldsets) - 1 : 0;
        }
        return $this;
    }

    /**
     * Method to get current fieldset
     *
     * @return Fieldset|null
     */
    public function getFieldset(): Fieldset|null
    {
        return $this->fieldsets[$this->current] ?? null;
    }

    /**
     * Method to get all fieldsets
     *
     * @return array
     */
    public function getFieldsets(): array
    {
        return $this->fieldsets;
    }

    /**
     * Method to determine if the form has fieldsets
     *
     * @return bool
     */
    public function hasFieldsets(): bool
    {
        return !empty($this->fieldsets);
    }

    /**
     * Method to add form column
     *
     * @param  mixed   $fieldsets
     * @param  ?string $class
     * @return Form
     */
    public function addColumn(mixed $fieldsets, ?string $class = null): Form
    {
        if (!is_array($fieldsets)) {
            $fieldsets = [$fieldsets];
        }

        foreach ($fieldsets as $i => $num) {
            $fieldsets[$i] = (int)$num - 1;
        }

        if ($class === null) {
            $class = 'pop-form-column-' . (count($this->columns) + 1);
        }

        $this->columns[$class] = $fieldsets;
        return $this;
    }

    /**
     * Method to determine if form has a column
     *
     * @param  string $class
     * @return bool
     */
    public function hasColumn(string $class): bool
    {
        if (is_numeric($class)) {
            $class = 'pop-form-column-' . $class;
        }

        return isset($this->columns[$class]);
    }

    /**
     * Method to get form column
     *
     * @param  string $class
     * @return array|null
     */
    public function getColumn(string $class): array|null
    {
        if (is_numeric($class)) {
            $class = 'pop-form-column-' . $class;
        }

        return $this->columns[$class] ?? null;
    }

    /**
     * Method to remove form column
     *
     * @param  string $class
     * @return Form
     */
    public function removeColumn(string $class): Form
    {
        if (is_numeric($class)) {
            $class = 'pop-form-column-' . $class;
        }

        if (isset($this->columns[$class])) {
            unset($this->columns[$class]);
        }

        return $this;
    }

    /**
     * Method to get current fieldset index
     *
     * @return int
     */
    public function getCurrent(): int
    {
        return $this->current;
    }

    /**
     * Method to get current fieldset index
     *
     * @param  int $i
     * @return Form
     */
    public function setCurrent(int $i): Form
    {
        $this->current = (int)$i;
        if (!isset($this->fieldsets[$this->current])) {
            $this->fieldsets[$this->current] = $this->createFieldset();
        }
        return $this;
    }

    /**
     * Method to get the legend of the current fieldset
     *
     * @return string|null
     */
    public function getLegend(): string|null
    {
        return $this->fieldsets[$this->current]?->getLegend();
    }

    /**
     * Method to set the legend of the current fieldset
     *
     * @param  string $legend
     * @return Form
     */
    public function setLegend(string $legend): Form
    {
        if (isset($this->fieldsets[$this->current])) {
            $this->fieldsets[$this->current]->setLegend($legend);
        }
        return $this;
    }

    /**
     * Method to add a form field
     *
     * @param  AbstractElement $field
     * @param  ?string         $container
     * @return Form
     */
    public function addField(AbstractElement $field, ?string $container = null): Form
    {
        if (count($this->fieldsets) == 0) {
            $this->createFieldset(null, $container);
        }
        $this->fieldsets[$this->current]->addField($field);
        return $this;
    }

    /**
     * Method to add form fields
     *
     * @param  array $fields
     * @return Form
     */
    public function addFields(array $fields): Form
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
        return $this;
    }

    /**
     * Method to add a form field from a config
     *
     * @param  string $name
     * @param  array  $field
     * @return Form
     */
    public function addFieldFromConfig(string $name, array $field): Form
    {
        $this->addField(Fields::create($name, $field));
        return $this;
    }

    /**
     * Method to add form fields from config
     *
     * @param  array|FormConfig $config
     * @param  ?string          $container
     * @return Form
     */
    public function addFieldsFromConfig(array|FormConfig $config, ?string $container = null): Form
    {
        $i = 1;
        foreach ($config as $name => $field) {
            if (is_numeric($name) && !isset($field[$name]['type'])) {
                $fields = [];
                foreach ($field as $n => $f) {
                    $fields[$n] = Fields::create($n, $f);
                }
                if ($i > 1) {
                    $this->fieldsets[$this->current]->createGroup();
                }
                if (!isset($this->fieldsets[$this->current])) {
                    $this->fieldsets[$this->current] = new Fieldset(null, $container);
                }
                $this->fieldsets[$this->current]->addFields($fields);
                $i++;
            } else {
                $this->addField(Fields::create($name, $field), $container);
            }
        }
        return $this;
    }

    /**
     * Method to add form fieldsets from config
     *
     * @param  array|FormConfig $fieldsets
     * @param  ?string          $container
     * @return Form
     */
    public function addFieldsetsFromConfig(array|FormConfig $fieldsets, ?string $container = null): Form
    {
        foreach ($fieldsets as $legend => $config) {
            if (!is_numeric($legend)) {
                $this->createFieldset($legend, $container);
            } else {
                $this->createFieldset(null, $container);
            }
            $this->addFieldsFromConfig($config);
        }

        return $this;
    }

    /**
     * Method to insert a field before another one
     *
     * @param  string          $name
     * @param  AbstractElement $field
     * @return Form
     */
    public function insertFieldBefore(string $name, AbstractElement $field): Form
    {
        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->hasField($name)) {
                $fieldset->insertFieldBefore($name, $field);
                break;
            }
        }
        return $this;
    }

    /**
     * Method to insert a field after another one
     *
     * @param  string          $name
     * @param  AbstractElement $field
     * @return Form
     */
    public function insertFieldAfter(string $name, AbstractElement $field): Form
    {
        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->hasField($name)) {
                $fieldset->insertFieldAfter($name, $field);
                break;
            }
        }
        return $this;
    }

    /**
     * Method to get the count of elements in the form
     *
     * @return int
     */
    public function count(): int
    {
        $count = 0;
        foreach ($this->fieldsets as $fieldset) {
            $count += $fieldset->count();
        }
        return $count;
    }

    /**
     * Method to get the field values as an array
     *
     * @param  array $options
     * @return array
     */
    public function toArray(array $options = []): array
    {
        $fieldValues = [];

        foreach ($this->fieldsets as $fieldset) {
            $fieldValues = array_merge($fieldValues, $fieldset->toArray());
        }

        if (!empty($options)) {
            if (isset($options['exclude'])) {
                if (!is_array($options['exclude'])) {
                    $options['exclude'] = [$options['exclude']];
                }
                $fieldValues = array_diff_key($fieldValues, array_flip($options['exclude']));
            }
            if (isset($options['filter'])) {
                $fieldValues = array_filter($fieldValues, $options['filter']);
            }
        }

        return $fieldValues;
    }

    /**
     * Method to get a field element object
     *
     * @param  string $name
     * @return AbstractElement|null
     */
    public function getField(string $name): AbstractElement|null
    {
        $namedField = null;
        $fields     = $this->getFields();

        foreach ($fields as $field) {
            if ($field->getName() == $name) {
                $namedField = $field;
                break;
            }
        }

        return $namedField;
    }

    /**
     * Method to get field element objects
     *
     * @return array
     */
    public function getFields(): array
    {
        $fields = [];

        foreach ($this->fieldsets as $fieldset) {
            $fields = array_merge($fields, $fieldset->getAllFields());
        }

        return $fields;
    }

    /**
     * Has a field element object
     *
     * @param  string $name
     * @return bool
     */
    public function hasField(string $name): bool
    {
        return ($this->getField($name) !== null);
    }

    /**
     * Has fields
     *
     * @return bool
     */
    public function hasFields(): bool
    {
        return (!empty($this->getFields()));
    }

    /**
     * Method to remove a form field
     *
     * @param  string $field
     * @return Form
     */
    public function removeField(string $field): Form
    {
        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->hasField($field)) {
                unset($fieldset[$field]);
            }
        }
        return $this;
    }

    /**
     * Method to get a field element value
     *
     * @param  string $name
     * @return mixed
     */
    public function getFieldValue(string $name): mixed
    {
        $fieldValues = $this->toArray();
        return $fieldValues[$name] ?? null;
    }

    /**
     * Method to set a field element value
     *
     * @param  string $name
     * @param  mixed  $value
     * @return Form
     */
    public function setFieldValue(string $name, mixed $value): Form
    {
        foreach ($this->fieldsets as $fieldset) {
            if (isset($fieldset[$name])) {
                $fieldset[$name] = $value;
            }
        }
        return $this;
    }

    /**
     * Method to set field element values
     *
     * @param  array $values
     * @return Form
     */
    public function setFieldValues(array $values): Form
    {
        $fields = $this->toArray();
        foreach ($fields as $name => $value) {
            if (isset($values[$name]) && !($this->getField($name)->isButton())) {
                $this->setFieldValue($name, $values[$name]);
            } else if (!($this->getField($name)->isButton())) {
                $this->getField($name)->resetValue();
            }
        }

        $this->filter();

        return $this;
    }

    /**
     * Filter value with the filters in the form object
     *
     * @param  mixed $field
     * @return mixed
     */
    public function filterValue(mixed $field): mixed
    {
        if ($field instanceof AbstractElement) {
            $name      = $field->getName();
            $type      = $field->getType();
            $realValue = $field->getValue();
        } else {
            $type      = null;
            $name      = null;
            $realValue = $field;
        }

        foreach ($this->filters as $filter) {
            if ($realValue !== null) {
                $realValue = $filter->filter($realValue, $name, $type);
            }
        }

        if (($field instanceof AbstractElement) && !($field instanceof Checkbox) &&
            !($field instanceof Radio)) {
            $field->setValue($realValue);
        }

        return $realValue;
    }

    /**
     * Filter values with the filters in the form object
     *
     * @param  mixed $values
     * @return mixed
     */
    public function filter(mixed $values = null): mixed
    {
        if ($values === null) {
            $values = $this->getFields();
        }

        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $values[$key] = $this->filterValue($value);
            }
        } else {
            $values = $this->filterValue($values);
        }

        return $values;
    }

    /**
     * Determine whether or not the form object is valid
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $result = true;
        $fields = $this->getFields();
        $values = $this->toArray();

        // Check each element for validators, validate them and return the result.
        foreach ($fields as $field) {
            if ($field->validate($values) == false) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Get form element errors for a field.
     *
     * @param  string $name
     * @return array
     */
    public function getErrors(string $name): array
    {
        $field  = $this->getField($name);
        $errors = ($field !== null) ? $field->getErrors() : [];

        return $errors;
    }

    /**
     * Get all form element errors
     *
     * @return array
     */
    public function getAllErrors(): array
    {
        $errors = [];
        $fields = $this->getFields();
        foreach ($fields as $name => $field) {
            if ($field->hasErrors()) {
                $errors[str_replace('[]', '', $field->getName())] = $field->getErrors();
            }
        }

        return $errors;
    }

    /**
     * Method to reset and clear any form field values
     *
     * @return Form
     */
    public function reset(): Form
    {
        $fields = $this->getFields();
        foreach ($fields as $field) {
            $field->resetValue();
        }
        return $this;
    }

    /**
     * Method to clear any security tokens
     *
     * @return Form
     */
    public function clearTokens(): Form
    {
        // Start a session.
        if (session_id() == '') {
            session_start();
        }
        if ($_SESSION) {
            if (isset($_SESSION['pop_csrf'])) {
                unset($_SESSION['pop_csrf']);
            }
            if (isset($_SESSION['pop_captcha'])) {
                unset($_SESSION['pop_captcha']);
            }
        }

        return $this;
    }

    /**
     * Prepare form object for rendering
     *
     * @return Form
     */
    public function prepare(): Form
    {
        if ($this->getAttribute('id') === null) {
            $this->setAttribute('id', 'pop-form');
        }
        if ($this->getAttribute('class') === null) {
            $this->setAttribute('class', 'pop-form');
        }

        if (count($this->columns) > 0) {
            foreach ($this->columns as $class => $fieldsets) {
                $column = new Child('div');
                $column->setAttribute('class', $class);
                foreach ($fieldsets as $i) {
                    if (isset($this->fieldsets[$i])) {
                        $fieldset = $this->fieldsets[$i];
                        $fieldset->prepare();
                        $column->addChild($fieldset);
                    }
                }
                $this->addChild($column);
            }
        } else {
            foreach ($this->fieldsets as $fieldset) {
                $fieldset->prepare();
                $this->addChild($fieldset);
            }
        }

        return $this;
    }

    /**
     * Prepare form object for rendering with a view
     *
     * @return array
     */
    public function prepareForView(): array
    {
        $formData = [];

        foreach ($this->fieldsets as $fieldset) {
            $formData = array_merge($formData, $fieldset->prepareForView());
        }

        return $formData;
    }

    /**
     * Render the form object
     *
     * @param  int     $depth
     * @param  ?string $indent
     * @param  bool    $inner
     * @return string|null
     */
    public function render(int $depth = 0, ?string $indent = null, bool $inner = false): string|null
    {
        if (!($this->hasChildren())) {
            $this->prepare();
        }

        foreach ($this->fieldsets as $fieldset) {
            foreach ($fieldset->getAllFields() as $field) {
                if ($field instanceof File) {
                    $this->setAttribute('enctype', 'multipart/form-data');
                    break;
                }
            }
        }

        return parent::render($depth, $indent, $inner);
    }

    /**
     * Render and return the form object as a string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Set method to set the property to the value of fields[$name]
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->setFieldValue($name, $value);
    }

    /**
     * Get method to return the value of fields[$name]
     *
     * @param  string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->getFieldValue($name);
    }

    /**
     * Return the isset value of fields[$name]
     *
     * @param  string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        $fieldValues = $this->toArray();
        return isset($fieldValues[$name]);
    }

    /**
     * Unset fields[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        $fieldValues = $this->toArray();
        if (isset($fieldValues[$name])) {
            $this->getField($name)->resetValue();
        }
    }

}
