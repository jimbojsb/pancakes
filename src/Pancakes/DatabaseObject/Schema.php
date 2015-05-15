<?php
namespace Pancakes\DatabaseObject;

class Schema
{
    protected $name;
    protected $tables = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Table $table
     */
    public function addTable(Table $table)
    {
        $this->tables[$table->getName()] = $table;
    }

    /**
     * @param $tableName
     * @return Table
     */
    public function getTable($tableName)
    {
        return $this->tables[$tableName];
    }

    /**
     * @param Table|string $table
     * @return bool
     */
    public function hasTable($table)
    {
        if ($table instanceof Table) {
            $table = $table->getName();
        }
        if ($this->tables[$table]) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getTables()
    {
        return array_values($this->tables);
    }

    /**
     * @param \PDO $connection
     * @param string $databaseName
     * @return Schema
     */
    public static function fromConnection(\PDO $connection, $databaseName = null)
    {
        if ($databaseName === null) {
            $databaseName = $connection->query("SELECT DATABASE()")->fetchColumn();
        }
        $schema = new self($databaseName);
        $tableList = $connection->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='$databaseName'");
        if ($tableList) {
            while ($table = $tableList->fetchColumn()) {
                $tableObj = new Table($schema, $table);
                $schema->addTable($tableObj);
            }
        }
        return $schema;
    }
}