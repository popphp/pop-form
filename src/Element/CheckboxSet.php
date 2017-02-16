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
namespace Pop\Form\Element;

use Pop\Dom\Child;

/**
 * Form checkbox element set class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */

class CheckboxSet extends AbstractElement
{

    /**
     * Array of checkbox input elements
     * @var array
     */
    protected $checkboxes = [];

    /**
     * Array of checked values
     * @var array
     */
    protected $checked = [];

    /**
     * Fieldset legend
     * @var string
     */
    protected $legend = null;

    /**
     * Constructor
     *
     * Instantiate a fieldset of checkbox input form elements
     *
     * @param  string       $name
     * @param  array        $values
     * @param  string|array $checked
     * @param  string       $indent
     */
    public function __construct($name, array $values, $checked = null, $indent = null)
    {
        parent::__construct('fieldset');

        $this->setName($name);
        $this->setAttribute('class', 'checkbox-fieldset');

        if (null !== $checked) {
            $this->setValue($checked);
        }

        if (null !== $indent) {
            $this->setIndent($indent);
        }

        // Create the checkbox elements and related span elements.
        $i = null;
        foreach ($values as $k => $v) {
            $checkbox = new Input\Checkbox($name . '[]', null, $indent);
            $checkbox->setAttributes([
                'class' => 'checkbox',
                'id'    => ($name . $i),
                'value' => $k
            ]);

            // Determine if the current radio element is checked.
            if (in_array($k, $this->checked)) {
                $checkbox->check();
            }

            $span = new Child('span');
            if (null !== $indent) {
                $span->setIndent($indent);
            }
            $span->setAttribute('class', 'checkbox-span');
            $span->setNodeValue($v);
            $this->addChildren([$checkbox, $span]);
            $this->checkboxes[] = $checkbox;
            $i++;
        }
    }

    /**
     * Set an attribute for the input checkbox elements
     *
     * @param  string $a
     * @param  string $v
     * @return Child
     */
    public function setCheckboxAttribute($a, $v)
    {
        foreach ($this->checkboxes as $checkbox) {
            $checkbox->setAttribute($a, $v);
            if ($a == 'tabindex') {
                $v++;
            }

        }
        return $this;
    }

    /**
     * Set an attribute or attributes for the input checkbox elements
     *
     * @param  array $a
     * @return Child
     */
    public function setCheckboxAttributes(array $a)
    {
        foreach ($this->checkboxes as $checkbox) {
            $checkbox->setAttributes($a);
            if (isset($a['tabindex'])) {
                $a['tabindex']++;
            }
        }
        return $this;
    }


    /**
     * Set the checked value of the checkbox form elements
     *
     * @param  $value
     * @return CheckboxSet
     */
    public function setValue($value)
    {
        $this->checked = (!is_array($value)) ? [$value] : $value;

        if (!empty($this->checked) && ($this->hasChildren())) {
            foreach ($this->childNodes as $child) {
                if ($child instanceof Input\Checkbox) {
                    if (in_array($child->getValue(), $this->checked)) {
                        $child->check();
                    } else {
                        $child->uncheck();
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Reset the value of the form element
     *
     * @return CheckboxSet
     */
    public function resetValue()
    {
        $this->checked = null;
        foreach ($this->childNodes as $child) {
            if ($child instanceof Input\Checkbox) {
                $child->uncheck();
            }
        }
        return $this;
    }

    /**
     * Get checkbox form element checked value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->checked;
    }

    /**
     * Set the checked value
     *
     * @param  mixed $checked
     * @return CheckboxSet
     */
    public function setChecked($checked)
    {
        return $this->setValue($checked);
    }

    /**
     * Get the checked value
     *
     * @return string
     */
    public function getChecked()
    {
        return $this->getValue();
    }

    /**
     * Method to set fieldset legend
     *
     * @param  string $legend
     * @return CheckboxSet
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;
        return $this;
    }

    /**
     * Method to get fieldset legend
     *
     * @return string
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * Validate the form element object
     *
     * @return boolean
     */
    public function validate()
    {
        return (count($this->errors) == 0);
    }

    /**
     * Render the child and its child nodes
     *
     * @param  int     $depth
     * @param  string  $indent
     * @param  string  $errorIndent
     * @return string
     */
    public function render($depth = 0, $indent = null, $errorIndent = null)
    {
        if (!empty($this->legend)) {
            $this->addChild(new Child('legend', $this->legend));
        }
        return parent::render($depth, $indent, $errorIndent);
    }

}