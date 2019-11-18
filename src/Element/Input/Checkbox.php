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
namespace Pop\Form\Element\Input;

use Pop\Form\Element;

/**
 * Form checkbox element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

class Checkbox extends Element\Input
{
    /**
     * Constructor
     *
     * Instantiate the text input form element
     *
     * @param  string $name
     * @param  string $value
     * @param  string $indent
     */
    public function __construct($name, $value = null, $indent = null)
    {
        parent::__construct($name, 'checkbox', $value, $indent);
    }
    /**
     * Set the value of the form input element object
     *
     * @param  mixed $value
     * @return Checkbox
     */
    public function setValue($value)
    {
        if ($value == $this->getAttribute('value')) {
            $this->check();
        } else {
            $this->uncheck();
        }
        return $this;
    }

    /**
     * Reset the value of the form element
     *
     * @return Checkbox
     */
    public function resetValue()
    {
        $this->uncheck();
        return $this;
    }

    /**
     * Set the checkbox to checked
     *
     * @return Checkbox
     */
    public function check()
    {
        $this->setAttribute('checked', 'checked');
        return $this;
    }

    /**
     * Set the checkbox to checked
     *
     * @return Checkbox
     */
    public function uncheck()
    {
        $this->removeAttribute('checked');
        return $this;
    }

    /**
     * Determine if the checkbox value is checked
     *
     * @return boolean
     */
    public function isChecked()
    {
        return ($this->getAttribute('checked') == 'checked');
    }

}
