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
        if (null !== $checked) {
            if (!is_array($checked)) {
                $checked = [$checked];
            }
        } else {
            $checked = [];
        }

        $this->checked = $checked;

        parent::__construct('fieldset');

        $this->setName($name);
        $this->setAttribute('class', 'checkbox-fieldset');

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
     * Get the checked values
     *
     * @return array
     */
    public function getChecked()
    {
        return $this->checked;
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

}