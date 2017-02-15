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
 * Form textarea element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */

class Textarea extends AbstractElement
{

    /**
     * Constructor
     *
     * Instantiate the textarea form element
     *
     * @param  string $name
     * @param  string $value
     * @param  string $indent
     */
    public function __construct($name, $value = null, $indent = null)
    {
        parent::__construct('textarea', $value);

        $this->setAttributes(['name' => $name, 'id' => $name]);
        $this->setName($name);
        if (null !== $indent) {
            $this->setIndent($indent);
        }
    }

    /**
     * Set whether the form element is required
     *
     * @param  boolean $required
     * @return Textarea
     */
    public function setRequired($required)
    {
        $this->setAttribute('required', 'required');
        return parent::setRequired($required);
    }

    /**
     * Set the value of the form textarea element object
     *
     * @param  mixed $value
     * @return Textarea
     */
    public function setValue($value)
    {
        $this->setNodeValue($value);
        return $this;
    }

    /**
     * Reset the value of the form element
     *
     * @return Textarea
     */
    public function resetValue()
    {
        $this->setNodeValue('');
        return $this;
    }

    /**
     * Get the value of the form textarea element object
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
