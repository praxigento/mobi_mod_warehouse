<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\Magento\CatalogInventory\Model\Stock;

/**
 * Create composite primary key.
 */
class Status
{
    public function aroundGetId(
        \Magento\CatalogInventory\Model\Stock\Status $subject,
        \Closure $proceed
    ) {
        $proceed(); // prevent plugins interruption
        /* compose complex primary key */
        $productId = $subject->getProductId();
        $wsId = $subject->getWebsiteId();
        $stockId = $subject->getData($subject::KEY_STOCK_ID);
        $result = "$productId:$wsId:$stockId";
        return $result;
    }
}