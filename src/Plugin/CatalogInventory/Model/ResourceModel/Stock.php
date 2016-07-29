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
//    /** @var  \Praxigento\Warehouse\Tool\IStockManager */
//    protected $_manStock;
//
//    public function __construct(
//        \Praxigento\Warehouse\Tool\IStockManager $manStock
//    ) {
//        $this->_manStock = $manStock;
//    }

    /**
     * Filter locked items by stock ID.
     *
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock $subject
     * @param \Closure $proceed
     * @param int[] $productIds
     * @param int $stockId defined in \Praxigento\Warehouse\Plugin\CatalogInventory\Model\StockManagement::aroundRegisterProductsSale
     * @return array found stock items data as an associative array
     */
    public function aroundLockProductsStock(
        \Magento\CatalogInventory\Model\ResourceModel\Stock $subject,
        \Closure $proceed,
        $productIds,
        $stockId
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
        $select->where('product_id IN(?)', $productIds);
        $select->forUpdate(true);
        /* MOBI-375 add filter by $stockId */
        $select->where('stock_id=?', $stockId);
        /* select data */
        $result = $conn->fetchAll($select);
        return $result;
    }

    /**
     * Update stock item in the stock.
     *
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock $subject
     * @param \Closure $proceed
     * @param array $items
     * @param int $stockId defined in \Praxigento\Warehouse\Plugin\CatalogInventory\Model\StockManagement::aroundRegisterProductsSale
     * @param string $operator
     * @return null
     */
    public function aroundCorrectItemsQty(
        \Magento\CatalogInventory\Model\ResourceModel\Stock $subject,
        \Closure $proceed,
        array $items,
        $stockId,
        $operator
    ) {
        if (empty($items)) {
            return $this;
        }

        $conn = $subject->getConnection();
        $conditions = [];
        foreach ($items as $productId => $qty) {
            $case = $conn->quoteInto('?', $productId);
            $result = $conn->quoteInto("qty{$operator}?", $qty);
            $conditions[$case] = $result;
        }

        $value = $conn->getCaseSql('product_id', $conditions, 'qty');
        $where = ['product_id IN (?)' => array_keys($items), 'stock_id = ?' => $stockId];

        $conn->beginTransaction();
        $conn->update($subject->getTable('cataloginventory_stock_item'), ['qty' => $value], $where);
        $conn->commit();
    }
}