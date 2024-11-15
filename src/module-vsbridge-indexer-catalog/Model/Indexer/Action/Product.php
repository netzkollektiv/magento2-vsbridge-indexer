<?php

namespace Divante\VsbridgeIndexerCatalog\Model\Indexer\Action;

use Divante\VsbridgeIndexerCatalog\Model\ResourceModel\Product as ResourceModel;
use Divante\VsbridgeIndexerCore\Indexer\RebuildActionInterface;

/**
 * Class Product
 */
class Product implements RebuildActionInterface
{
    /**
     * @var ResourceModel
     */
    private $resourceModel;

    /**
     * Product constructor.
     *
     * @param ResourceModel $resourceModel
     */
    public function __construct(ResourceModel $resourceModel)
    {
        $this->resourceModel = $resourceModel;
    }

    /**
     * @param int $storeId
     * @param array $productIds
     *
     * @return \Generator
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function rebuild(int $storeId, array $productIds) : \Traversable
    {
        $lastProductId = 0;

        // Ensure to reindex also the parents product ids
        if (!empty($productIds)) {
            $productIds = $this->getProductIds($productIds);
        }

        do {
            $products = $this->resourceModel->getProducts($storeId, $productIds, $lastProductId);

            /** @var array $product */
            foreach ($products as $product) {
                $lastProductId = (int)$product['entity_id'];
                $product['id'] = $lastProductId;

                $product['attribute_set_id'] = (int)$product['attribute_set_id'];
                $product['media_gallery'] = [];

                unset($product['required_options']);
                unset($product['has_options']);
                yield $lastProductId => $product;
            }
        } while (!empty($products));
    }

    /**
     * @param array $childrenIds
     *
     * @return array
     */
    private function getProductIds(array $childrenIds)
    {
        $parentIds = $this->resourceModel->getRelationsByChild($childrenIds);

        if (!empty($parentIds)) {
            $parentIds = array_map('intval', $parentIds);
        }

        return array_unique(array_merge($childrenIds, $parentIds));
    }
}
