<?php

namespace Pop\Form\Test;

use Pop\Form\Fields;
use Pop\Db;

class FieldsTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructorAddFieldsFromTable()
    {
        TestAsset\Users::setDb(Db\Db::sqliteConnect(['database' => __DIR__ . '/tmp/db.sqlite']));
        $fields = Fields::getConfigFromTable(TestAsset\Users::getTableInfo());
        $this->assertEquals(4, count($fields));
    }

}
