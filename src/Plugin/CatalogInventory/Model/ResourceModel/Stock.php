<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model\ResourceModel;

/**
 * Wrap methods of \Magento\CatalogInventory\Model\ResourceModel\Stock
 * to update qty in inventory.
 */
class Stock
{
    /** @var  \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $manStock
    ) {
        $this->_manStock = $manStock;
    }

    /**
     * Filter locked items by stock ID.
     *
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock $subject
     * @param \Closure $proceed
     * @param int[] $productIds
     * @param int $websiteId
     */
    public function aroundLockProductsStock(
        \Magento\CatalogInventory\Model\ResourceModel\Stock $subject,
        \Closure $proceed,
        $productIds,
        $websiteId
    ) {
        if (empty($productIds)) {
            return [];
        }
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $conn */
        $conn = $subject->getConnection();
        $itemTable = $subject->getTable('cataloginventory_stock_item');
        $productTable = $subject->getTable('catalog_product_entity');
        /** @var \Magento\Framework\DB\Select $select */
        $select = $conn->select();
        $select->from(['si' => $itemTable]);
        $select->join(['p' => $productTable], 'p.entity_id=si.product_id', ['type_id']);
        $select->where('website_id=?', $websiteId);
        $select->where('product_id IN(?)', $productIds);
        $select->forUpdate(true);
        /* MOBI-375 add filter by $stockId */
        $stockId = $this->_manStock->getCurrentStockId();
        $select->where('stock_id=?', $stockId);
        /* select data */
        $result = $conn->fetchAll($select);
        return $result;
    }
}