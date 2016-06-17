<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model;


class StockRegistryProvider
{
    /** @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory */
    protected $_factoryStockItem;
    /** @var \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory */
    protected $_factoryStockItemCrit;
    /** @var  \Magento\CatalogInventory\Api\StockItemRepositoryInterface */
    protected $_repoStockItem;
    /** @var  \Magento\CatalogInventory\Model\StockRegistryStorage */
    protected $_storageStockRegistry;
    /** @var  \Praxigento\Warehouse\Tool\IStockManager */
    protected $_toolStockManager;

    public function __construct(
        \Magento\CatalogInventory\Model\StockRegistryStorage $storageStockRegistry,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $factoryStockItem,
        \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory $factoryStockItemCrit,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $repoStockItem,
        \Praxigento\Warehouse\Tool\IStockManager $toolStockMan
    ) {
        $this->_storageStockRegistry = $storageStockRegistry;
        $this->_factoryStockItem = $factoryStockItem;
        $this->_factoryStockItemCrit = $factoryStockItemCrit;
        $this->_repoStockItem = $repoStockItem;
        $this->_toolStockManager = $toolStockMan;
    }

    /**
     * Detect current stock and get appropriate stock item.
     *
     * @param \Magento\CatalogInventory\Model\StockRegistryProvider $subject
     * @param \Closure $proceed
     * @param int $productId
     * @param int $scopeId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function aroundGetStockItem(
        \Magento\CatalogInventory\Model\StockRegistryProvider $subject,
        \Closure $proceed,
        $productId,
        $scopeId
    ) {
        $result = $this->_storageStockRegistry->getStockItem($productId, $scopeId);
        if (null === $result) {
            $criteria = $this->_factoryStockItemCrit->create();
            $criteria->setProductsFilter($productId);
            $stockId = $this->_toolStockManager->getCurrentStockId();
            $criteria->setStockFilter($stockId);
            $collection = $this->_repoStockItem->getList($criteria);
            $result = current($collection->getItems());
            if ($result && $result->getItemId()) {
                $this->_storageStockRegistry->setStockItem($productId, $scopeId, $result);
            } else {
                $result = $this->_factoryStockItem->create();
            }
        }
        return $result;
    }

}