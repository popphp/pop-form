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

/**
 * Form input element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */

class Input extends AbstractElement
{

    /**
     * Constructor
     *
     * Instantiate the form input element, defaults to text
     *
     * @param  string $name
     * @param  string $type
     * @param  string $value
     * @param  string $indent
     */
    public function __construct($name, $type = 'text', $value = null, $indent = null)
    {
        parent::__construct('input');

        $this->setName($name);
        $this->setAttributes([
            'type'  => $type,
            'name'  => $name,
            'id'    => $name,
            'value' => $value
        ]);

        if (null !== $indent) {
            $this->setIndent($indent);
        }
    }

    /**
     * Set whether the form element is required
     *
     * @param  boolean $required
     * @return Input
     */
    public function setRequired($required)
    {
        $this->setAttribute('required', 'required');
        return parent::setRequired($required);
    }

    /**
     * Set the value of the form input element object
     *
     * @param  mixed $value
     * @return Input
     */
    public function setValue($value)
    {
        $this->setAttribute('value', $value);
        return $this;
    }

    /**
     * Get the value of the form input element object
     *
     * @return string
     */
    public function getValue()
    {
        return $this->getAttribute('value');
    }

    /**
     * Get the type of the form input element object
     *
     * @return string
     */
    public function getType()
    {
        return $this->getAttribute('type');
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
