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
 * Form file element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

class File extends Element\Input
{

    /**
     * Constructor
     *
     * Instantiate the file input form element
     *
     * @param  string $name
     * @param  string $value
     * @param  string $indent
     */
    public function __construct($name, $value = null, $indent = null)
    {
        parent::__construct($name, 'file', $value, $indent);
    }

    /**
     * Validate the form element object
     *
     * @param  array $formValues
     * @return boolean
     */
    public function validate(array $formValues = [])
    {
        if (($_FILES) && (isset($_FILES[$this->name]['name']))) {
            $value = $_FILES[$this->name]['name'];
            $size  = $_FILES[$this->name]['size'];
        } else {
            $value = null;
            $size  = null;
        }

        // Check if the element is required
        if (($this->required) && empty($value)) {
            $this->errors[] = 'This field is required.';
        }

        // Check field validators
        if (count($this->validators) > 0) {
            foreach ($this->validators as $validator) {
                if ($validator instanceof \Pop\Validator\ValidatorInterface) {
                    $class =  get_class($validator);
                    if ((null !== $size) &&
                        (('Pop\Validator\LessThanEqual' == $class) || ('Pop\Validator\GreaterThanEqual' == $class) ||
                         ('Pop\Validator\LessThan' == $class) || ('Pop\Validator\GreaterThan' == $class))) {
                        if (!$validator->evaluate($size)) {
                            $this->errors[] = $validator->getMessage();
                        }
                    } else {
                        if (!$validator->evaluate($value)) {
                            $this->errors[] = $validator->getMessage();
                        }
                    }
                } else if (is_callable($validator)) {
                    $this->validateCallable($validator, $value, $formValues);
                }
            }
        }

        return (count($this->errors) == 0);
    }

}
