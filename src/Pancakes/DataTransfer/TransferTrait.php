<?php
namespace Pancakes\DataTransfer;

trait TransferTrait
{
    /** @var  \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var  \PDO */
    protected $sourceConnection;

    /** @var  \PDO */
    protected $destinationConnection;

    /** @var array */
    protected $transferSpecifications = [];

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

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

    /**
     * @param TransferSpecification $spec
     */
    public function addTransferSpecification(TransferSpecification $spec)
    {
        $this->transferSpecifications[] = $spec;
    }

    public function transfer($tableName)
    {
        $spec = new TransferSpecification($tableName);
        $this->addTransferSpecification($spec);
        return $spec;
    }

    protected function getMaxPacket()
    {
        $sql = "SHOW VARIABLES LIKE 'max_allowed_packet'";
        $maxPacket = $this->destinationConnection->query($sql)->fetch(\PDO::FETCH_ASSOC)["Value"];
        return $maxPacket;
    }



}