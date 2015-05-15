<?php
use Pancakes\DataTransfer\TransferTrait;
use Pseudo\Pdo;

class TransferTraitTest extends PHPUnit_Framework_TestCase
{
    use TransferTrait;

    public function testConnectionSetters()
    {
        $pdo = new Pdo();
        $this->setDestinationConnection($pdo);
        $this->setSourceConnection($pdo);

        $this->assertInstanceOf('PDO', $this->sourceConnection);
        $this->assertInstanceOf('PDO', $this->destinationConnection);
    }
}