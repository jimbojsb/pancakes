<?php
class TransferTraitTest extends PHPUnit_Framework_TestCase
{
    use \Pancakes\DataTransfer\TransferTrait;

    public function testGetMaxPacket()
    {
        $pdo = new Pseudo\Pdo();
        $pdo->mock("SHOW VARIABLES LIKE 'max_allowed_packet'", [["Value" => 12345]]);
        $this->setDestinationConnection($pdo);
        $this->assertEquals(12345, $this->getMaxPacket());
    }

    public function testTransfer()
    {
        $spec = $this->transfer('test');
        $this->assertInstanceOf('Pancakes\DataTransfer\TransferSpecification', $spec);
        $this->assertInstanceOf('\Pancakes\DatabaseObject\Table', $spec->getTable());
        $this->assertEquals('test', $spec->getTable()->getName());
    }
}