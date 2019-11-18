<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form\Element;

/**
 * Form button element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

class Button extends AbstractElement
{

    /**
     * Constructor
     *
     * Instantiate the button form element.
     *
     * @param  string $name
     * @param  string $value
     * @param  string $indent
     */
    public function __construct($name, $value = null, $indent = null)
    {
        parent::__construct('button', $value);

        $this->setAttributes(['name' => $name, 'id' => $name]);

        if (strtolower($name) == 'submit') {
            $this->setAttribute('type', 'submit');
        } else if (strtolower($name) == 'reset') {
            $this->setAttribute('type', 'reset');
        } else{
            $this->setAttribute('type', 'button');
        }

        $this->setName($name);

        if (null !== $value) {
            $this->setValue($value);
        }

        if (null !== $indent) {
            $this->setIndent($indent);
        }
    }

    /**
     * Set the value of the form button element object
     *
     * @param  mixed $value
     * @return Button
     */
    public function setValue($value)
    {
        $this->setNodeValue($value);
        return $this;
    }

    /**
     * Reset the value of the form element
     *
     * @return Button
     */
    public function resetValue()
    {
        $this->setNodeValue('');
        return $this;
    }

    /**
     * Get form element object type
     *
     * @return string
     */
    public function getType()
    {
        return 'button';
    }

    /**
     * Get the value of the form button element object
     *
     * @return string
     */
    public function getValue()
    {
        return $this->getNodeValue();
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
