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
namespace Pop\Form\Element\Select;

use Pop\Dom\Child;

/**
 * Form select option element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

class Option extends Child
{

    /**
     * Constructor
     *
     * Instantiate the option element object
     *
     * @param  string  $value
     * @param  string  $nodeValue
     * @param  array   $options
     */
    public function __construct($value, $nodeValue, array $options = [])
    {
        parent::__construct('option', $nodeValue, $options);
        $this->setValue($value);
    }

    /**
     * Set the option value
     *
     * @param  mixed $value
     * @return Option
     */
    public function setValue($value)
    {
        $this->setAttribute('value', $value);
        return $this;
    }

    /**
     * Get the option value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->getAttribute('value');
    }

    /**
     * Select the option value
     *
     * @return Option
     */
    public function select()
    {
        $this->setAttribute('selected', 'selected');
        return $this;
    }

    /**
     * Deselect the option value
     *
     * @return Option
     */
    public function deselect()
    {
        $this->removeAttribute('selected');
        return $this;
    }

    /**
     * Determine if the option value is selected
     *
     * @return boolean
     */
    public function isSelected()
    {
        return ($this->getAttribute('selected') == 'selected');
    }

}
