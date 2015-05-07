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

        $this->sourceQueries["SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='sourcedb' AND TABLE_NAME='employees'"] = [["TABLE_NAME" => "employees"]];
        $this->sourceQueries["SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA='sourcedb' AND TABLE_NAME='dept_emp'"] = [["TABLE_NAME" => "dept_emp"]];
        $this->sourceQueries["SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA='sourcedb' AND TABLE_NAME='dept_manager'"] = [["TABLE_NAME" => "dept_manager"]];
        $this->sourceQueries["SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA='sourcedb' AND TABLE_NAME='salaries'"] = [["TABLE_NAME" => "salaries"]];
        $this->sourceQueries["SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA='sourcedb' AND TABLE_NAME='departments'"] = [["TABLE_NAME" => "departments"]];
        $this->sourceQueries["SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA='sourcedb' AND TABLE_NAME='titles'"] = [["TABLE_NAME" => "titles"]];

        foreach ($this->sourceQueries as $query => $result) {
            $this->sourcePdo->mock($query, $result);
        }
    }

    public function testCopyAll()
    {
    }
}