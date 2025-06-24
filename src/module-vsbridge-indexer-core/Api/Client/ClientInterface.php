<?php

/**
 * @package   Divante\VsbridgeIndexerCore
 * @author    Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgeIndexerCore\Api\Client;

use Divante\VsbridgeIndexerCore\Exception\ConnectionDisabledException;

/**
 * Interface ClientInterface
 */
interface ClientInterface
{
    /**
     * @param array $bulkParams
     *
     * @return array
     * @throws ConnectionDisabledException
     */
    public function bulk(array $bulkParams);

    /**
     * @param string $indexName
     * @param int|string $value
     *
     * @return void
     */
    public function changeRefreshInterval(string $indexName, $value);

    /**
     * @param string $indexName
     * @param int $value
     *
     * @return void
     */
    public function changeNumberOfReplicas(string $indexName, int $value);

    /**
     * @param string $indexName
     * @param array  $indexSettings
     *
     * @return void
     * @throws ConnectionDisabledException
     */
    public function createIndex(string $indexName, array $indexSettings);


    /**
     * Retrieve information about cluster health
     *
     * @return array
     */
    public function getClustersHealth(): array;

    /**
     * Retrieve the list of all index having a specified alias.
     *
     * @param string $indexAlias Index alias.
     *
     * @return string[]
     */
    public function getIndicesNameByAlias(string $indexAlias): array;

    /**
     * Retrieve information about index settings
     *
     * @param string $indexName
     *
     * @return array
     */
    public function getIndexSettings(string $indexName): array;

    /**
     * Retrieve max queue size for master node
     *
     * @return int
     */
    public function getMasterMaxQueueSize(): int;

    /**
     * @param array $aliasActions
     *
     * @return void
     */
    public function updateAliases(array $aliasActions);

    /**
     * @param string $indexName
     *
     * @return void
     */
    public function refreshIndex(string $indexName);

    /**
     * @param string $indexName
     *
     * @return bool
     */
    public function indexExists(string $indexName);

    /**
     * @param string $indexName
     *
     * @return array
     */
    public function deleteIndex(string $indexName);

    /**
     * @param string $indexName
     * @param array $mapping
     *
     * @return void
     */
    public function putMapping(string $indexName, array $mapping);

    /**
     * @param array $params
     *
     * @return void
     */
    public function deleteByQuery(array $params);
}
