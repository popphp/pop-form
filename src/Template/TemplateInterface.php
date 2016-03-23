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
namespace Pop\Form\Template;

/**
 * Form template interface
 *
 * @category   Pop
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.1
 */
interface TemplateInterface
{

    /**
     * Get form template
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Set form template
     *
     * @param  string $template
     * @return TemplateInterface
     */
    public function setTemplate($template);

    /**
     * Render the form and return the output
     *
     * @param  \Pop\Form\Form $form
     * @throws Exception
     * @return string
     */
    public function render(\Pop\Form\Form $form);

}