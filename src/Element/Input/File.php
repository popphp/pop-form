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
 * Form file element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.0
 */

class File extends Element\Input
{

    /**
     * Constructor
     *
     * Instantiate the file input form element
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  ?string $indent
     */
    public function __construct(string $name, ?string $value = null, ?string $indent = null)
    {
        parent::__construct($name, 'file', $value, $indent);
    }

    /**
     * Validate the form element object
     *
     * @param  array $formValues
     * @return bool
     */
    public function validate(array $formValues = []): bool
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
            $this->errors[] = $this->getRequiredMessage();
        }

        // Check field validators
        if (count($this->validators) > 0) {
            foreach ($this->validators as $validator) {
                if ($validator instanceof \Pop\Validator\ValidatorInterface) {
                    $class =  get_class($validator);
                    if (($size !== null) &&
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
