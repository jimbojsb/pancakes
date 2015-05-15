<?php
namespace Pancakes\DataTransfer;

use \Pancakes\DatabaseObject\Table;
use \Pancakes\DatabaseObject\Schema;

class TransferSpecification
{
    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var array
     */
    protected $tables;
}