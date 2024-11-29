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
namespace Pop\Form\Element\Select;

use Pop\Dom\Child;

/**
 * Form select option element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.1
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
    public function __construct(string $value, string $nodeValue, array $options = [])
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
    public function setValue(mixed $value): Option
    {
        $this->setAttribute('value', $value);
        return $this;
    }

    /**
     * Get the option value
     *
     * @return ?string
     */
    public function getValue(): ?string
    {
        return $this->getAttribute('value');
    }

    /**
     * Select the option value
     *
     * @return Option
     */
    public function select(): Option
    {
        $this->setAttribute('selected', 'selected');
        return $this;
    }

    /**
     * Deselect the option value
     *
     * @return Option
     */
    public function deselect(): Option
    {
        $this->removeAttribute('selected');
        return $this;
    }

    /**
     * Determine if the option value is selected
     *
     * @return bool
     */
    public function isSelected(): bool
    {
        return ($this->getAttribute('selected') == 'selected');
    }

}
