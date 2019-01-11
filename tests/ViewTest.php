<?php

namespace Pop\Form\Test;

use Pop\Form\Form;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{

    public function testSetStreamTemplate()
    {

        $form = Form::createFromConfig([
            'username' => [
                'type'     => 'text',
                'label'    => 'Username:',
                'required' => true
            ],
            'file' => [
                'type'  => 'file',
                'label' => 'File:'
            ],
            'submit' => [
                'type'  => 'submit',
                'value' => 'SUBMIT'
            ]
        ]);
        $formData = $form->prepareForView();
        $this->assertEquals(5, count($formData));
    }

}
