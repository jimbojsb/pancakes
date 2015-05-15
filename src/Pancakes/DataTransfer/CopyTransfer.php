<?php
namespace Pancakes\DataTransfer;

use \Pancakes\DatabaseObject\Schema;

class CopyTransfer
{
    use TransferTrait;
    use \Psr\Log\LoggerAwareTrait;
    use \Psr\Log\LoggerTrait;


    /**
     * @var TransferSpecification array
     */
    protected $transferSpecs = [];


    public function execute()
    {
        $sourceSchema = Schema::fromConnection($this->sourceConnection);
        $destinationSchema = Schema::fromConnection($this->destinationConnection);

        $destinationMaxPacket = $this->getMaxPacket();
        $this->debug("Destination max packet size: $destinationMaxPacket");

        foreach ($this->transferSpecs as $spec) {
            $table = $spec->getTable();
            if ($sourceSchema->hasTable($table)) {
                if ($destinationSchema->hasTable($table)) {
                } else {
                    $this->info("Table " . $table->getName() . " does not exist in destination, it will be skipped");
                }
            } else {
                $this->info("Table " . $table->getName() . " does not exist in source, it will be skipped");
            }
        }
    }

    public function __execute()
    {
        $this->validateTargets();
        $source = $input->getArgument('source_connection');
        $destination = $input->getArgument('destination_connection');
        $transferConfigFile = $input->getArgument('transfer_config');

        $sourceConnection = $this->getConnection($source);
        $destinationConnection = $this->getConnection($destination);

        if (!($sourceConnection instanceof \PDO) || !($destinationConnection instanceof \PDO)) {
            throw new \Exception('Unable to get connection information for source or destination');
        }

        $maxPacket = $destinationConnection->query("SHOW VARIABLES LIKE '%packet%'")->fetch(\PDO::FETCH_ASSOC);
        $maxPacket = $maxPacket["Value"];

        $destinationConnection->query("SET FOREIGN_KEY_CHECKS=0");

        $transferConfig = include $transferConfigFile;
        if (!is_array($transferConfig)) {
            throw new \Exception('Error loading transfer config file');
        }
        $output->writeln('<info>Found ' . count($transferConfig["tables"]) . ' tables to transfer</info>');
        foreach ($transferConfig["tables"] as $table => $params) {

            if (is_int($table)) {
                $table = $transferConfig["tables"][$table];
                $params = array();
            }

            $output->writeln('<info>Transferring ' . $table . '</info>');
            $sourceTable = $sourceConnection->query("DESCRIBE $table");
            if ($sourceTable) {
                $sourceTableSchema = array();
                while ($row = $sourceTable->fetch(\PDO::FETCH_ASSOC)) {
                    $sourceTableSchema[] = $row;
                }
            } else {
                $output->writeln('<error>Skipping table: `' . $table . '` - source table does not exist</error>');
                continue;
            }

            $realDestinationTable = ($params["destination_table"] ?: $table);
            $destinationTable = $destinationConnection->query("DESCRIBE $realDestinationTable");
            if ($destinationTable) {
                $destinationTableSchema = array();
                while ($row = $destinationTable->fetch(\PDO::FETCH_ASSOC)) {
                    $destinationTableSchema[] = $row;
                }
            } else {
                $output->writeln('<error>Skipping table: `' . $table . '` - destination table does not exist`</error>');
                continue;
            }

            $columnsToTransfer = array();
            foreach ($sourceTableSchema as $srcColumn) {
                if (is_array($params["column_exclude"]) && in_array($srcColumn["Field"], $params["column_exclude"])) {
                    continue;
                }
                $foundColumn = false;
                foreach ($destinationTableSchema as $destinationColumn) {
                    $realDestinationColumn = $params["column_map"][$srcColumn["Field"]] ?: $srcColumn['Field'];
                    if ($realDestinationColumn === $destinationColumn["Field"]) {
                        if ($srcColumn["Type"] !== $destinationColumn["Type"]) {
                            $output->writeln('<comment>--- Definition mismatch: `' . $srcColumn['Field'] . '` - source is ' . $srcColumn['Type'] . ' and destination is ' . $destinationColumn['Type'] . '. Continuing anyway...</comment>');
                        }
                        $columnsToTransfer[] = $srcColumn;
                        $foundColumn = true;
                        break;
                    }
                }
                if (!$foundColumn) {
                    $output->writeln('<comment>--- Skipping `' . $srcColumn['Field'] . '`: column does not exist in destination</comment>');
                }
            }

            $destinationConnection->query("TRUNCATE TABLE $realDestinationTable");
            $destinationConnection->query("ALTER TABLE $realDestinationTable DISABLE KEYS");


            $selectCols = array();
            $insertCols = array();
            foreach ($columnsToTransfer as $col) {
                $selectCols[] = "`" . $col["Field"] . "`";
                $insertCols[] = "`" . ($params["column_map"][$col["Field"]] ?: $col["Field"]) . "`";
            }
            $insertSqlBase = "INSERT INTO $realDestinationTable (" . implode(",", $insertCols) . ") VALUES ";
            $insertSql = $insertSqlBase;
            $selectSql = "SELECT " . implode(",", $selectCols) . " FROM $table";
            if ($params["where"]) {
                $selectSql .= " " . $params["where"];
            }

            $rows = $sourceConnection->query($selectSql);
            $output->writeln('<comment>--- Transferring ' . $rows->rowCount() . ' rows</comment>');
            $expectedRowCount = 0;
            while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
                foreach ($row as $key => &$val) {
                   if ($params["column_transform"][$key]) {
                       $val = $params["column_transform"][$key]($val, $row);
                   }
                   if ($val === null) {
                       $val = 'NULL';
                   } else {
                       $val = "'" . addslashes($val) . "'";
                   }
                }
                $vals = array_values($row);
                $rowData = "(" . implode(",", $vals) . ")";

                if (strlen($insertSql) + strlen($rowData) + 1 < $maxPacket) {
                    $expectedRowCount++;
                    $insertSql .= $rowData . ",";
                } else {
                    $insertSql = substr($insertSql, 0, strlen($insertSql) - 1);
                    $result = $destinationConnection->query($insertSql);
                    if (!$result) {
                        throw new \Exception(print_r($destinationConnection->errorInfo(), true));
                    }
                    if ($result->rowCount() != $expectedRowCount) {
                        throw new \Exception("Expected to see $expectedRowCount inserted, only saw ". $result->rowCount());
                    } else {
                        $output->writeln('<comment>---- Bulk inserted ' . $result->rowCount() . ' rows</comment>');
                    }
                    $expectedRowCount = 0;
                    $expectedRowCount++;
                    $insertSql = $insertSqlBase;
                    $insertSql .= $rowData . ",";
                }
            }
            if ($insertSql != $insertSqlBase) {
                $insertSql = substr($insertSql, 0, strlen($insertSql) - 1);
                $result = $destinationConnection->query($insertSql);
                if (!$result) {

                    throw new \Exception(print_r($destinationConnection->errorInfo(), true));
                }
                if ($result->rowCount() != $expectedRowCount) {
                    throw new \Exception("Expected to see $expectedRowCount inserted, only saw ". $result->rowCount());
                }
            }
            $destinationConnection->query("ALTER TABLE $realDestinationTable ENABLE KEYS");
        }
        $destinationConnection->query("SET FOREIGN_KEY_CHECKS=1");
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }


}