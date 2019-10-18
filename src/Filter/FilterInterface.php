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
namespace Pop\Form\Filter;

/**
 * Form filter interface
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.4.0
 */
interface FilterInterface
{

    /**
     * Set callable
     *
     * @param  callable $callable
     * @return FilterInterface
     */
    public function setCallable(callable $callable);

    /**
     * Set params
     *
     * @param  mixed $params
     * @return FilterInterface
     */
    public function setParams($params);

    /**
     * Set exclude by type
     *
     * @param  mixed $excludeByType
     * @return FilterInterface
     */
    public function setExcludeByType($excludeByType);

    /**
     * Set exclude by name
     *
     * @param  mixed $excludeByName
     * @return FilterInterface
     */
    public function setExcludeByName($excludeByName);

    /**
     * Get callable
     *
     * @return callable
     */
    public function getCallable();

    /**
     * Get params
     *
     * @return array
     */
    public function getParams();

    /**
     * Get exclude by type
     *
     * @return array
     */
    public function getExcludeByType();

    /**
     * Get exclude by name
     *
     * @return array
     */
    public function getExcludeByName();

    /**
     * Has callable
     *
     * @return boolean
     */
    public function hasCallable();

    /**
     * Has params
     *
     * @return boolean
     */
    public function hasParams();

    /**
     * Has exclude by type
     *
     * @return boolean
     */
    public function hasExcludeByType();

    /**
     * Has exclude by name
     *
     * @return boolean
     */
    public function hasExcludeByName();

    /**
     * Filter value
     *
     * @param  mixed  $value
     * @param  mixed  $type
     * @param  string $name
     * @return mixed
     */
    public function filter($value, $type = null, $name = null);

}