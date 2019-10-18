<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.4.0
 */

class Optgroup extends Child
{

    /**
     * Constructor
     *
     * Instantiate the option element object
     *
     * @param  string  $value
     * @param  array   $options
     */
    public function __construct($value = null, array $options = [])
    {
        parent::__construct('optgroup', $value, $options);
    }

    /**
     * Add an option element
     *
     * @param  Child $option
     * @return Optgroup
     */
    public function addOption(Child $option)
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
    public function addOptions(array $options)
    {
        $this->addChildren($options);
        return $this;
    }

    /**
     * Get option elements
     *
     * @return array
     */
    public function getOptions()
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
