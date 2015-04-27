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
namespace Pop\Form;

use Pop\Dom\Child;

/**
 * Abstract form class
 *
 * @category   Pop
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
abstract class AbstractForm extends Child implements \ArrayAccess
{

    /**
     * Form template
     * @var Template\TemplateInterface
     */
    protected $template = null;

    /**
     * Form field values
     * @var array
     */
    protected $fields = [];

    /**
     * Filters
     * @var array
     */
    protected $filters = [];

    /**
     * Form field groups
     * @var array
     */
    protected $groups = [];

    /**
     * Form field configuration values
     * @var array
     */
    protected $fieldConfig = [];

    /**
     * Form field group configuration values
     * @var array
     */
    protected $fieldGroupConfig = [];

    /**
     * Global Form error display format
     * @var array
     */
    protected $errorDisplay = null;

    /**
     * Has file flag
     * @var boolean
     */
    protected $hasFile = false;

    /**
     * Set a form template
     *
     * @param  string $template
     * @return Form
     */
    public function setTemplate($template)
    {
        if (!($template instanceof Template\TemplateInterface)) {
            // If template is a PHP file template
            if (((substr($template, -6) == '.phtml') ||
                    (substr($template, -5, 4) == '.php') ||
                    (substr($template, -4) == '.php')) && (file_exists($template))) {
                $template = new Template\File($template);
            // Else, if template is a string or a non-PHP file
            } else {
                $template = new Template\Stream($template);
            }
        }
        $this->template = $template;

        return $this;
    }

    /**
     * Set the form action
     *
     * @param  string $action
     * @return Form
     */
    public function setAction($action)
    {
        $this->setAttribute('action', $action);
        return $this;
    }

    /**
     * Set the form method
     *
     * @param  string $method
     * @return Form
     */
    public function setMethod($method)
    {
        $this->setAttribute('method', $method);
        return $this;
    }

    /**
     * Add filter
     *
     * @param  mixed $call
     * @param  mixed $params
     * @return AbstractForm
     */
    public function addFilter($call, $params = null)
    {
        $this->filters[] = [
            'call'   => $call,
            'params' => $params
        ];
        return $this;
    }

    /**
     * Add filters
     *
     * @param  array $filters
     * @throws Exception
     * @return AbstractForm
     */
    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            if (!isset($filter['call'])) {
                throw new Exception('Error: The \'call\' key must be set.');
            }
            $params = (isset($filter['params'])) ? $filter['params'] : null;
            $this->addFilter($filter['call'], $params);
        }
        return $this;
    }

    /**
     * Clear filters
     *
     * @return AbstractForm
     */
    public function clearFilters()
    {
        $this->filters = [];
        return $this;
    }

    /**
     * Get the form template for the render method to utilize.
     *
     * @return Template\TemplateInterface
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get the $hasFile property
     *
     * @return boolean
     */
    public function hasFile()
    {
        return $this->hasFile;
    }

    /**
     * Get the form action
     *
     * @return array
     */
    public function getAction()
    {
        return $this->getAttribute('action');
    }

    /**
     * Get the form method
     *
     * @return array
     */
    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get fieldConfig
     *
     * @param $name
     * @return array
     */
    public function getFieldConfig($name = null)
    {
        if (null !== $name) {
            return (array_key_exists($name, $this->fieldConfig)) ? $this->fieldConfig[$name] : [];
        } else {
            return $this->fieldConfig;
        }
    }

    /**
     * Get fieldGroupConfig
     *
     * @return array
     */
    public function getFieldGroupConfig()
    {
        return $this->fieldGroupConfig;
    }

    /**
     * Determine if the for has group field configs
     *
     * @return array
     */
    public function hasFieldGroupConfig()
    {
        return (count($this->fieldGroupConfig) > 0);
    }

    /**
     * Method to clear any session data used with the form for
     * security tokens, captchas, etc.
     *
     * @return Form
     */
    public function clear()
    {
        // Start a session.
        if (session_id() == '') {
            session_start();
        }

        if (isset($_SESSION['pop_csrf'])) {
            unset($_SESSION['pop_csrf']);
        }

        if (isset($_SESSION['pop_captcha'])) {
            unset($_SESSION['pop_captcha']);
        }

        return $this;
    }

    /**
     * Method to filter the values with the applied
     * callbacks and their parameters
     *
     * @param  array $values
     * @return mixed
     */
    protected function filterValues(array $values = null)
    {
        if (count($this->filters) > 0) {
            foreach ($this->filters as $filter) {
                $params = [];
                if (isset($filter['params'])) {
                    $params = (!is_array($filter['params'])) ? [$filter['params']] : $filter['params'];
                }
                if (null !== $values) {
                    $this->applyFilter($values, $filter['call'], $params);
                } else {
                    $this->applyFilter($this->fields, $filter['call'], $params);
                }
            }
        }

        return (null !== $values) ? $values : $this->fields;
    }

    /**
     * Execute filter
     *
     * @param  array  $array
     * @param  string $call
     * @param  array  $params
     * @return void
     */
    protected function applyFilter(&$array, $call, $params = [])
    {
        array_walk_recursive($array, function(&$value, $key, $userdata) {
            if (isset($this->fields[$key])) {
                $params = array_merge([$value], $userdata[1]);
                $value  = call_user_func_array($userdata[0], $params);
            }
        }, [$call, $params]);
    }

    /**
     * Set method to set the property to the value of fields[$name]
     *
     * @param  string $name
     * @param  mixed $value
     * @throws Exception
     * @return void
     */
    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
        $this->setFieldValues([$name => $value]);
    }

    /**
     * Get method to return the value of fields[$name]
     *
     * @param  string $name
     * @throws Exception
     * @return mixed
     */
    public function __get($name)
    {
        return (!array_key_exists($name, $this->fields)) ? null : $this->fields[$name];
    }

    /**
     * Return the isset value of fields[$name]
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * Unset fields[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $this->fields[$name] = null;
    }

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * ArrayAccess offsetSet
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }

    /**
     * Set the field values. Optionally, you can apply filters
     * to the passed values via callbacks and their parameters
     *
     * @param  array $values
     * @return AbstractForm
     */
    abstract public function setFieldValues(array $values = null);

    /**
     * Filter of field values with the filters that have been set
     *
     * @return AbstractForm
     */
    abstract public function filter();

}
