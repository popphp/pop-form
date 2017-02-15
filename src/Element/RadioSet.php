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
 * Form radio element set class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */

class RadioSet extends AbstractElement
{

    /**
     * Array of radio input elements
     * @var array
     */
    protected $radios = [];

    /**
     * Array of checked values
     * @var string
     */
    protected $checked = null;

    /**
     * Constructor
     *
     * Instantiate the radio input form elements
     *
     * @param  string $name
     * @param  array  $values
     * @param  string $checked
     * @param  string $indent
     */
    public function __construct($name, array $values, $checked = null, $indent = null)
    {
        $this->checked = $checked;

        parent::__construct('fieldset');

        $this->setName($name);
        $this->setAttribute('class', 'radio-fieldset');

        if (null !== $indent) {
            $this->setIndent($indent);
        }

        // Create the radio elements and related span elements.
        $i = null;
        foreach ($values as $k => $v) {
            $radio = new Input\Radio($name, null, $indent);
            $radio->setAttributes([
                'class' => 'radio',
                'id'    => ($name . $i),
                'value' => $k
            ]);

            // Determine if the current radio element is checked.
            if ((null !== $this->checked) && ($k == $this->checked)) {
                $radio->check();
            }

            $span = new Child('span');
            if (null !== $indent) {
                $span->setIndent($indent);
            }
            $span->setAttribute('class', 'radio-span');
            $span->setNodeValue($v);
            $this->addChildren([$radio, $span]);
            $this->radios[] = $radio;
            $i++;
        }
    }

    /**
     * Set an attribute for the input radio elements
     *
     * @param  string $a
     * @param  string $v
     * @return Child
     */
    public function setRadioAttribute($a, $v)
    {
        foreach ($this->radios as $radio) {
            $radio->setAttribute($a, $v);
            if ($a == 'tabindex') {
                $v++;
            }

        }
        return $this;
    }

    /**
     * Set an attribute or attributes for the input radio elements
     *
     * @param  array $a
     * @return Child
     */
    public function setRadioAttributes(array $a)
    {
        foreach ($this->radios as $radio) {
            $radio->setAttributes($a);
            if (isset($a['tabindex'])) {
                $a['tabindex']++;
            }
        }
        return $this;
    }

    /**
     * Get the checked value
     *
     * @return string
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