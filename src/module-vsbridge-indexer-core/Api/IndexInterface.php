<?php
/**
 * @package   Divante\VsbridgeIndexerCore
 * @author    Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgeIndexerCore\Api;

/**
 * Interface IndexInterface
 */
interface IndexInterface
{
    const DUMMY_INDEX_TYPE = '_doc';

    /**
     * Retrieve Index Name
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve Index Alias
     *
     * @return string
     */
    public function getAlias();

    /**
     * @return boolean
     */
    public function isNew();

    /**
     * @return \Divante\VsbridgeIndexerCore\Api\Index\TypeInterface[]
     */
    public function getTypes();

    /**
     * @param $typeName
     *
     * @return \Divante\VsbridgeIndexerCore\Api\Index\TypeInterface
     * @throws \InvalidArgumentException When the type does not exists.
     */
    public function getType($typeName);
}
