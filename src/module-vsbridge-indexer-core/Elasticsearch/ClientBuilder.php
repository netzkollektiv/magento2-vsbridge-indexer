<?php

/**
 * @package   Divante\VsbridgeIndexerCore
 * @author    Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgeIndexerCore\Elasticsearch;

use Divante\VsbridgeIndexerCore\Api\Client\BuilderInterface as ClientBuilderInterface;
use OpenSearch\ClientBuilder as OpenSearchClientBuilder;

/**
 * Class ClientBuilder
 */
class ClientBuilder implements ClientBuilderInterface
{
    /**
     * @var array
     */
    private $defaultOptions = [
        'host' => 'localhost',
        'port' => '9200',
        'enable_http_auth' => false,
        'auth_user' => null,
        'auth_pwd' => null,
        'timeout' => 30,        // ten second timeout
        'connect_timeout' => 30,
        'ssl_verification' => true
    ];

    /**
     * @inheritdoc
     */
    public function build(array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);
        $esClientBuilder = OpenSearchClientBuilder::create();
        $host = $this->getHost($options);

        if (!empty($host)) {
            $esClientBuilder->setHosts([$host]);
        }

        if (isset($options['ssl_verification']) && !$options['ssl_verification']) {
            $esClientBuilder->setSSLVerification(false);
        }

        return $esClientBuilder->build();
    }

    /**
     * Return hosts config used to connect to the cluster.
     *
     * @param array $options Client options.
     *
     * @return array
     */
    private function getHost(array $options)
    {
        $scheme = 'http';

        if (isset($options['enable_https_mode']) && $options['enable_https_mode']) {
            $scheme = 'https';
        } elseif (isset($options['scheme'])) {
            $scheme = $options['scheme'];
        }

        $user = $options['auth_user'] ?? null;
        $pass = $options['auth_pass'] ?? null;
        $host = $options['host'] ?? 'localhost';
        $port = $options['port'] ?? null;

        $auth = '';
        if ($options['enable_http_auth']) {
            $auth = $user;
            if ($pass !== null) {
                $auth .= ':' . $pass;
            }
            $auth .= '@';
        }

        $uri = $scheme . '://' . $auth . $host;
        if ($port !== null) {
            $uri .= ':' . $port;
        }
        return $uri;
    }
}
