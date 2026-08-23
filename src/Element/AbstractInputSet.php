<?php
declare(strict_types=1);
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form\Element;

use Pop\Dom\Child;

/**
 * Abstract form input set class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
abstract class AbstractInputSet extends AbstractElement
{

    /**
     * Array of input elements
     * @var array
     */
    protected array $inputs = [];

    /**
     * Fieldset legend
     * @var ?string
     */
    protected ?string $legend = null;

    /**
     * Fieldset container
     * @var ?string
     */
    protected ?string $container = null;

    /**
     * Create an input element, wrap it with its span and append it (and the input) to the set
     *
     * @param  AbstractElement $input
     * @param  mixed           $nodeValue
     * @param  string          $spanClass
     * @param  ?string         $indent
     * @param  string          $containerClass
     * @return void
     */
    protected function appendInputWithSpan(
        AbstractElement $input, mixed $nodeValue, string $spanClass, ?string $indent, string $containerClass
    ): void
    {
        $span = new Child('span');
        if ($indent !== null) {
            $span->setIndent($indent);
        }
        $span->setAttribute('class', $spanClass);
        $span->setNodeValue(($nodeValue !== null) ? (string)$nodeValue : null);

        if (!empty($this->container)) {
            $container = new Child($this->container);
            $container->setAttribute('class', $containerClass);
            $container->addChildren([$input, $span]);
            $this->addChildren([$container]);
        } else {
            $this->addChildren([$input, $span]);
        }

        $this->inputs[] = $input;
    }

    /**
     * Set whether the form element is disabled
     *
     * @param  bool $disabled
     * @return static
     */
    public function setDisabled(bool $disabled): static
    {
        $childNodes = $this->getFieldsetChildNodes();
        if ($disabled) {
            foreach ($childNodes as $childNode) {
                $childNode->setAttribute('disabled', 'disabled');
            }
        } else {
            foreach ($childNodes as $childNode) {
                $childNode->removeAttribute('disabled');
            }
        }

        parent::setDisabled($disabled);
        return $this;
    }

    /**
     * Set whether the form element is readonly
     *
     * @param  bool $readonly
     * @return static
     */
    public function setReadonly(bool $readonly): static
    {
        $childNodes = $this->getFieldsetChildNodes();
        if ($readonly) {
            foreach ($childNodes as $childNode) {
                $childNode->setAttribute('readonly', 'readonly');
                $childNode->setAttribute('onclick', 'return false;');
            }
        } else {
            foreach ($childNodes as $childNode) {
                $childNode->removeAttribute('readonly');
                $childNode->removeAttribute('onclick');
            }
        }

        parent::setReadonly($readonly);
        return $this;
    }

    /**
     * Set an attribute for the input elements
     *
     * @param  string $a
     * @param  string $v
     * @return static
     */
    protected function setInputAttribute(string $a, string $v): static
    {
        foreach ($this->inputs as $input) {
            $input->setAttribute($a, $v);
            if ($a == 'tabindex') {
                $v = (string)((int)$v + 1);
            }
        }
        return $this;
    }

    /**
     * Set an attribute or attributes for the input elements
     *
     * @param  array $a
     * @return static
     */
    protected function setInputAttributes(array $a): static
    {
        if (isset($a['tabindex'])) {
            $a['tabindex'] = (string)$a['tabindex'];
        }
        foreach ($this->inputs as $input) {
            $input->setAttributes($a);
            if (isset($a['tabindex'])) {
                $a['tabindex'] = (string)((int)$a['tabindex'] + 1);
            }
        }
        return $this;
    }

    /**
     * Method to set fieldset legend
     *
     * @param  string $legend
     * @return static
     */
    public function setLegend(string $legend): static
    {
        $this->legend = $legend;
        return $this;
    }

    /**
     * Method to get fieldset legend
     *
     * @return ?string
     */
    public function getLegend(): ?string
    {
        return $this->legend;
    }

    /**
     * Method to set fieldset container
     *
     * @param  string $container
     * @return static
     */
    public function setContainer(string $container): static
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Method to get fieldset container
     *
     * @return ?string
     */
    public function getContainer(): ?string
    {
        return $this->container;
    }

    /**
     * Method to get fieldset child nodes
     *
     * @return array
     */
    public function getFieldsetChildNodes(): array
    {
        if (!empty($this->container)) {
            $childNodes = [];
            foreach ($this->childNodes as $childNode) {
                if (($childNode->getNodeName() == $this->container) && ($childNode->hasChildNodes())) {
                    $childNodes = array_merge($childNodes, $childNode->getChildNodes());
                }
            }
            return $childNodes;
        } else {
            return $this->childNodes;
        }
    }

    /**
     * Validate the form element object
     *
     * @param  array $formValues
     * @return bool
     */
    public function validate(array $formValues = []): bool
    {
        $value = $this->getValue();

        // Check if the element is required
        if (($this->required) && empty($value)) {
            $this->errors[] = $this->getRequiredMessage();
        }

        $this->validateValue($value, $formValues);

        return (count($this->errors) == 0);
    }

    /**
     * Render the child and its child nodes
     *
     * @param  int     $depth
     * @param  ?string $indent
     * @param  bool    $inner
     * @return string
     */
    public function render(int $depth = 0, ?string $indent = null, bool $inner = false): string
    {
        if (!empty($this->legend)) {
            $this->addChild(new Child('legend', $this->legend));
        }
        return parent::render($depth, $indent, $inner);
    }

}
