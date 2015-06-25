<?php

namespace Pop\Form\Test;

use Pop\Form\Element\RadioSet;

class RadioSetTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $radio = new RadioSet('my_radio', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ]);
        $radio->setAttribute('class', 'radio-btn');
        $radio->setAttribute('tabindex', '1');
        $this->assertInstanceOf('Pop\Form\Element\RadioSet', $radio);
    }

    public function testMarked()
    {
        $radio = new RadioSet('my_radio', [
            'Red' => 'Red', 'White' => 'White', 'Blue' => 'Blue'
        ], null, 'Red');
        $radio->setAttributes([
            'class'    => 'radio-btn',
            'tabindex' => '1'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\RadioSet', $radio);
    }
}