<?php

namespace Pop\Form\Test;

use Pop\Form\Filter\Filter;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{

    public function testHasAndGetters()
    {
        $filter = new Filter('htmlentities', ENT_QUOTES, 'email', 'email');
        $this->assertTrue($filter->hasCallable());
        $this->assertTrue($filter->hasParams());
        $this->assertTrue($filter->hasExcludeByType());
        $this->assertTrue($filter->hasExcludeByName());
        $this->assertNotEmpty($filter->getCallable());
        $this->assertNotEmpty($filter->getParams());
        $this->assertNotEmpty($filter->getExcludeByType());
        $this->assertNotEmpty($filter->getExcludeByName());
    }

    public function testFilterArrayValue()
    {
        $filter = new Filter('strip_tags');
        $values = $filter->filter([
            '<strong>Header</strong>',
            '<em>Subheader</em>'
        ]);

        $this->assertEquals('Header', $values[0]);
        $this->assertEquals('Subheader', $values[1]);
    }

}