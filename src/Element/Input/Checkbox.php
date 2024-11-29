<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.1
 */

class Checkbox extends Element\Input
{
    /**
     * Constructor
     *
     * Instantiate the text input form element
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  ?string $indent
     */
    public function __construct(string $name, ?string $value = null, ?string $indent = null)
    {
        parent::__construct($name, 'checkbox', $value, $indent);
    }
    /**
     * Set the value of the form input element object
     *
     * @param  mixed $value
     * @return Checkbox
     */
    public function setValue(mixed $value): Checkbox
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
    public function resetValue(): Checkbox
    {
        $this->uncheck();
        return $this;
    }

    /**
     * Set the checkbox to checked
     *
     * @return Checkbox
     */
    public function check(): Checkbox
    {
        $this->setAttribute('checked', 'checked');
        return $this;
    }

    /**
     * Set the checkbox to checked
     *
     * @return Checkbox
     */
    public function uncheck(): Checkbox
    {
        $this->removeAttribute('checked');
        return $this;
    }

    /**
     * Determine if the checkbox value is checked
     *
     * @return bool
     */
    public function isChecked(): bool
    {
        return ($this->getAttribute('checked') == 'checked');
    }

}
