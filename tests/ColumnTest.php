<?php
use \Pancakes\DatabaseObject\Column;

class ColumnTest extends PHPUnit_Framework_TestCase
{
    public function testLengthDefinition()
    {
        $c = new Column("test");
        $c->setLength(20);
        $c->setType("char");
        $this->assertEquals("`test` CHAR(20)", $c->getDefinition());
    }

    public function testPrecisionOnlyDefinition()
    {
        $c = new Column("test");
        $c->setPrecision(11);
        $c->setType("int");
        $this->assertEquals("`test` INT(11)", $c->getDefinition());
    }

    public function testPrecisionAndScaleDefintion()
    {
        $c = new Column("test");
        $c->setPrecision(6);
        $c->setScale(2);
        $c->setType("decimal");
        $this->assertEquals("`test` DECIMAL(6,2)", $c->getDefinition());
    }

    public function testUnsignedColumnDefinition()
    {
        $c = new Column("test");
        $c->setLength(20);
        $c->setType("bigint");
        $c->setSigned(false);
        $this->assertEquals("`test` BIGINT(20) UNSIGNED", $c->getDefinition());
    }

    public function testColumnOptionsDefinition()
    {
        $c = new Column("test");
        $c->setLength(11);
        $c->setType("int");
        $c->setOptions("AUTO_INCREMENT");
        $c->setNullable(false);
        $this->assertEquals("`test` INT(11) NOT NULL AUTO_INCREMENT", $c->getDefinition());

        $c = new Column("test");
        $c->setLength(11);
        $c->setType("int");
        $c->setOptions("AUTO_INCREMENT");
        $c->setNullable(true);
        $this->assertEquals("`test` INT(11) AUTO_INCREMENT", $c->getDefinition());
    }
}