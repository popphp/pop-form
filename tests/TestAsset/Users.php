<?php

namespace Pop\Form\Test\TestAsset;

use Pop\Db\Db;
use Pop\Db\Adapter;
use Pop\Db\Record;

class Users extends Record
{

    public function __construct(array $columns = null, Adapter\AbstractAdapter $db = null)
    {
        if (null === $db) {
            $db = Db::connect('sqlite', ['database' => __DIR__ . '/../tmp/db.sqlite']);
        }
        parent::__construct($columns, $db);
    }

}