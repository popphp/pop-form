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
 * Form range element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.2
 */

class Range extends Element\Input
{

    /**
     * Constructor
     *
     * Instantiate the range input form element
     *
     * @param  string  $name
     * @param  int     $min
     * @param  int     $max
     * @param  ?string $value
     * @param  ?string $indent
     */
    public function __construct(string $name, int $min, int $max, ?string $value = null, ?string $indent = null)
    {
        parent::__construct($name, 'range', $value, $indent);
        $this->setAttributes([
            'min' => $min,
            'max' => $max
        ]);
    }

}
