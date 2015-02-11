<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp
 * @category   Pop
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form\Element\Input;

use Pop\Form\Element\AbstractElement;

/**
 * Form button element class
 *
 * @category   Pop
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */

abstract class AbstractInput extends AbstractElement
{

    /**
     * Element type
     * @var string
     */
    protected $type = 'input';

    /**
     * Constructor
     *
     * Instantiate the form element
     *
     * @param  string $name
     * @param  string $value
     * @param  string $indent
     * @return AbstractInput
     */
    public function __construct($name, $value = null, $indent = null)
    {
        parent::__construct($this->type, null, null, false, $indent);
        $this->setValue($value);
        $this->setName($name);
    }

}
