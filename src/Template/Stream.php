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

use Pop\Dom\Child;

/**
 * Form stream template class
 *
 * @category   Pop
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
class Stream extends AbstractTemplate
{

    /**
     * Constructor
     *
     * Instantiate the view stream template object
     *
     * @param  string $template
     * @return Stream
     */
    public function __construct($template)
    {
        $this->setTemplate($template);
    }

    /**
     * Set view template with auto-detect
     *
     * @param  string $template
     * @return Stream
     */
    public function setTemplate($template)
    {
        $this->template = (file_exists($template)) ? file_get_contents($template) : $template;
        return $this;
    }

    /**
     * Render the view and return the output
     *
     * @param  \Pop\Form\Form $form
     * @return string
     */
    public function render(\Pop\Form\Form $form)
    {
        $this->form = $form;
        $this->renderTemplate();
        return $this->output;
    }

    /**
     * Render view template string
     *
     * @return void
     */
    protected function renderTemplate()
    {
        if (null !== $this->form) {
            $template = $this->template;
            $children = $this->form->getChildren();

            // Loop through the child elements of the form.
            foreach ($children as $child) {
                // Clear the password field from display.
                if ($child->getAttribute('type') == 'password') {
                    $child->setValue(null);
                    $child->setAttribute('value', null);
                }

                // Get the element name.
                if ($child->getNodeName() == 'fieldset') {
                    $chdrn = $child->getChildren();
                    $attribs = $chdrn[0]->getAttributes();
                } else {
                    $attribs = $child->getAttributes();
                }

                $name = (isset($attribs['name'])) ? $attribs['name'] : '';
                $name = str_replace('[]', '', $name);

                // Set the element's label, if applicable.
                if (null !== $child->getLabel()) {
                    // Format the label name.
                    $label = new Child('label', $child->getLabel());
                    $label->setAttribute('for', $name);

                    $labelAttributes = $child->getLabelAttributes();
                    if (null !== $labelAttributes) {
                        foreach ($labelAttributes as $a => $v) {
                            if (($a == 'class') && ($child->isRequired())) {
                                $v .= ' required';
                            }
                            $label->setAttribute($a, $v);
                        }
                    } else if ($child->isRequired()) {
                        $label->setAttribute('class', 'required');
                    }

                    // Swap the element's label placeholder with the rendered label element.
                    $labelSearch        = '[{' . $name . '_label}]';
                    $labelReplace       = $label->render(true);
                    $labelReplace       = substr($labelReplace, 0, -1);
                    $template           = str_replace($labelSearch, $labelReplace, $template);
                }

                // Set the element's hint, if applicable.
                if (null !== $child->getHint()) {
                    // Format the hint name.
                    $hint = new Child('span', $child->getHint());

                    $hintAttributes = $child->getHintAttributes();
                    if (null !== $hintAttributes) {
                        foreach ($hintAttributes as $a => $v) {
                            $hint->setAttribute($a, $v);
                        }
                    }

                    // Swap the element's hint placeholder with the rendered hint element.
                    $hintSearch        = '[{' . $name . '_hint}]';
                    $hintReplace       = $hint->render(true);
                    $hintReplace       = substr($hintReplace, 0, -1);
                    $template           = str_replace($hintSearch, $hintReplace, $template);
                }

                // Calculate the element's indentation.
                $childIndent = substr($template, 0, strpos($template, ('[{' . $name . '}]')));
                $childIndent = substr($childIndent, (strrpos($childIndent, "\n") + 1));

                // Some whitespace clean up
                $length  = strlen($childIndent);
                $last    = 0;
                $matches = [];
                preg_match_all('/[^\s]/', $childIndent, $matches, PREG_OFFSET_CAPTURE);
                if (isset($matches[0])) {
                    foreach ($matches[0] as $str) {
                        $childIndent = str_replace($str[0], null, $childIndent);
                        $last = $str[1];
                    }
                }

                // Final whitespace clean up
                $childIndent = substr($childIndent, 0, (0 - abs($length - $last)));

                // Set each child element's indentation.
                $childChildren = $child->getChildren();
                $child->removeChildren();
                foreach ($childChildren as $cChild) {
                    $cChild->setIndent(($childIndent . '    '));
                    $child->addChild($cChild);
                }

                // Swap the element's placeholder with the rendered element.
                $elementSearch  = '[{' . $name . '}]';
                $elementReplace = $child->render(true, 0, null, $childIndent);
                $elementReplace = substr($elementReplace, 0, -1);
                $elementReplace = str_replace('</select>', $childIndent . '</select>', $elementReplace);
                $elementReplace = str_replace('</fieldset>', $childIndent . '</fieldset>', $elementReplace);
                $template       = str_replace($elementSearch, $elementReplace, $template);
            }

            $this->form->setNodeValue("\n" . $template . "\n" . $this->form->getIndent());
            $this->form->removeChildren();
            $this->output = $this->form->render(true);
        }
    }

}
