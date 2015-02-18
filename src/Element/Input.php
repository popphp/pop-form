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
namespace Pop\Form\Element;

/**
 * Form input element class
 *
 * @category   Pop
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */

class Input extends AbstractElement
{

    /**
     * Element type
     * @var string
     */
    protected $type = 'input';

    /**
     * Constructor
     *
     * Instantiate the form input element, defaults to text
     *
     * @param  string $name
     * @param  string $type
     * @param  string $value
     * @param  string $indent
     * @return Input
     */
    public function __construct($name, $type = 'text', $value = null, $indent = null)
    {
        parent::__construct($this->type, null, null, false, $indent);

        $this->setAttributes([
            'type'  => $type,
            'name'  => $name,
            'id'    => $name,
            'value' => $value
        ]);

        $this->setValue($value);
        $this->setName($name);
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

}
