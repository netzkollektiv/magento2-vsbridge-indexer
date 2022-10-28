<?php
/**
 * @package  Divante\VsbridgeIndexerCatalog
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgeIndexerCatalog\Model\ResourceModel\Category;

use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Category;

/**
 * Class DisabledParents
 */
class DisabledParents
{

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ActiveSelectModifier
     */
    private $activeSelectModifier;

    /**
     * Category constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ActiveSelectModifier $activeSelectModifier
    ) {
        $this->resource = $resourceConnection;
        $this->activeSelectModifier = $activeSelectModifier;
    }

    /**
     * @param array $categoryIds
     *
     * @return array
     */
    public function hasDisabledParents(array $categoryIds, $storeId)
    {
        $categoryIds = array_map('intval', $categoryIds);
        $categoryIds = array_filter($categoryIds, function ($v) {
            return $v !== Category::TREE_ROOT_ID;
        });

        if (empty($categoryIds)) {
            return false;
        }

        $result = $this->getDisabledParents($categoryIds, $storeId);

        return count(array_diff($categoryIds, $result)) > 0;
    }

    /**
     * @param array $categoryIds
     *
     * @return array
     */
    public function getDisabledParents(array $categoryIds, $storeId)
    {
        $categoryTable = $this->resource->getTableName('catalog_category_entity');

        $select = $this->getConnection()->select()->from(
            ['entity' => $categoryTable],
            ['entity_id']
        )->where('entity.entity_id in (?)', $categoryIds);

        $this->activeSelectModifier->execute($select, $storeId);
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }
}
