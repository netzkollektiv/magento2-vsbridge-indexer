<?php

namespace Divante\VsbridgeIndexerCore\Elasticsearch;

use Divante\VsbridgeIndexerCore\Api\Client\ClientInterface;
use Divante\VsbridgeIndexerCore\Model\ElasticsearchResolverInterface;
use OpenSearch\Client as OpenSearchClient;
use OpenSearch\Common\Exceptions\Missing404Exception;

/**
 * Class Client
 */
class Client implements ClientInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ElasticsearchResolverInterface
     */
    private $esVersionResolver;

    /**
     * Client constructor.
     *
     * @param ElasticsearchResolverInterface $esVersionResolver
     * @param Client $client
     */
    public function __construct(
        ElasticsearchResolverInterface $esVersionResolver,
        OpenSearchClient $client
    ) {
        $this->client = $client;
        $this->esVersionResolver = $esVersionResolver;
    }

    /**
     * @param array $bulkParams
     *
     * @return array
     */
    public function bulk(array $bulkParams)
    {
        return $this->client->bulk($bulkParams);
    }

    /**
     * @param string $indexName
     * @param int|string $value
     *
     * @return void
     */
    public function changeRefreshInterval(string $indexName, $value)
    {
        $this->client->indices()->putSettings(['index' => $indexName, 'body' => ['refresh_interval' => $value]]);
    }

    /**
     * @param string $indexName
     * @param int $value
     *
     * @return void
     */
    public function changeNumberOfReplicas(string $indexName, int $value)
    {
        $this->client->indices()->putSettings(['index' => $indexName, 'body' => ['number_of_replicas' => $value]]);
    }

    /**
     * @param $indexName
     * @param array $indexSettings
     */
    public function createIndex(string $indexName, array $indexSettings)
    {
        $this->client->indices()->create(
            [
                'index' => $indexName,
                'body'  => $indexSettings,
            ]
        );
    }

    /**
     * Retrieve information about cluster health
     *
     * @return array
     */
    public function getClustersHealth(): array
    {
        return $this->client->cat()->health();
    }

    /**
     * @param string $indexAlias
     *
     * @return array
     */
    public function getIndicesNameByAlias(string $indexAlias): array
    {
        $indices = [];

        try {
            $indices = $this->client->indices()->getMapping(['index' => $indexAlias]);
        } catch (Missing404Exception $e) {
        }

        return array_keys($indices);
    }

    /**
     * @param string $indexName
     *
     * @return array
     */
    public function getIndexSettings(string $indexName): array
    {
        return $this->client->indices()->getSettings(['index' => $indexName]);
    }

    /**
     * @return int
     */
    public function getMasterMaxQueueSize(): int
    {
        $master = $this->client->cat()->master();
        $masterNode = $this->client->nodes()->info(['node_id' => $master[0]['id']]);
        return $masterNode['nodes'][$master[0]['id']]['thread_pool']['search']['max_queue_size'] ?? 0;
    }

    /**
     * @param array $aliasActions
     */
    public function updateAliases(array $aliasActions)
    {
        $this->client->indices()->updateAliases(['body' => ['actions' => $aliasActions]]);
    }

    /**
     * @param string $indexName
     */
    public function refreshIndex(string $indexName)
    {
        $this->client->indices()->refresh(['index' => $indexName]);
    }

    /**
     * @param string $indexName
     *
     * @return bool
     */
    public function indexExists(string $indexName)
    {
        return $this->client->indices()->exists(['index' => $indexName]);
    }

    /**
     * @param string $indexName
     *
     * @return array
     */
    public function deleteIndex(string $indexName)
    {
        return $this->client->indices()->delete(['index' => $indexName]);
    }

    /**
     * @param string $indexName
     * @param array $mapping
     */
    public function putMapping(string $indexName, array $mapping)
    {
        $this->client->indices()->putMapping([
            'index' => $indexName,
            'body'  => $mapping
        ]);
    }

    /**
     * @param array $params
     */
    public function deleteByQuery(array $params)
    {
        $this->client->deleteByQuery($params);
    }
}
