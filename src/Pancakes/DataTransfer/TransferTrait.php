<?php
namespace Pancakes\DataTransfer;

trait TransferTrait
{
    /** @var  \PDO */
    protected $sourceConnection;

    /** @var  \PDO */
    protected $destinationConnection;

    /**
     * @var array
     */
    protected $tables = [];

    /**
     * @param \PDO $sourceConnection
     */
    public function setSourceConnection(\PDO $sourceConnection)
    {
        $this->sourceConnection = $sourceConnection;
    }

    /**
     * @param \PDO $destinationConnection
     */
    public function setDestinationConnection(\PDO $destinationConnection)
    {
        $this->destinationConnection = $destinationConnection;
    }

    public function addTable($tableName, $options = [])
    {
        $this->tables[$tableName] = $options;
    }

    protected function getMaxPacket()
    {

    }
}