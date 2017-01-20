<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model\ResourceModel\Stock\Status;


class Collection
{
    public function aroundGetItemId(
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status\Collection $subject,
        \Closure $proceed,
        \Magento\CatalogInventory\Model\Stock\Status $item
    ) {
        $proceed($item); // call original method to use other plugins
        /* compose complex primary key */
        $productId = $item->getProductId();
        $wsId = $item->getWebsiteId();
        $stockId = $item->getData($item::KEY_STOCK_ID);
        $result = "$productId:$wsId:$stockId";
        return $result;
    }
}