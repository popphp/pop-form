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
 * Form select optgroup element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.1
 */

class Optgroup extends Child
{

    /**
     * Constructor
     *
     * Instantiate the option element object
     *
     * @param  ?string $value
     * @param  array   $options
     */
    public function __construct(?string $value = null, array $options = [])
    {
        parent::__construct('optgroup', $value, $options);
    }

    /**
     * Add an option element
     *
     * @param  Child $option
     * @return Optgroup
     */
    public function addOption(Child $option): Optgroup
    {
        $this->addChild($option);
        return $this;
    }

    /**
     * Add option elements
     *
     * @param  array $options
     * @return Optgroup
     */
    public function addOptions(array $options): Optgroup
    {
        $this->addChildren($options);
        return $this;
    }

    /**
     * Get option elements
     *
     * @return array
     */
    public function getOptions(): array
    {
        $options = [];

        foreach ($this->childNodes as $child) {
            if ($child instanceof Option) {
                $options[] = $child;
            }
        }

        return $options;
    }

}
