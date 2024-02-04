<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form;

use Pop\Dom\Child;
use Pop\Form\Element\AbstractElement;
use ArrayIterator;

/**
 * Form fieldset class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */

class Fieldset extends Child implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Form field elements
     * @var array
     */
    protected array $fields = [];

    /**
     * Current field group
     * @var int
     */
    protected int $current = 0;

    /**
     * Fieldset legend
     * @var ?string
     */
    protected ?string $legend = null;

    /**
     * Fieldset container (dl, table, div or p)
     * @var string
     */
    protected string $container = 'dl';

    /**
     * Constructor
     *
     * Instantiate the form fieldset object
     *
     * @param  ?array $fields
     * @param  ?string $container
     */
    public function __construct(?array $fields = null, ?string $container = 'dl')
    {
        parent::__construct('fieldset');
        if ($container !== null) {
            $this->setContainer($container);
        }
        if ($fields !== null) {
            $this->addFields($fields);
        }
    }

    /**
     * Method to create form fieldset object and fields from config
     *
     * @param  array  $config
     * @return Fieldset
     */
    public static function createFromConfig(array $config): Fieldset
    {
        $fields = [];

        foreach ($config as $name => $field) {
            $fields[$name] = Fields::create($name, $field);
        }

        return new static($fields);
    }

    /**
     * Method to set container
     *
     * @param  string $container
     * @return Fieldset
     */
    public function setContainer(string $container): Fieldset
    {
        $this->container = strtolower($container);
        return $this;
    }

    /**
     * Method to get current group index
     *
     * @param  int $i
     * @return Fieldset
     */
    public function setCurrent(int $i): Fieldset
    {
        $this->current = (int)$i;
        if (!isset($this->fields[$this->current])) {
            $this->fields[$this->current] = [];
        }
        return $this;
    }

    /**
     * Method to create new group
     *
     * @return Fieldset
     */
    public function createGroup(): Fieldset
    {
        $this->current++;
        $this->fields[$this->current] = [];
        return $this;
    }

    /**
     * Method to add a form field
     *
     * @param  AbstractElement $field
     * @return Fieldset
     */
    public function addField(AbstractElement $field): Fieldset
    {
        if (!isset($this->fields[$this->current])) {
            $this->fields[$this->current] = [];
        }
        $this->fields[$this->current][$field->getName()] = $field;
        return $this;
    }

    /**
     * Method to add form fields
     *
     * @param  array $fields
     * @return Fieldset
     */
    public function addFields(array $fields): Fieldset
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
        return $this;
    }

    /**
     * Method to insert a field before another one
     *
     * @param  string          $name
     * @param  AbstractElement $field
     * @return Fieldset
     */
    public function insertFieldBefore(string $name, AbstractElement $field): Fieldset
    {
        foreach ($this->fields as $i => $group) {
            $fields = [];
            foreach ($group as $key => $value) {
                if ($key == $name) {
                    $fields[$field->getName()] = $field;
                    $fields[$key] = $value;
                } else {
                    $fields[$key] = $value;
                }
            }
            $this->fields[$i] = $fields;
        }

        return $this;
    }

    /**
     * Method to insert a field after another one
     *
     * @param  string          $name
     * @param  AbstractElement $field
     * @return Fieldset
     */
    public function insertFieldAfter(string $name, AbstractElement $field): Fieldset
    {
        foreach ($this->fields as $i => $group) {
            $fields = [];
            foreach ($group as $key => $value) {
                if ($key == $name) {
                    $fields[$key] = $value;
                    $fields[$field->getName()] = $field;
                } else {
                    $fields[$key] = $value;
                }
            }
            $this->fields[$i] = $fields;
        }

        return $this;
    }

    /**
     * Method to get the count of elements in the form fieldset
     *
     * @return int
     */
    public function count(): int
    {
        $count = 0;
        foreach ($this->fields as $group) {
            $count += count($group);
        }
        return $count;
    }

    /**
     * Method to get the field values as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $fieldValues = [];

        foreach ($this->fields as $group) {
            foreach ($group as $name => $field) {
                $fieldValues[$name] = $field->getValue();
            }
        }

        return $fieldValues;
    }

    /**
     * Method to get fieldset legend
     *
     * @return string|null
     */
    public function getLegend(): string|null
    {
        return $this->legend;
    }

    /**
     * Method to get container
     *
     * @return string
     */
    public function getContainer(): string
    {
        return $this->container;
    }

    /**
     * Method to get current group index
     *
     * @return int
     */
    public function getCurrent(): int
    {
        return $this->current;
    }

    /**
     * Method to determine if the fieldset has a field
     *
     * @param  string $name
     * @return bool
     */
    public function hasField(string $name): bool
    {
        $result = false;
        foreach ($this->fields as $key => $fields) {
            if (isset($fields[$name])) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     * Method to get a field element object
     *
     * @param  string $name
     * @return AbstractElement
     */
    public function getField(string $name): AbstractElement
    {
        $result = null;
        foreach ($this->fields as $key => $fields) {
            if (isset($fields[$name])) {
                $result = $fields[$name];
            }
        }
        return $result;
    }

    /**
     * Method to get field element objects in a group
     *
     * @param  int  $i
     * @return array|null
     */
    public function getFields(int $i): array|null
    {
        return $this->fields[$i] ?? null;
    }

    /**
     * Method to get all field element groups
     *
     * @return array
     */
    public function getFieldGroups(): array
    {
        return $this->fields;
    }

    /**
     * Method to get all field elements
     *
     * @return array
     */
    public function getAllFields(): array
    {
        $fields = [];
        foreach ($this->fields as $group) {
            foreach ($group as $field) {
                $fields[$field->getName()] = $field;
            }
        }
        return $fields;
    }

    /**
     * Method to get a field element value
     *
     * @param  string $name
     * @return mixed
     */
    public function getFieldValue(string $name): mixed
    {
        $result = null;
        foreach ($this->fields as $key => $fields) {
            if (isset($fields[$name])) {
                $result = $this->fields[$key][$name]->getValue();
            }
        }
        return $result;
    }

    /**
     * Method to set fieldset legend
     *
     * @param  string $legend
     * @return Fieldset
     */
    public function setLegend(string $legend): Fieldset
    {
        $this->legend = $legend;
        return $this;
    }

    /**
     * Method to set a field element value
     *
     * @param  string $name
     * @param  mixed  $value
     * @return Fieldset
     */
    public function setFieldValue(string $name, mixed $value): Fieldset
    {
        foreach ($this->fields as $key => $fields) {
            if (isset($fields[$name])) {
                $this->fields[$key][$name]->setValue($value);
            }
        }
        return $this;
    }

    /**
     * Method to set field element values
     *
     * @param  array $values
     * @return Fieldset
     */
    public function setFieldValues(array $values): Fieldset
    {
        foreach ($values as $name => $value) {
            $this->setFieldValue($name, $value);
        }
        return $this;
    }

    /**
     * Method to iterate over the form elements
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Prepare fieldset object for rendering
     *
     * @return Fieldset
     */
    public function prepare(): Fieldset
    {
        if (!empty($this->legend)) {
            $this->addChild(new Child('legend', $this->legend));
        }

        switch ($this->container) {
            case 'table':
                $this->prepareTable();
                break;
            case 'dl':
                $this->prepareDl();
                break;
            default:
                $this->prepareElement($this->container);
        }

        return $this;
    }

    /**
     * Prepare fieldset elements for rendering with a view
     *
     * @return array
     */
    public function prepareForView(): array
    {
        $fields = [];

        foreach ($this->fields as $groups) {
            foreach ($groups as $field) {
                if ($field->hasLabel()) {
                    $labelFor = $field->getName() . (($field->getNodeName() == 'fieldset') ? '1' : '');
                    $label    = new Child('label', $field->getLabel());
                    $label->setAttribute('for', $labelFor);
                    if ($field->getLabelAttributes() !== null) {
                        $label->setAttributes($field->getLabelAttributes());
                    }
                    if ($field->isRequired()) {
                        if ($label->hasAttribute('class')) {
                            $label->setAttribute('class', $label->getAttribute('class') . ' required');
                        } else {
                            $label->setAttribute('class', 'required');
                        }
                    }
                    $fields[$field->getName() . '_label'] = $label->render();
                }

                if ($field->hasHint()) {
                    $hint = new Child('span', $field->getHint());
                    if ($field->getHintAttributes() !== null) {
                        $hint->setAttributes($field->getHintAttributes());
                    }
                    $fields[$field->getName() . '_hint'] = $hint->render();
                }

                if ($field->hasErrors()) {
                    $fields[$field->getName() . '_errors'] = $field->getErrors();
                }

                $fields[$field->getName()] = $field->render();
            }
        }

        return $fields;
    }

    /**
     * Prepare table
     *
     * @return void
     */
    protected function prepareTable(): void
    {
        foreach ($this->fields as $fields) {
            $table = new Child('table');

            foreach ($fields as $field) {
                $errors = [];
                if ($field->hasErrors()) {
                    foreach ($field->getErrors() as $error) {
                        $errors[] = (new Child('div', $error))->setAttribute('class', 'error');
                    }
                }

                $tr = new Child('tr');
                if ($field->hasLabel()) {
                    $td = new Child('td');
                    $labelFor = $field->getName() . (($field->getNodeName() == 'fieldset') ? '1' : '');

                    $label = new Child('label', $field->getLabel());
                    $label->setAttribute('for', $labelFor);
                    if ($field->hasLabelAttributes()) {
                        $label->setAttributes($field->getLabelAttributes());
                    }
                    if ($field->isRequired()) {
                        if ($label->hasAttribute('class')) {
                            $label->setAttribute('class', $label->getAttribute('class') . ' required');
                        } else {
                            $label->setAttribute('class', 'required');
                        }
                    }
                    $td->addChild($label);
                    $tr->addChild($td);
                }

                $nodeValue = null;
                $options   = [];
                if ($field->hasAppend()) {
                    $nodeValue = $field->getAppend();
                    $options   = ['childrenFirst' => true];
                } else if ($field->hasPrepend()) {
                    $nodeValue = $field->getPrepend();
                    $options   = ['childrenFirst' => false];
                }

                $td = new Child('td', $nodeValue, $options);

                if ($field->isErrorPre()) {
                    $td->addChildren($errors);
                }
                $td->addChild($field);

                if ($field->hasHint()) {
                    $hint = new Child('span', $field->getHint());
                    if ($field->hasHintAttributes()) {
                        $hint->setAttributes($field->getHintAttributes());
                    }
                    $td->addChild($hint);
                }

                if ($field->hasLabel()) {
                    $td->setAttribute('colspan', 2);
                }
                if (!$field->isErrorPre()) {
                    $td->addChildren($errors);
                }
                $tr->addChild($td);
                $table->addChild($tr);
            }

            $this->addChild($table);
        }
    }

    /**
     * Prepare DIV or P
     *
     * @param  string $element
     * @return void
     */
    protected function prepareElement(string $element): void
    {
        foreach ($this->fields as $fields) {
            foreach ($fields as $field) {
                $errors = [];
                if ($field->hasErrors()) {
                    foreach ($field->getErrors() as $error) {
                        $errors[] = (new Child('div', $error))->setAttribute('class', 'error');
                    }
                }

                $nodeValue = null;
                $options   = [];
                if ($field->hasAppend()) {
                    $nodeValue = $field->getAppend();
                    $options   = ['childrenFirst' => true];
                } else if ($field->hasPrepend()) {
                    $nodeValue = $field->getPrepend();
                    $options   = ['childrenFirst' => false];
                }

                $container = new Child($element, $nodeValue, $options);

                if ($field->hasLabel()) {
                    $labelFor = $field->getName() . (($field->getNodeName() == 'fieldset') ? '1' : '');
                    $label    = new Child('label', $field->getLabel());
                    $label->setAttribute('for', $labelFor);
                    if ($field->hasLabelAttributes()) {
                        $label->setAttributes($field->getLabelAttributes());
                    }
                    if ($field->isRequired()) {
                        if ($label->hasAttribute('class')) {
                            $label->setAttribute('class', $label->getAttribute('class') . ' required');
                        } else {
                            $label->setAttribute('class', 'required');
                        }
                    }
                    $container->addChild($label);
                }

                if ($field->isErrorPre()) {
                    $container->addChildren($errors);
                }
                $container->addChild($field);

                if ($field->hasHint()) {
                    $hint = new Child('span', $field->getHint());
                    if ($field->hasHintAttributes()) {
                        $hint->setAttributes($field->getHintAttributes());
                    }
                    $container->addChild($hint);
                }
                if (!$field->isErrorPre()) {
                    $container->addChildren($errors);
                }
                $this->addChild($container);
            }
        }
    }

    /**
     * Prepare DL
     *
     * @return void
     */
    protected function prepareDl(): void
    {
        foreach ($this->fields as $fields) {
            $dl = new Child('dl');

            foreach ($fields as $field) {
                $errors = [];
                if ($field->hasErrors()) {
                    foreach ($field->getErrors() as $error) {
                        $errors[] = (new Child('div', $error))->setAttribute('class', 'error');
                    }
                }

                if ($field->hasLabel()) {
                    $dt = new Child('dt');
                    $labelFor = $field->getName() . (($field->getNodeName() == 'fieldset') ? '1' : '');

                    $label = new Child('label', $field->getLabel());
                    $label->setAttribute('for', $labelFor);
                    if ($field->hasLabelAttributes()) {
                        $label->setAttributes($field->getLabelAttributes());
                    }
                    if ($field->isRequired()) {
                        if ($label->hasAttribute('class')) {
                            $label->setAttribute('class', $label->getAttribute('class') . ' required');
                        } else {
                            $label->setAttribute('class', 'required');
                        }
                    }
                    $dt->addChild($label);
                    $dl->addChild($dt);
                }

                $nodeValue = null;
                $options   = [];
                if ($field->hasAppend()) {
                    $nodeValue = $field->getAppend();
                    $options   = ['childrenFirst' => true];
                } else if ($field->hasPrepend()) {
                    $nodeValue = $field->getPrepend();
                    $options   = ['childrenFirst' => false];
                }

                $dd = new Child('dd', $nodeValue, $options);

                if ($field->isErrorPre()) {
                    $dd->addChildren($errors);
                }
                $dd->addChild($field);

                if ($field->hasHint()) {
                    $hint = new Child('span', $field->getHint());
                    if ($field->hasHintAttributes()) {
                        $hint->setAttributes($field->getHintAttributes());
                    }
                    $dd->addChild($hint);
                }
                if (!$field->isErrorPre()) {
                    $dd->addChildren($errors);
                }
                $dl->addChild($dd);
            }

            $this->addChild($dl);
        }
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
        return $this->hasField($name);
    }

    /**
     * Unset fields[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        foreach ($this->fields as $i => $group) {
            foreach ($group as $key => $value) {
                if ($key == $name) {
                    unset($this->fields[$i][$key]);
                }
            }
        }
    }

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get($offset);
    }

    /**
     * ArrayAccess offsetSet
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }

}
