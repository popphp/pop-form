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
namespace Pop\Form\Template;

/**
 * Form template abstract class
 *
 * @category   Pop
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
abstract class AbstractTemplate implements TemplateInterface
{

    /**
     * Form template
     * @var string
     */
    protected $template = null;

    /**
     * Form object
     * @var \Pop\Form\Form
     */
    protected $form = null;

    /**
     * Form output string
     * @var string
     */
    protected $output = null;

    /**
     * Get form template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set form template
     *
     * @param  string $template
     * @return AbstractTemplate
     */
    abstract public function setTemplate($template);

    /**
     * Render the form and return the output
     *
     * @param  \Pop\Form\Form $form
     * @throws Exception
     * @return string
     */
    abstract public function render(\Pop\Form\Form $form);

    /**
     * Render form template file
     *
     * @return void
     */
    abstract protected function renderTemplate();

}