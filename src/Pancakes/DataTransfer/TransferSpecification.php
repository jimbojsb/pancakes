<?php
namespace Pancakes\DataTransfer;

use \Pancakes\DatabaseObject\Table;

class TransferSpecification
{
    /**
     * @var Table
     */
    protected $table;
    protected $columns = [];
    protected $constraints = [];
    protected $transformations = [];

    public function __construct($table)
    {
        if (!($table instanceof Table)) {
            $table = new Table($table);
        }
        $this->table = $table;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }


}