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
namespace Pop\Form;

/**
 * Form validator class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

class FormValidator implements FormInterface, \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Trait declaration
     */
    use FormTrait;

    /**
     * Form validators
     * @var array
     */
    protected $validators = [];

    /**
     * Required fields
     * @var array
     */
    protected $required = [];

    /**
     * Form values
     * @var array
     */
    protected $values = [];

    /**
     * Form validation errors
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor
     *
     * Instantiate the form validator object
     *
     * @param array $validators
     * @param mixed $required
     * @param array $values
     * @param mixed $filters
     */
    public function __construct(array $validators = null, $required = null, array $values = null, $filters = null)
    {
        if (!empty($validators)) {
            $this->addValidators($validators);
        }
        if (null !== $required) {
            $this->setRequired($required);
        }
        if (null !== $values) {
            $this->setValues($values);
        }
        if (null !== $filters) {
            if (is_array($filters)) {
                $this->addFilters($filters);
            } else {
                $this->addFilter($filters);
            }
        }
    }

    /**
     * Create form validator from config
     *
     * @param  array|FormConfig $formConfig
     * @param  mixed            $required
     * @param  array            $values
     * @param  mixed            $filters
     * @return FormValidator
     */
    public static function createFromConfig($formConfig, $required = null, array $values = null, $filters = null)
    {
        $validators = [];

        foreach ($formConfig as $key => $value) {
            if (!empty($value['validator'])) {
                $validators[$key] = $value['validator'];
            } else if (!empty($value['validators'])) {
                $validators[$key] = $value['validators'];
            }
        }

        return new self($validators, $required, $values, $filters);
    }

    /**
     * Add validators
     *
     * @param  array $validators
     * @return FormValidator
     */
    public function addValidators($validators)
    {
        foreach ($validators as $field => $validator) {
            $this->addValidator($field, $validator);
        }
        return $this;
    }

    /**
     * Add validator
     *
     * @param  string $field
     * @param  mixed  $validator
     * @return FormValidator
     */
    public function addValidator($field, $validator)
    {
        if (!isset($this->validators[$field])) {
            $this->validators[$field] = [];
        }

        if (!is_array($validator)) {
            $validator = [$validator];
        }

        foreach ($validator as $valid) {
            if (!in_array($valid, $this->validators[$field], true)) {
                $this->validators[$field][] = $valid;
            }
        }

        return $this;
    }

    /**
     * Has validators
     *
     * @param  string $field
     * @return boolean
     */
    public function hasValidators($field = null)
    {
        if (null === $field) {
            return (count($this->validators) > 0);
        } else if ((null !== $field) && isset($this->validators[$field])) {
            return (count($this->validators[$field]) > 0);
        } else {
            return false;
        }
    }

    /**
     * Get validators
     *
     * @param  string $field
     * @return mixed
     */
    public function getValidators($field = null)
    {
        if (null === $field) {
            return $this->validators;
        } else if ((null !== $field) && isset($this->validators[$field])) {
            return $this->validators[$field];
        } else {
            return null;
        }
    }

    /**
     * Has validator
     *
     * @param  string $field
     * @param  int    $index
     * @return boolean
     */
    public function hasValidator($field, $index)
    {
        return (isset($this->validators[$field]) && isset($this->validators[$field][$index]));
    }

    /**
     * Get validator
     *
     * @param  string $field
     * @param  int    $index
     * @return mixed
     */
    public function getValidator($field, $index)
    {
        return (isset($this->validators[$field]) && isset($this->validators[$field][$index])) ?
            $this->validators[$field][$index] : null;
    }

    /**
     * Remove validators
     *
     * @param  string $field
     * @return FormValidator
     */
    public function removeValidators($field = null)
    {
        if ((null !== $field) && isset($this->validators[$field])) {
            unset($this->validators[$field]);
        } else if (null === $field) {
            $this->validators = [];
        }
        return $this;
    }

    /**
     * Remove validator
     *
     * @param  string $field
     * @param  int    $index
     * @return FormValidator
     */
    public function removeValidator($field, $index)
    {
        if (isset($this->validators[$field]) && isset($this->validators[$field][$index])) {
            unset($this->validators[$field][$index]);
        }
        return $this;
    }

    /**
     * Set required
     *
     * @param  mixed $required
     * @return FormValidator
     */
    public function setRequired($required)
    {
        if (!is_array($required)) {
            $required = [$required];
        }

        foreach ($required as $req) {
            if (!in_array($req, $this->required)) {
                $this->required[] = $req;
            }
        }

        return $this;
    }

    /**
     * Is required
     *
     * @param  string $field
     * @return boolean
     */
    public function isRequired($field)
    {
        return (in_array($field, $this->required));
    }

    /**
     * Remove required
     *
     * @param  string $field
     * @return FormValidator
     */
    public function removeRequired($field)
    {
        if (in_array($field, $this->required)) {
            unset($this->required[array_search($field, $this->required)]);
        }

        return $this;
    }

    /**
     * Set values
     *
     * @param  array $values
     * @return FormValidator
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Filter value with the filters
     *
     * @param  mixed $field
     * @return mixed
     */
    public function filterValue($field)
    {
        if (!isset($this->values[$field])) {
            throw new Exception("Error: A value for '" . $field . "' has not been set.");
        }

        $value = $this->values[$field];

        foreach ($this->filters as $filter) {
            $value = $filter->filter($value, $field);
        }

        $this->values[$field] = $value;

        return $value;
    }

    /**
     * Filter values with the filters
     *
     * @param  mixed $values
     * @return mixed
     */
    public function filter($values = null)
    {
        if (null !== $values) {
            $this->values = $values;
        }

        if (is_array($this->values)) {
            foreach ($this->values as $name => $value) {
                $this->values[$name] = $this->filterValue($name);
            }
        } else {
            $this->values = $this->filterValue($this->values);
        }

        return $this->values;
    }

    /**
     * Validate values
     *
     * @return boolean
     */
    public function validate()
    {
        $this->filter();

        foreach ($this->required as $required) {
            if (!isset($this->values[$required])) {
                $this->addError($required, 'This field is required.');
            }
        }

        foreach ($this->values as $field => $value) {
            if ($this->hasValidators($field)) {
                foreach ($this->validators[$field] as $validator) {
                    if ($validator instanceof \Pop\Validator\ValidatorInterface) {
                        if (!$validator->evaluate($value)) {
                            $this->addError($field, $validator->getMessage());
                        }
                    } else if (is_callable($validator)) {
                        $result = call_user_func_array($validator, [$value, $this->values]);
                        if ($result instanceof \Pop\Validator\ValidatorInterface) {
                            if (!$result->evaluate($value)) {
                                $this->addError($field, $result->getMessage());
                            }
                        } else if (is_array($result)) {
                            foreach ($result as $val) {
                                if ($val instanceof \Pop\Validator\ValidatorInterface) {
                                    if (!$val->evaluate($value)) {
                                        $this->addError($field, $val->getMessage());
                                    }
                                }
                            }
                        } else if (null !== $result) {
                            $this->addError($field, $result);
                        }
                    }
                }
            }
        }

        return !$this->hasErrors();
    }

    /**
     * Has errors
     *
     * @param  string $field
     * @return boolean
     */
    public function hasErrors($field = null)
    {
        if ((null !== $field) && isset($this->errors[$field])) {
            return (count($this->errors[$field]) > 0);
        } else {
            return (count($this->errors) > 0);
        }
    }

    /**
     * Get errors
     *
     * @param  string $field
     * @return array
     */
    public function getErrors($field = null)
    {
        if ((null !== $field) && isset($this->errors[$field])) {
            return $this->errors[$field];
        } else {
            return $this->errors;
        }
    }

    /**
     * Get error
     *
     * @param  string $field
     * @param  int    $index
     * @return mixed
     */
    public function getError($field, $index)
    {
        return (isset($this->errors[$field]) && isset($this->errors[$field][$index])) ?
            $this->errors[$field][$index] : null;
    }

    /**
     * Add error
     *
     * @param  string $field
     * @param  string $error
     * @return FormValidator
     */
    protected function addError($field, $error)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        if (!in_array($error, $this->errors[$field])) {
            $this->errors[$field][] = $error;
        }

        return $this;
    }

    /**
     * Count of values
     *
     * @return int
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * Get values
     *
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * Set method to set the property to the value of values[$name]
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->values[$name] = $value;
    }

    /**
     * Get method to return the value of values[$name]
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return (isset($this->values[$name])) ? $this->values[$name] : null;
    }

    /**
     * Return the isset value of values[$name]
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->values[$name]);
    }

    /**
     * Unset values[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        if (isset($this->values[$name])) {
            unset($this->values[$name]);
        }
    }

}