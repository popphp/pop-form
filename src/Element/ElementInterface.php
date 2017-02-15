<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form\Element;

/**
 * Form element interface
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
interface ElementInterface
{

    /**
     * Set the name of the form element
     *
     * @param  string $name
     * @return ElementInterface
     */
    public function setName($name);

    /**
     * Set the value of the form element
     *
     * @param  mixed $value
     * @return ElementInterface
     */
    public function setValue($value);

    /**
     * Set the label of the form element
     *
     * @param  mixed $label
     * @return ElementInterface
     */
    public function setLabel($label);

    /**
     * Set the attributes of the label of the form element
     *
     * @param  array $attribs
     * @return ElementInterface
     */
    public function setLabelAttributes(array $attribs);

    /**
     * Set whether the form element is required
     *
     * @param  boolean $required
     * @return mixed
     */
    public function setRequired($required);

    /**
     * Set error pre-display
     *
     * @param  boolean $pre
     * @return ElementInterface
     */
    public function setErrorPre($pre = true);

    /**
     * Set error post-display
     *
     * @param  boolean $post
     * @return ElementInterface
     */
    public function setErrorPost($post = true);

    /**
     * Set error display values
     *
     * @param  string  $container
     * @param  array   $attribs
     * @param  boolean $pre
     * @return ElementInterface
     */
    public function setErrorDisplay($container, array $attribs, $pre = false);

    /**
     * Set validators
     *
     * @param  array $validators
     * @return ElementInterface
     */
    public function setValidators(array $validators = []);

    /**
     * Clear errors
     *
     * @return ElementInterface
     */
    public function clearErrors();

    /**
     * Get form element name
     *
     * @return string
     */
    public function getName();

    /**
     * Get form element value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Get form element label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get the attributes of the form element label
     *
     * @return array
     */
    public function getLabelAttributes();

    /**
     * Get validators
     *
     * @return array
     */
    public function getValidators();

    /**
     * Get error display values
     *
     * @return array
     */
    public function getErrorDisplay();

    /**
     * Get whether the form element is required
     *
     * @return boolean
     */
    public function isRequired();

    /**
     * Get form element errors
     *
     * @return array
     */
    public function getErrors();

    /**
     * Get if form element has errors
     *
     * @return array
     */
    public function hasErrors();

    /**
     * Add a validator the form element
     *
     * @param  mixed $validator
     * @return ElementInterface
     */
    public function addValidator($validator);

    /**
     * Validate the form element
     *
     * @throws Exception
     * @return boolean
     */
    public function validate();

}
