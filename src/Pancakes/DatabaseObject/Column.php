<?php
namespace Pancakes\DatabaseObject;

class Column
{
    const TYPE_VARCHAR = 'VARCHAR';
    const TYPE_INT = 'INT';
    const TYPE_BIGINT = 'BIGINT';
    const TYPE_TINYTEXT = 'TINYTEXT';
    const TYPE_TEXT = 'TEXT';
    const TYPE_BLOB = 'BLOB';
    const TYPE_CHAR = 'CHAR';
    const TYPE_TINYINT = 'TINYINT';
    const TYPE_DATE = 'DATE';
    const TYPE_DATETIME = 'DATETIME';
    const TYPE_TIMESTAMP = 'TIMESTAMP';

    const OPTION_UNSIGNED = 'UNSIGNED';

    protected $length;
    protected $precision;
    protected $scale;
    protected $type;
    protected $options = [];
    protected $defaultValue;

    /** @var string */
    protected $name;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getQuotedName()
    {
        return "`$this->name`";
    }

    public function getDefinition()
    {
        $string = $this->getQuotedName() . " ";
        $string .= strtoupper($this->type);
        if ($this->hasLength()) {
            $string .= "($this->length)";
        }
        if ($this->options) {
            $string .= " " . implode(" ", $this->options);
        }

        return $string;
    }

    /**
     * @param mixed $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @param mixed $precision
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;
    }

    /**
     * @param mixed $scale
     */
    public function setScale($scale)
    {
        $this->scale = $scale;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }



    protected function hasLength()
    {
        if (in_array($this->type, [
            self::TYPE_INT,
            self::TYPE_TINYINT,
            self::TYPE_VARCHAR,
            self::TYPE_CHAR,
            self::TYPE_BIGINT
        ])) {
            return true;
        } else {
            return false;
        }
    }

    public static function fromInformationSchemaArray(array $data)
    {
        $column = new self(
            $data["COLUMN_NAME"],
            strtoupper($data["DATA_TYPE"]),

        );
    }
}