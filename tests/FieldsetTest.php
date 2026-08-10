<?php

namespace Pop\Form\Test;

use Pop\Form\Fieldset;
use Pop\Form\Element;
use PHPUnit\Framework\TestCase;

class FieldsetTest extends TestCase
{

    public function testConstructor()
    {
        $username = new Element\Input('username');
        $email    = new Element\Input\Email('email');
        $submit   = new Element\Input\Submit('submit', 'SUBMIT');
        $fieldset = new Fieldset([$username, $email, $submit]);
        $this->assertInstanceOf('Pop\Form\Fieldset', $fieldset);
        $this->assertInstanceOf('Pop\Form\Element\AbstractElement', $fieldset->getField('username'));
    }

    public function testCreateFromConfig()
    {
        $fieldset = Fieldset::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'file' => [
                'type'  => 'file',
                'label' => 'File:'
            ]
        ]);
        $fieldset->setCurrent(1);
        $fieldset->setFieldValues(['username' => 'my_username']);
        $this->assertEquals(2, count($fieldset->getAllFields()));
        $this->assertEquals(1, $fieldset->getCurrent());
        $fieldset->createGroup();
        $this->assertEquals(2, $fieldset->getCurrent());
        $this->assertEquals(2, count($fieldset->getFields(0)));
        $this->assertEquals(3, count($fieldset->getFieldGroups()));
        $this->assertEquals('my_username', $fieldset->getFieldValue('username'));
        $this->assertEquals('my_username', $fieldset['username']);
        foreach ($fieldset as $field => $value) {
            $this->assertNotEmpty($field);
        }
        unset($fieldset['username']);
        $this->assertNull($fieldset->username);
    }

    public function testPrepareTable()
    {
        $fieldset = Fieldset::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $fieldset->setContainer('table');
        $fieldset->prepare();
        $this->assertStringContainsString('table', $fieldset->getChild(0)->getNodeName());
    }

    public function testPrepareDiv()
    {
        $fieldset = Fieldset::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $fieldset->setContainer('div');
        $fieldset->prepare();
        $this->assertStringContainsString('div', $fieldset->getChild(0)->getNodeName());
    }

    public function testPrepareLegend()
    {
        $fieldset = Fieldset::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $fieldset->setContainer('div');
        $fieldset->setLegend('Hello!');
        $fieldset->prepare();
        $this->assertStringContainsString('legend', $fieldset->getChild(0)->getNodeName());
    }

    public function testPrepareWiresAriaOnFieldWithErrors()
    {
        $fieldset = Fieldset::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true,
                'hint'     => 'Letters and numbers only.'
            ]
        ]);
        $username = $fieldset->getField('username');
        $username->validate();
        $this->assertTrue($username->hasErrors());

        $fieldset->prepare();
        $rendered = (string)$fieldset;

        $this->assertStringContainsString('id="username-error"', $rendered);
        $this->assertStringContainsString('role="alert"', $rendered);
        $this->assertStringContainsString('id="username-hint"', $rendered);
        $this->assertStringContainsString('aria-invalid="true"', $rendered);
        $this->assertStringContainsString('aria-describedby="username-hint username-error"', $rendered);
    }

    public function testPrepareOmitsAriaInvalidWithoutErrors()
    {
        $fieldset = Fieldset::createFromConfig([
            'username' => [
                'type'  => 'text',
                'label' => 'Username:'
            ]
        ]);
        $fieldset->prepare();
        $rendered = (string)$fieldset;

        $this->assertStringNotContainsString('aria-invalid', $rendered);
        $this->assertStringNotContainsString('aria-describedby', $rendered);
    }

    public function testPrepareCheckboxSetErrorsSkipAriaInvalidOnFieldset()
    {
        $fieldset = Fieldset::createFromConfig([
            'colors' => [
                'type'     => 'checkbox-set',
                'label'    => 'Colors:',
                'values'   => ['red' => 'Red', 'blue' => 'Blue'],
                'required' => true
            ]
        ]);
        $colors = $fieldset->getField('colors');
        $colors->validate();
        $this->assertTrue($colors->hasErrors());

        $fieldset->prepare();
        $rendered = (string)$fieldset;

        $this->assertStringContainsString('id="colors-error"', $rendered);
        $this->assertStringContainsString('aria-describedby="colors-error"', $rendered);
        $this->assertStringNotContainsString('aria-invalid', $rendered);
    }

    private function fullFeatureConfig(): array
    {
        return [
            'amount' => [
                'type'             => 'text',
                'label'            => 'Amount:',
                'label-attributes' => ['class' => 'existing'],
                'required'         => true,
                'append'           => '.00',
                'error'            => 'pre'
            ],
            'note' => [
                'type'            => 'text',
                'label'           => 'Note:',
                'prepend'         => '#',
                'hint'            => 'Optional note',
                'hint-attributes' => ['class' => 'note-hint'],
                'required'        => true
            ]
        ];
    }

    private function validateFullFeatureFields(\Pop\Form\Fieldset $fieldset): void
    {
        $fieldset->getField('amount')->validate();
        $fieldset->getField('note')->validate();
    }

    public function testPrepareDlFullFeatureCoverage()
    {
        $fieldset = Fieldset::createFromConfig($this->fullFeatureConfig());
        $this->validateFullFeatureFields($fieldset);
        $fieldset->prepare();
        $rendered = (string)$fieldset;

        $this->assertStringContainsString('existing required', $rendered);
        $this->assertStringContainsString('.00', $rendered);
        $this->assertStringContainsString('#', $rendered);
        $this->assertStringContainsString('Optional note', $rendered);
        $this->assertStringContainsString('note-hint', $rendered);
        $this->assertStringContainsString('This field is required.', $rendered);
    }

    public function testPrepareTableFullFeatureCoverage()
    {
        $fieldset = Fieldset::createFromConfig($this->fullFeatureConfig());
        $fieldset->setContainer('table');
        $this->validateFullFeatureFields($fieldset);
        $fieldset->prepare();
        $rendered = (string)$fieldset;

        $this->assertStringContainsString('existing required', $rendered);
        $this->assertStringContainsString('.00', $rendered);
        $this->assertStringContainsString('#', $rendered);
        $this->assertStringContainsString('Optional note', $rendered);
        $this->assertStringContainsString('note-hint', $rendered);
        $this->assertStringContainsString('This field is required.', $rendered);
    }

    public function testPrepareDivFullFeatureCoverage()
    {
        $fieldset = Fieldset::createFromConfig($this->fullFeatureConfig());
        $fieldset->setContainer('div');
        $this->validateFullFeatureFields($fieldset);
        $fieldset->prepare();
        $rendered = (string)$fieldset;

        $this->assertStringContainsString('existing required', $rendered);
        $this->assertStringContainsString('.00', $rendered);
        $this->assertStringContainsString('#', $rendered);
        $this->assertStringContainsString('Optional note', $rendered);
        $this->assertStringContainsString('note-hint', $rendered);
        $this->assertStringContainsString('This field is required.', $rendered);
    }

    public function testPrepareForViewFullFeatureCoverage()
    {
        $fieldset = Fieldset::createFromConfig($this->fullFeatureConfig());
        $this->validateFullFeatureFields($fieldset);
        $fields = $fieldset->prepareForView();

        $this->assertStringContainsString('existing required', $fields['amount_label']);
        $this->assertStringContainsString('Optional note', $fields['note_hint']);
        $this->assertEquals(['This field is required.'], $fields['amount_errors']);
    }

}
