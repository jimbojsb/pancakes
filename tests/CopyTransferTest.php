<?php
use Pancakes\DataTransfer\TransferSpecification;

class CopyTransferTest extends PHPUnit_Framework_TestCase
{
    protected $sourcePdo;
    protected $destinationPdo;
    protected $sourceQueries = [];

    public function setUp()
    {
        $this->sourcePdo = new \Pseudo\Pdo();
        $this->destinationPdo = new \Pseudo\Pdo();

        $this->sourceQueries["SELECT TABLE_NAME FROM information_schema.TABLES FROM information_schema.TABLES WHERE TABLE_SCHEMA='sourcedb' AND TABLE_NAME='table1'"] = [["TABLE_NAME" => "table1"]];
        $this->sourceQueries["SELECT TABLE_NAME FROM information_schema.TABLES FROM information_schema.TABLES WHERE TABLE_SCHEMA='sourcedb' AND TABLE_NAME='table2'"] = [["TABLE_NAME" => "table2"]];


        foreach ($this->sourceQueries as $query => $result) {
            $this->sourcePdo->mock($query, $result);
        }
    }

    public function testCopyAll()
    {

    }
}