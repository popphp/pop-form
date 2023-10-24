<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
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
    protected array $validators = [];

    /**
     * Required fields
     * @var array
     */
    protected array $required = [];

    /**
     * Form values
     * @var array
     */
    protected array $values = [];

    /**
     * Form validation errors
     * @var array
     */
    protected array $errors = [];

    /**
     * Constructor
     *
     * Instantiate the form validator object
     *
     * @param ?array $validators
     * @param mixed  $required
     * @param ?array $values
     * @param mixed  $filters
     */
    public function __construct(array $validators = null, mixed $required = null, ?array $values = null, mixed $filters = null)
    {
        if (!empty($validators)) {
            $this->addValidators($validators);
        }
        if ($required !== null) {
            $this->setRequired($required);
        }
        if ($values !== null) {
            $this->setValues($values);
        }
        if ($filters !== null) {
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
     * @param  ?array           $values
     * @param  mixed            $filters
     * @return FormValidator
     */
    public static function createFromConfig(
        array|FormConfig $formConfig, mixed $required = null, ?array $values = null, mixed $filters = null
    ): FormValidator
    {
        $validators = [];
        $required   = [];

        foreach ($formConfig as $key => $value) {
            if (!empty($value['validator'])) {
                $validators[$key] = $value['validator'];
            } else if (!empty($value['validators'])) {
                $validators[$key] = $value['validators'];
            }
            if (isset($value['required']) && ($value['required'] == true)) {
                $required[] = $key;
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
    public function addValidators(array $validators): FormValidator
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
    public function addValidator(string $field, mixed $validator): FormValidator
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
     * @param  ?string $field
     * @return bool
     */
    public function hasValidators(?string $field = null)
    {
        if ($field === null) {
            return (count($this->validators) > 0);
        } else if (($field !== null) && isset($this->validators[$field])) {
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
    public function getValidators(?string $field = null)
    {
        if ($field === null) {
            return $this->validators;
        } else if (($field !== null) && isset($this->validators[$field])) {
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
     * @return bool
     */
    public function hasValidator(string $field, int $index): bool
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
    public function getValidator(string $field, int $index)
    {
        return (isset($this->validators[$field]) && isset($this->validators[$field][$index])) ?
            $this->validators[$field][$index] : null;
    }

    /**
     * Remove validators
     *
     * @param  ?string $field
     * @return FormValidator
     */
    public function removeValidators(?string $field = null): FormValidator
    {
        if (($field !== null) && isset($this->validators[$field])) {
            unset($this->validators[$field]);
        } else if ($field === null) {
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
    public function removeValidator(string $field, int $index): FormValidator
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
    public function setRequired(mixed $required): FormValidator
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
     * @return bool
     */
    public function isRequired(string $field): bool
    {
        return (in_array($field, $this->required));
    }

    /**
     * Remove required
     *
     * @param  string $field
     * @return FormValidator
     */
    public function removeRequired(string $field): FormValidator
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
    public function setValues(array $values): FormValidator
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Filter value with the filters
     *
     * @param  mixed $field
     * @throws Exception
     * @return mixed
     */
    public function filterValue(mixed $field): mixed
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
    public function filter(mixed $values = null): mixed
    {
        if ($values !== null) {
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
     * @param  mixed $fields
     * @return bool
     */
    public function validate(mixed $fields = null): bool
    {
        $this->filter();

        if ($fields !== null) {
            $fields     = (!is_array($fields)) ? [$fields] : $fields;
            $formFields = array_filter(
                $this->values,
                function ($key) use ($fields) {
                    return in_array($key, $fields);
                },
                ARRAY_FILTER_USE_KEY
            );

            foreach ($formFields as $field) {
                if (in_array($field, $this->required) && !isset($formFields[$field])) {
                    $this->addError($field, 'This field is required.');
                }
            }
        } else {
            $formFields = $this->values;
            // Check for required fields
            foreach ($this->required as $required) {
                if (!isset($formFields[$required])) {
                    $this->addError($required, 'This field is required.');
                }
            }
        }

        // Check for required fields and execute any field validators
        foreach ($formFields as $field => $value) {
            if ($this->hasValidators($field)) {
                foreach ($this->validators[$field] as $validator) {
                    if ($validator instanceof \Pop\Validator\ValidatorInterface) {
                        if (!$validator->evaluate($value)) {
                            $this->addError($field, $validator->getMessage());
                        }
                    } else if (is_callable($validator)) {
                        $result = call_user_func_array($validator, [$value, $formFields]);
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
                        } else if ($result !== null) {
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
     * @param  ?string $field
     * @return bool
     */
    public function hasErrors(?string $field = null): bool
    {
        if ($field !== null) {
            return (isset($this->errors[$field]) && (count($this->errors[$field]) > 0));
        } else {
            return (count($this->errors) > 0);
        }
    }

    /**
     * Get errors
     *
     * @param  ?string $field
     * @return array
     */
    public function getErrors(?string $field = null): array
    {
        if (($field !== null) && isset($this->errors[$field])) {
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
    public function getError(string $field, int $index): mixed
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
    protected function addError(string $field, string $error): FormValidator
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
    public function count(): int
    {
        return count($this->values);
    }

    /**
     * Get values
     *
     * @return array
     */
    public function toArray(): array
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
    public function __set(string $name, mixed $value): void
    {
        $this->values[$name] = $value;
    }

    /**
     * Get method to return the value of values[$name]
     *
     * @param  string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->values[$name] ?? null;
    }

    /**
     * Return the isset value of values[$name]
     *
     * @param  string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->values[$name]);
    }

    /**
     * Unset values[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        if (isset($this->values[$name])) {
            unset($this->values[$name]);
        }
    }

}