<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form;

use Pop\Dom\Child;

/**
 * Form class
 *
 * @category   Pop
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.1
 */
class Form extends AbstractForm
{

    /**
     * Constructor
     *
     * Instantiate the form object
     *
     * @param  array  $fields
     * @param  string $action
     * @param  string $method
     * @return Form
     */
    public function __construct(array $fields = null, $action = null, $method = 'post')
    {
        parent::__construct('form');

        $this->setAttributes([
            'action' => ((null !== $action) ? $action : $_SERVER['REQUEST_URI']),
            'method' => $method
        ]);

        if (null !== $fields) {
            $this->setFieldConfig($fields);
            $this->setFieldValues();
        }
    }

    /**
     * Set the field config
     *
     * @param  array $fields
     * @return Form
     */
    public function setFieldConfig(array $fields)
    {
        // If group fields config
        if ($this->isFieldGroupConfig($fields)) {
            $this->fieldGroupConfig = (count($this->fieldGroupConfig) > 0) ? array_merge($this->fieldGroupConfig, $fields) : $fields;
            foreach ($fields as $field) {
                foreach ($field as $name => $value) {
                    $field[$name]['name'] = $name;
                    $this->fields[$name] = (isset($value['value'])) ? $value['value'] : null;
                    if ($field[$name]['type'] == 'file') {
                        $this->hasFile = true;
                    }
                }
            }
        // Else, if fields config
        } else {
            foreach ($fields as $name => $value) {
                $fields[$name]['name'] = $name;
                $this->fields[$name] = (isset($value['value'])) ? $value['value'] : null;
                if ($fields[$name]['type'] == 'file') {
                    $this->hasFile = true;
                }
            }
        }

        $this->fieldConfig = (count($this->fieldConfig) > 0) ? array_merge($this->fieldConfig, $fields) : $fields;
        return $this;
    }

    /**
     * Add a single field config
     *
     * @param  string $name
     * @param  array  $field
     * @return Form
     */
    public function addFieldConfig($name, array $field)
    {
        $match = false;
        if (array_key_exists($name, $this->fieldConfig)) {
            $this->fieldConfig[$name] = $field;
            $match = true;
        } else {
            foreach ($this->fieldConfig as $key => $value) {
                if (array_key_exists($name, $value)) {
                    $this->fieldConfig[$key][$name] = $field;
                    $match = true;
                }
            }
        }

        if (!$match) {
            $keys = array_keys($this->fieldConfig);
            if (is_numeric($keys[0])) {
                $last = $keys[(count($keys) - 1)];
                $this->fieldConfig[$last][$name] = $field;
            } else {
                $this->fieldConfig[$name] = $field;
            }
        }

        $this->childNodes = [];
        $this->setFieldValues();

        return $this;
    }

    /**
     * Add multiple field configs
     *
     * @param  array  $fields
     * @return Form
     */
    public function addFieldConfigs(array $fields)
    {
        foreach ($fields as $name => $field) {
            $this->addFieldConfig($name, $field);
        }
        return $this;
    }

    /**
     * Insert a field config before another field config
     *
     * @param  string $before
     * @param  string $name
     * @param  array  $field
     * @throws Exception
     * @return Form
     */
    public function insertFieldConfigBefore($before, $name, array $field)
    {
        if (!array_key_exists($before, $this->fieldConfig)) {
            throw new Exception('Error: That field does not exist.');
        }
        $keys = array_keys($this->fieldConfig);
        $i    = array_search($before, $keys);
        $keys = array_merge(array_slice($keys, 0, $i), [$name], array_slice($keys, $i));

        $fields = [];

        foreach ($keys as $key) {
            $fields[$key] = ($key == $name) ? $field : $this->fieldConfig[$key];
        }

        $this->childNodes = [];
        $this->fieldConfig = $fields;
        $this->setFieldConfig($fields);
        $this->setFieldValues();

        // If the element is the top of element of a group, switch out for the new element being inserted before
        foreach ($this->groups as $key => $group) {
            if ($group == $before) {
                $this->groups[$key] = $name;
            }
        }

        return $this;
    }

    /**
     * Insert a field config before another field config
     *
     * @param  string $after
     * @param  string $name
     * @param  array  $field
     * @throws Exception
     * @return Form
     */
    public function insertFieldConfigAfter($after, $name, array $field)
    {
        if (!array_key_exists($after, $this->fieldConfig)) {
            throw new Exception('Error: That field does not exist.');
        }
        $keys = array_keys($this->fieldConfig);
        $i    = array_search($after, $keys);
        $keys = array_merge(array_slice($keys, 0, $i + 1), [$name], array_slice($keys, $i + 1));

        $fields = [];

        foreach ($keys as $key) {
            $fields[$key] = ($key == $name) ? $field : $this->fieldConfig[$key];
        }

        $this->childNodes = [];
        $this->fieldConfig = $fields;
        $this->setFieldConfig($fields);
        $this->setFieldValues();

        return $this;
    }

    /**
     * Insert a field group config before another field group config
     *
     * @param  string $beforeIndex
     * @param  array  $fieldGroup
     * @throws Exception
     * @return Form
     */
    public function insertGroupConfigBefore($beforeIndex, array $fieldGroup)
    {
        if (!isset($this->fieldGroupConfig[$beforeIndex])) {
            throw new Exception('Error: That field group does not exist.');
        }
        if ($beforeIndex == 0) {
            $this->fieldGroupConfig = array_merge($fieldGroup, $this->fieldGroupConfig);
        } else {
            array_splice($this->fieldGroupConfig, $beforeIndex, 0, $fieldGroup);
        }

        $this->childNodes  = [];
        $this->fieldConfig = [];
        $this->groups      = [];
        $this->setFieldConfig($this->fieldGroupConfig);
        $this->setFieldValues();
    }

    /**
     * Insert a field group config after another field group config
     *
     * @param  string $afterIndex
     * @param  array  $fieldGroup
     * @throws Exception
     * @return Form
     */
    public function insertGroupConfigAfter($afterIndex, array $fieldGroup)
    {
        if (!isset($this->fieldGroupConfig[$afterIndex])) {
            throw new Exception('Error: That field group does not exist.');
        }
        $afterIndex++;
        if (($afterIndex - 1) == (count($this->fieldGroupConfig) - 1)) {
            $this->fieldGroupConfig = array_merge($this->fieldGroupConfig, $fieldGroup);
        } else {
            array_splice($this->fieldGroupConfig, $afterIndex, 0, $fieldGroup);
        }

        $this->childNodes  = [];
        $this->fieldConfig = [];
        $this->groups      = [];
        $this->setFieldConfig($this->fieldGroupConfig);
        $this->setFieldValues();

    }

    /**
     * Set the field values. Optionally, you can apply filters
     * to the passed values via callbacks and their parameters
     *
     * @param  array $values
     * @return Form
     */
    public function setFieldValues(array $values = null)
    {
        // Filter values if passed
        if (null !== $values) {
            $values = $this->filterValues($values);
        }

        // If no fields have been created yet, create the fields assigning the field values
        if ((count($this->getChildren()) == 0) && (count($this->fieldConfig) > 0)) {
            $this->createFields($values);
        // Else, set the field values for the already existing fields
        } else {
            $fields       = $this->getElements();
            $fieldsPassed = [];
            if ((null !== $values) && (count($fields) > 0)) {
                foreach ($fields as $field) {
                    $fieldName = str_replace('[]', '', $field->getName());
                    if (isset($values[$fieldName])) {
                        // If a multi-value form element
                        if ($field->hasChildren()) {
                            $field->setMarked($values[$fieldName]);
                            $this->fields[$fieldName] = $values[$fieldName];
                            $fieldsPassed[]           = $fieldName;
                            // Loop through the field's children
                            if ($field->hasChildren()) {
                                $children = $field->getChildren();
                                foreach ($children as $key => $child) {
                                    // If checkbox or radio
                                    if (($child->getAttribute('type') == 'checkbox') || ($child->getAttribute('type') == 'radio')) {
                                        if (is_array($field->getMarked()) && in_array($child->getAttribute('value'), $field->getMarked())) {
                                            $field->getChild($key)->setAttribute('checked', 'checked');
                                        } else if ($child->getAttribute('value') == $field->getMarked()) {
                                            $field->getChild($key)->setAttribute('checked', 'checked');
                                        } else {
                                            $field->getChild($key)->removeAttribute('checked');
                                        }
                                    // If select option
                                    } else if ($child->getNodeName() == 'option') {
                                        if (is_array($field->getMarked()) && in_array($child->getAttribute('value'), $field->getMarked())) {
                                            $field->getChild($key)->setAttribute('selected', 'selected');
                                        } else if ($child->getAttribute('value') == $field->getMarked()) {
                                            $field->getChild($key)->setAttribute('selected', 'selected');
                                        } else {
                                            $field->getChild($key)->removeAttribute('selected');
                                        }
                                    // If select option group
                                    } else if ($child->getNodeName() == 'optgroup') {
                                        $cdrn = $child->getChildren();
                                        foreach ($cdrn as $k => $c) {
                                            if (is_array($field->getMarked()) && in_array($c->getAttribute('value'), $field->getMarked())) {
                                                $child->getChild($k)->setAttribute('selected', 'selected');
                                            } else if ($c->getAttribute('value') == $field->getMarked()) {
                                                $child->getChild($k)->setAttribute('selected', 'selected');
                                            } else {
                                                $child->getChild($k)->removeAttribute('selected');
                                            }
                                        }
                                    }
                                }
                            }
                        // Else, if a single-value form element
                        } else {
                            $fieldValue = ($field instanceof Element\Input\Captcha) ?
                                strtoupper($values[$fieldName]) : $values[$fieldName];
                            $field->setValue($fieldValue);
                            $this->fields[$fieldName] = $fieldValue;
                            $fieldsPassed[] = $fieldName;
                            if ($field->getNodeName() == 'textarea') {
                                $field->setNodeValue($fieldValue);
                            } else {
                                $field->setAttribute('value', $fieldValue);
                                if (($field instanceof Element\Input\Checkbox) || ($field instanceof Element\Input\Radio)) {
                                    $field->setAttribute('checked', 'checked');
                                }
                            }
                        }
                    }
                }
            }

        }

        if (null !== $this->errorDisplay) {
            $this->setErrorDisplay(
                $this->errorDisplay['container'],
                $this->errorDisplay['attributes'],
                $this->errorDisplay['pre']
            );
        }

        return $this;
    }

    /**
     * Insert a form element before another element
     *
     * @param  string                  $name
     * @param  Element\AbstractElement $e
     * @throws Exception
     * @return Form
     */
    public function insertElementBefore($name, Element\AbstractElement $e)
    {
        $i = $this->getElementIndex($name);
        if (null === $i) {
            throw new Exception('Error: That element does not exist.');
        }

        // If the element is the top of element of a group, switch out for the new element being inserted before
        foreach ($this->groups as $key => $group) {
            if ($group == $name) {
                $this->groups[$key] = $e->getName();
            }
        }

        $this->childNodes = array_merge(array_slice($this->childNodes, 0, $i), [$e], array_slice($this->childNodes, $i));

        return $this;
    }

    /**
     * Insert a form element after another element
     *
     * @param  string                  $name
     * @param  Element\AbstractElement $e
     * @throws Exception
     * @return Form
     */
    public function insertElementAfter($name, Element\AbstractElement $e)
    {
        $i = $this->getElementIndex($name);
        if (null === $i) {
            throw new Exception('Error: That element does not exist.');
        }

        $this->childNodes = array_merge(array_slice($this->childNodes, 0, $i + 1), [$e], array_slice($this->childNodes, $i + 1));

        return $this;
    }

    /**
     * Add a form element or elements to the form object.
     *
     * @param  Element\AbstractElement $e
     * @return Form
     */
    public function addElement(Element\AbstractElement $e)
    {
        return $this->addElements([$e]);
    }

    /**
     * Add a form element or elements to the form object.
     *
     * @param  array $e
     * @throws Exception
     * @return Form
     */
    public function addElements(array $e)
    {
        foreach ($e as $c) {
            if (!($c instanceof Element\AbstractElement)) {
                throw new Exception('Error: One of the elements passed is not an instance of Pop\\Form\\Element\\AbstractElement.');
            }
        }

        $this->addChildren($e);
        $children = $this->getChildren();

        foreach ($children as $child) {
            $attribs = $child->getAttributes();
            if (($child instanceof Element\Textarea) || ($child instanceof Element\Button)) {
                if (isset($attribs['name'])) {
                    $this->fields[$attribs['name']] = ((null !== $child->getValue()) ? $child->getValue() : null);
                }
            } else if ($child instanceof Element\Select) {
                if (isset($attribs['name'])) {
                    $name = (strpos($attribs['name'], '[]') !== false) ? substr($attribs['name'], 0, strpos($attribs['name'], '[]')) : $attribs['name'];
                    $this->fields[$name] = ((null !== $child->getMarked()) ? $child->getMarked() : null);
                }
            } else if ($child instanceof Element\RadioSet) {
                $radioChildren = $child->getChildren();
                if (isset($radioChildren[0])) {
                    $childAttribs = $radioChildren[0]->getAttributes();
                    if (isset($childAttribs['name'])) {
                        $this->fields[$childAttribs['name']] = ((null !== $child->getMarked()) ? $child->getMarked() : null);
                    }
                }
            } else if ($child instanceof Element\CheckboxSet) {
                $checkChildren = $child->getChildren();
                if (isset($checkChildren[0])) {
                    $childAttribs = $checkChildren[0]->getAttributes();
                    if (isset($childAttribs['name'])) {
                        $key = str_replace('[]', '', $childAttribs['name']);
                        $this->fields[$key] = ((null !== $child->getMarked()) ? $child->getMarked() : null);
                    }
                }
            } else {
                if (isset($attribs['name'])) {
                    $this->fields[$attribs['name']] = (isset($attribs['value']) ? $attribs['value'] : null);
                    if ($attribs['type'] == 'file') {
                        $this->hasFile = true;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Alias method to getElements())
     *
     * @return array
     */
    public function elements()
    {
        return $this->getElements();
    }

    /**
     * Get the elements of the form object.
     *
     * @return array
     */
    public function getElements()
    {
        $children = $this->getChildren();
        $elements = [];

        foreach ($children as $child) {
            if ($child instanceof Element\AbstractElement){
                $elements[] = $child;
            }
        }

        return $elements;
    }

    /**
     * Alias method to getElement()
     *
     * @param string $elementName
     * @return Element\AbstractElement
     */
    public function element($elementName)
    {
        return $this->getElement($elementName);
    }

    /**
     * Get an element object of the form by name.
     *
     * @param string $elementName
     * @return Element\AbstractElement
     */
    public function getElement($elementName)
    {
        $i = $this->getElementIndex($elementName);
        return (null !== $i) ? $this->getChild($this->getElementIndex($elementName)) : null;
    }

    /**
     * Get the index of an element object of the form by name.
     *
     * @param string $elementName
     * @return int
     */
    public function getElementIndex($elementName)
    {
        $name  = null;
        $elem  = null;
        $index = null;
        $elems = $this->getChildren();

        foreach ($elems as $i => $e) {
            if ($e->getNodeName() == 'fieldset') {
                $children = $e->getChildren();
                foreach ($children as $c) {
                    if ($c->getNodeName() == 'input') {
                        $attribs = $c->getAttributes();
                        $name = str_replace('[]', '', $attribs['name']);
                    }
                }
            } else {
                $attribs = $e->getAttributes();
                $name = $attribs['name'];
            }
            if ($name == $elementName) {
                $index = $i;
            }
        }

        return $index;
    }

    /**
     * Remove a form element
     *
     * @param string $elementName
     * @return $this
     */
    public function removeElement($elementName)
    {
        $i = $this->getElementIndex($elementName);

        $newInitValues = [];
        $keys = array_keys($this->fieldConfig);

        if (isset($keys[0]) && is_numeric($keys[0])) {
            foreach ($this->fieldConfig as $fields) {
                $newInitValuesAry = [];
                foreach ($fields as $name => $field) {
                    if (isset($name) && ($name == $elementName)) {
                        unset($fields[$name]);
                    } else {
                        $newInitValuesAry[$name] = $field;
                    }
                }
                $newInitValues[] = $newInitValuesAry;
            }
        } else {
            foreach ($this->fieldConfig as $name => $field) {
                if (isset($name) && ($name == $elementName)) {
                    unset($this->fieldConfig[$name]);
                } else {
                    $newInitValues[$name] = $field;
                }
            }
        }
        $this->fieldConfig = $newInitValues;

        if (isset($this->fields[$elementName])) {
            unset($this->fields[$elementName]);
        }

        if (null !== $i) {
            $this->removeChild($i);
        }

        return $this;
    }

    /**
     * Determine whether or not the form object is valid and return the result.
     *
     * @return boolean
     */
    public function isValid()
    {
        $noErrors = true;
        $children = $this->getChildren();

        // Check each element for validators, validate them and return the result.
        foreach ($children as $child) {
            if ($child->validate($this->fields) == false) {
                $noErrors = false;
            }
        }

        return $noErrors;
    }

    /**
     * Set error pre-display globally across all form element objects
     *
     * @param  string  $container
     * @param  array   $attribs
     * @param  boolean $pre
     * @return Form
     */
    public function setErrorDisplay($container, array $attribs, $pre = false)
    {
        if (null === $this->errorDisplay) {
            $this->errorDisplay = [
                'container'  => 'div',
                'attributes' => ['class' => 'error'],
                'pre'        => false
            ];
        }

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->setErrorDisplay($container, $attribs, $pre);
        }

        $this->errorDisplay['container']  = $container;
        $this->errorDisplay['attributes'] = $attribs;
        $this->errorDisplay['pre']        = $pre;

        return $this;
    }

    /**
     * Check if form has errors
     *
     * @param  string $field
     * @return boolean
     */
    public function hasErrors($field = null)
    {
        return (count($this->getErrors($field)) > 0);
    }

    /**
     * Get all form element errors.
     *
     * @param  string $field
     * @return array
     */
    public function getErrors($field = null)
    {
        $errors   = [];
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->hasErrors()) {
                $errors[str_replace('[]', '', $element->getName())] = $element->getErrors();
            }
        }

        if (null !== $field) {
            return (isset($errors[$field])) ? $errors[$field] : [];
        } else {
            return $errors;
        }
    }

    /**
     * Render the form object either using the defined template or
     * by a basic 1:1 DT/DD tag structure.
     *
     * @param  boolean $ret
     * @throws Exception
     * @return mixed
     */
    public function renderForm($ret = false)
    {
        // Check to make sure form elements exist.
        if ((count($this->getChildren()) == 0) && (count($this->fieldConfig) == 0)) {
            throw new Exception('Error: There are no form elements declared for this form object.');
        } else if ((count($this->getChildren()) == 0) && (count($this->fieldConfig) > 0)) {
            $this->createFields();
        }

        // If the form has a file field
        if ($this->hasFile) {
            $this->setAttribute('enctype', 'multipart/form-data');
        }

        // Render the form template
        $this->output = (null !== $this->template) ?
            $this->template->render($this) : $this->renderWithoutTemplate();

        // Return or print the form output.
        if ($ret) {
            return $this->output;
        } else {
            echo $this->output;
        }
    }

    /**
     * Filter of field values with the filters that have been set
     *
     * @return Form
     */
    public function filter()
    {
        $this->filterValues();
        return $this;
    }

    /**
     * Method to create the form fields
     *
     * @param  array $values
     * @throws Exception
     * @return void
     */
    protected function createFields(array $values = null)
    {
        // Loop through the field config and build the fields and build the fields
        if (count($this->fieldConfig) > 0) {
            // If the fields are a group of fields
            $keys = array_keys($this->fieldConfig);
            if (is_numeric($keys[0])) {
                $fields = [];
                foreach ($this->fieldConfig as $ary) {
                    $k = array_keys($ary);
                    if (isset($k[0])) {
                        $this->groups[] = $k[0];
                    }
                    $fields = array_merge($fields, $ary);
                }
                $this->fieldConfig = $fields;
            }

            foreach ($this->fieldConfig as $name => $field) {
                if (is_array($field) && isset($field['type'])) {
                    if ($field['type'] == 'file') {
                        $this->hasFile = true;
                    }
                    $this->addElement(Fields::factory($name, $field, $values));
                }
            }
        }
    }

    /**
     * Method to determine if the field config passed is a field group
     *
     * @param  array $fields
     * @return boolean
     */
    protected function isFieldGroupConfig(array $fields)
    {
        $result = false;

        foreach ($fields as $key => $value) {
            if (is_array($value) && !isset($value['type'])) {
                $values = array_values($value);
                $val    = array_shift($values);
                if (is_array($val) && isset($val['type'])) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Method to render the form using a basic 1:1 DT/DD layout
     *
     * @return string
     */
    protected function renderWithoutTemplate()
    {
        // Initialize properties.
        $this->output = null;
        $children     = $this->getChildren();
        $this->removeChildren();

        $id = (null !== $this->getAttribute('id')) ? $this->getAttribute('id') . '-field-group' : 'pop-form-field-group';

        // Create DL element.
        $i = 1;
        $dl = new Child('dl', null, null, false, $this->getIndent());
        $dl->setAttribute('id', $id . '-' . $i);
        $dl->setAttribute('class', $id);

        // Loop through the children and create and attach the appropriate DT and DT elements, with labels where applicable.
        foreach ($children as $child) {
            if ($child->getNodeName() == 'fieldset') {
                $chdrn = $child->getChildren();
                if (isset($chdrn[0])) {
                    $attribs = $chdrn[0]->getAttributes();
                }
            } else {
                $attribs = $child->getAttributes();
            }

            $name = (isset($attribs['name'])) ? $attribs['name'] : '';
            $name = str_replace('[]', '', $name);

            if (count($this->groups) > 0) {
                if (isset($this->groups[$i]) && ($this->groups[$i] == $name)) {
                    $this->addChild($dl);
                    $i++;
                    $dl = new Child('dl', null, null, false, $this->getIndent());
                    $dl->setAttribute('id', $id . '-' . $i);
                    $dl->setAttribute('class', $id);
                }
            }

            // Clear the password field from display.
            if ($child->getAttribute('type') == 'password') {
                $child->setValue(null);
                $child->setAttribute('value', null);
            }

            // If the element label is set, render the appropriate DT and DD elements.
            if (($child instanceof Element\AbstractElement) && (null !== $child->getLabel())) {
                // Create the DT and DD elements.
                $dt = new Child('dt', null, null, false, ($this->getIndent() . '    '));
                $dd = new Child('dd', null, null, false, ($this->getIndent() . '    '));

                // Format the label name.
                $lblName = ($child->getNodeName() == 'fieldset') ? '1' : '';
                $label   = new Child('label', $child->getLabel(), null, false, ($this->getIndent() . '        '));
                $label->setAttribute('for', ($name . $lblName));

                $labelAttributes = $child->getLabelAttributes();
                if (count($labelAttributes) > 0) {
                    foreach ($labelAttributes as $a => $v) {
                        if (($a == 'class') && ($child->isRequired())) {
                            $v .= ' required';
                        }
                        $label->setAttribute($a, $v);
                    }
                } else if ($child->isRequired()) {
                    $label->setAttribute('class', 'required');
                }

                // Add the appropriate children to the appropriate elements.
                $dt->addChild($label);
                $child->setIndent(($this->getIndent() . '        '));
                $childChildren = $child->getChildren();
                $child->removeChildren();

                foreach ($childChildren as $cChild) {
                    $cChild->setIndent(($this->getIndent() . '            '));
                    $child->addChild($cChild);
                }

                $dd->addChild($child);

                if (null !== $child->getHint()) {
                    $hint = new Child('span', $child->getHint(), null, false, ($this->getIndent() . '        '));
                    if (count($child->getHintAttributes()) > 0) {
                        $hint->setAttributes($child->getHintAttributes());
                    }
                    $dd->addChild($hint);
                }

                $dl->addChildren([$dt, $dd]);
            // Else, render only a DD element.
            } else {
                $dd = new Child('dd', null, null, false, ($this->getIndent() . '    '));
                $child->setIndent(($this->getIndent() . '        '));
                $dd->addChild($child);

                if (($child instanceof Element\AbstractElement) && (null !== $child->getHint())) {
                    $hint = new Child('span', $child->getHint(), null, false, ($this->getIndent() . '        '));
                    if (count($child->getHintAttributes()) > 0) {
                        $hint->setAttributes($child->getHintAttributes());
                    }
                    $dd->addChild($hint);
                }

                $dl->addChild($dd);
            }
        }

        // Add the DL element and its children to the form element.
        $this->addChild($dl);
        return parent::render(true);
    }

    /**
     * Output the form object as a string
     *
     * @return string
     */

    public function __toString()
    {
        return $this->renderForm(true);
    }

}
