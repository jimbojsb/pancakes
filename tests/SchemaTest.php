<?php
use Pancakes\DatabaseObject\Schema;
use Pancakes\DatabaseObject\Table;
use Pseudo\Pdo;

class SchemaTest extends PHPUnit_Framework_TestCase
{
    public function testDetectNameFromConnection()
    {
        $connection = new Pdo();
        $connection->mock("SELECT DATABASE()", [["DATABASE()" => "test"]]);
        $schema = Schema::fromConnection($connection);
        $this->assertEquals("test", $schema->getName());
    }

    public function testAddAndRetrieveTable()
    {
        $schema = new Schema("test");
        $table = new Table("test_table");
        $schema->addTable($table);
        $this->assertEquals(spl_object_hash($table), spl_object_hash($schema->getTable("test_table")));
    }

    public function testCreationFromInformationSchema()
    {
        $connection = new Pdo();
        $connection->mock("SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA='test'", [
            ["TABLE_NAME" => "table1"],
            ["TABLE_NAME" => "table2"]
        ]);
        $connection->mock("SELECT COLUMN_NAME, ORDINAL_POSITION, COLUMN_DEFAULT, IS_NULLABLE, DATA_TYPE, NUMERIC_PRECISION, NUMERIC_SCALE, COLUMN_TYPE, EXTRA
                           FROM COLUMNS WHERE TABLE_SCHEMA='test' AND TABLE_NAME='table1'", [
        ]);
        $schema = Schema::fromConnection($connection, 'test');
        $this->assertEquals(2, count($schema->getTables()));
        $tables = $schema->getTables();
        $this->assertEquals("table1", $tables[0]->getName());
        $this->assertEquals("table2", $tables[1]->getName());
    }
}