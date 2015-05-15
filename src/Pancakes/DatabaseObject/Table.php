<?php
namespace Pancakes\DatabaseObject;

class Table
{
    const ENGINE_INNODB = 'InnoDB';
    const CHARSET_UTF8 = 'utf8';

    /** @var string */
    protected $name;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var string
     */
    protected $engine = self::ENGINE_INNODB;

    /**
     * @var string
     */
    protected $charset = self::CHARSET_UTF8;

    /**
     * @var int
     */
    protected $autoIncrement = 1;

    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @param Schema $schema
     * @param $name
     */
    public function __construct(Schema $schema, $name)
    {
        $this->name = $name;
        $this->schema = $schema;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getQuotedName()
    {
        return "`$this->name`";
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param string $engine
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * @return int
     */
    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * @param int $autoIncrement
     */
    public function setAutoIncrement($autoIncrement)
    {
        $this->autoIncrement = $autoIncrement;
    }

    public function existsOnConnection(\PDO $connection)
    {
        $database = $connection->
        $sql = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='$database' TABLE_NAME='$tableName'";
        $result = $connection->query($sql);
        if ($result->rowCount() == 1) {
            return true;
        }
        return false;
    }


}