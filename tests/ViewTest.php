<?php

namespace Pop\Form\Test;

use Pop\Form\Form;

class ViewTest extends \PHPUnit_Framework_TestCase
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
