<?php
use \Pancakes\DatabaseObject\Column;

class ColumnTest extends PHPUnit_Framework_TestCase
{
    public function testGetQuotedName()
    {
        $c = new Column('test', Column::TYPE_VARCHAR, 64);
        $expected = "`test`";
        $this->assertEquals($expected, $c->getQuotedName());
    }

    public function testRenderVarchar()
    {
        $c = new Column('test', Column::TYPE_VARCHAR, 64);
        $expected = "`test` VARCHAR(64)";
        $this->assertEquals($expected, $c->getDefinition());
    }

    public function testRenderInt()
    {
        $c = new Column('test', Column::TYPE_INT, 11);
        $expected = "`test` INT(11)";
        $this->assertEquals($expected, $c->getDefinition());
    }

    public function testRenderChar()
    {
        $c = new Column('test', Column::TYPE_CHAR, 64);
        $expected = "`test` CHAR(64)";
        $this->assertEquals($expected, $c->getDefinition());
    }

    public function testRenderTinyInt()
    {
        $c = new Column('test', Column::TYPE_TINYINT, 1);
        $expected = "`test` TINYINT(1)";
        $this->assertEquals($expected, $c->getDefinition());
    }

    public function testRenderBigInt()
    {
        $c = new Column('test', Column::TYPE_BIGINT, 20);
        $expected = "`test` BIGINT(20)";
        $this->assertEquals($expected, $c->getDefinition());
    }

    public function testRenderDate()
    {
        $c = new Column('test', Column::TYPE_DATE);
        $expected = "`test` DATE";
        $this->assertEquals($expected, $c->getDefinition());
    }

    public function testRenderDateTime()
    {
        $c = new Column('test', Column::TYPE_DATETIME);
        $expected = "`test` DATETIME";
        $this->assertEquals($expected, $c->getDefinition());
    }

    public function testRenderTimestamp()
    {
        $c = new Column('test', Column::TYPE_TIMESTAMP);
        $expected = "`test` TIMESTAMP";
        $this->assertEquals($expected, $c->getDefinition());
    }
}