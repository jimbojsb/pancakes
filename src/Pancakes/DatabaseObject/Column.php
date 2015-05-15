<?php
namespace Pancakes\DatabaseObject;

class Column
{
    /**
     * @var int
     */
    protected $length;

    /**
     * @var int
     */
    protected $precision;

    /**
     * @var int
     */
    protected $scale;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $options;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var bool;
     */
    protected $signed = true;

    /**
     * @var bool
     */
    protected $nullable = true;

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

    /**
     * @return string
     */
    public function getDefinition()
    {
        $string = $this->getQuotedName() . " ";
        $string .= strtoupper($this->type);
        if ($this->length) {
            $string .= "($this->length)";
        } else if ($this->precision && !$this->scale) {
            $string .= "($this->precision)";
        } else if ($this->scale && $this->precision) {
            $string .= "($this->precision,$this->scale)";
        }

        if (!$this->signed) {
            $string .= " UNSIGNED";
        }

        if (!$this->nullable) {
            $string .= " NOT NULL";
        }

        if ($this->options) {
            $string .= " " . $this->options;
        }

        return $string;
    }

    public function isEqualTo(Column $column)
    {
        return $this->getDefinition() == $column->getDefinition();
    }

    /**
     * @param mixed $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @param int $precision
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;
    }

    /**
     * @param int $scale
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
     * @param string $options
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

    /**
     * @param boolean $signed
     */
    public function setSigned($signed)
    {
        $this->signed = $signed;
    }


    /**
     * @param boolean $nullable
     */
    public function setNullable($nullable)
    {
        $this->nullable = $nullable;
    }

    public static function fromInformationSchemaArray(array $data)
    {
        $column = new self($data["COLUMN_NAME"]);
        if ($data["CHARACTER_LENGTH"]) {
            $column->setLength($data["CHARACTER_LENGTH"]);
        } else if ($data["NUMERIC_SCALE"]) {
            $column->setScale($data["NUMERIC_SCALE"]);
            $column->setPrecision($data["NUMERIC_PRECISION"]);
        }

        if ($data["NULLABLE"] === "YES") {
            $column->setNullable(true);
        } else {
            $column->setNullable(false);
        }

        if ($data["EXTRA"]) {
            $column->options = $data["EXTRA"];
        }

        if (strpos($data["COLUMN_TYPE"], "unsigned") !== false) {
            $column->signed = false;
        } else {
            $column->signed = true;
        }

        return $column;
    }
}