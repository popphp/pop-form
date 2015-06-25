<?php

namespace Pop\Form\Test;

use Pop\Form\Element\CheckboxSet;

class CheckboxSetTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\CheckboxSet', $checkbox);
    }

    public function testMarkedArray()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], null, ['Red', 'Blue']);
        $checkbox->setAttribute('class', 'check-box');
        $checkbox->setAttribute('tabindex', '1');
        $this->assertInstanceOf('Pop\Form\Element\CheckboxSet', $checkbox);
    }

    public function testMarkedNoArray()
    {
        $checkbox = new CheckboxSet('my_checkbox', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], null, 'Red');
        $checkbox->setAttributes([
            'class'    => 'check-box',
            'tabindex' => '1'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\CheckboxSet', $checkbox);
    }

}