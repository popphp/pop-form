<?php

namespace Pop\Form\Test;

use Pop\Form\Form;
use Pop\Form\Template;

class TemplateTest extends \PHPUnit_Framework_TestCase
{

    public function testSetStreamTemplate()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->setTemplate('<html></html>');
        $this->assertInstanceOf('Pop\Form\Template\Stream', $form->getTemplate());
        $this->assertEquals('<html></html>', $form->getTemplate()->getTemplate());
    }

    public function testSetFileTemplate()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form();
        $form->setTemplate(__DIR__ . '/tmp/form.phtml');
        $this->assertInstanceOf('Pop\Form\Template\File', $form->getTemplate());
        $this->assertEquals(__DIR__ . '/tmp/form.phtml', $form->getTemplate()->getTemplate());
    }

    public function testSetFileTemplateException()
    {
        $this->setExpectedException('Pop\Form\Template\Exception');
        $template = new Template\File(__DIR__ . '/tmp/bad.phtml');
    }

    public function testRenderStreamTemplate()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'email' => [
                'type'  => 'email',
                'label' => 'Email:'
            ],
            'password' => [
                'type'  => 'password',
                'label' => 'Password:',
                'required' => true
            ],
            'file' => [
                'type'  => 'file',
                'label' => 'File:'
            ],
            'colors' => [
                'type' => 'checkbox',
                'label' => 'Colors',
                'value' => [
                    'Red'   => 'Red',
                    'White' => 'White',
                    'Blue'  => 'Blue'
                ]
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);

        $form->getElement('colors')->setLabelAttributes([
            'class' => 'label-class'
        ]);

        $form->setTemplate(__DIR__ . '/tmp/form.html');
        $string = (string)$form;

        $this->assertContains('<form', $string);
        $this->assertContains('action="/process"', $string);
        $this->assertContains('id="username"', $string);
        $this->assertContains('enctype="multipart/form-data"', $string);
    }

    public function testRenderFileTemplate()
    {
        $_SERVER['REQUEST_URI'] = '/process';
        $form = new Form([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'email' => [
                'type'  => 'email',
                'label' => 'Email:'
            ],
            'password' => [
                'type'  => 'password',
                'label' => 'Password:',
                'required' => true
            ],
            'file' => [
                'type'  => 'file',
                'label' => 'File:'
            ],
            'colors' => [
                'type' => 'checkbox',
                'label' => 'Colors',
                'value' => [
                    'Red'   => 'Red',
                    'White' => 'White',
                    'Blue'  => 'Blue'
                ]
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);

        $form->getElement('colors')->setLabelAttributes([
            'class' => 'label-class'
        ]);

        $form->setTemplate(__DIR__ . '/tmp/form.phtml');
        $string = (string)$form;

        $this->assertContains('<form', $string);
        $this->assertContains('action="/process"', $string);
        $this->assertContains('id="username"', $string);
        $this->assertContains('enctype="multipart/form-data"', $string);
    }

}
