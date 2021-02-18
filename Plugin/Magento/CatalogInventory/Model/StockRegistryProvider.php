<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Magento\CatalogInventory\Model;


class StockRegistryProvider {
    /**
     * @var \Magento\CatalogInventory\Api\StockRepositoryInterface
     */
    private $daoStock;
    /**
     * @var  \Magento\CatalogInventory\Api\StockItemRepositoryInterface
     */
    private $daoStockItem;
    /**
     * @var \Magento\CatalogInventory\Api\StockStatusRepositoryInterface
     */
    private $daoStockStatus;
    /**
     * @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory
     */
    private $factStockItem;
    /**
     * @var \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory
     */
    private $factStockItemCrit;
    /**
     * @var  \Praxigento\Warehouse\Api\Helper\Stock
     */
    private $manStock;
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $modQuoteSession;
    /**
     * @var  \Magento\CatalogInventory\Model\StockRegistryStorage
     */
    private $storeStockRegistry;

    public function __construct(
        \Magento\CatalogInventory\Model\StockRegistryStorage $storeStockRegistry,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $factStockItem,
        \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory $factStockItemCrit,
        \Magento\CatalogInventory\Api\StockRepositoryInterface $daoStock,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $daoStockItem,
        \Magento\CatalogInventory\Api\StockStatusRepositoryInterface $daoStockStatus,
        \Magento\Backend\Model\Session\Quote $modQuoteSession,
        \Praxigento\Warehouse\Api\Helper\Stock $manStock
    ) {
        $this->storeStockRegistry = $storeStockRegistry;
        $this->factStockItem = $factStockItem;
        $this->factStockItemCrit = $factStockItemCrit;
        $this->daoStock = $daoStock;
        $this->daoStockItem = $daoStockItem;
        $this->daoStockStatus = $daoStockStatus;
        $this->modQuoteSession = $modQuoteSession;
        $this->manStock = $manStock;
    }

    /**
     * MOBI-799: load current stock by default.
     *
     * @param \Magento\CatalogInventory\Model\StockRegistryProvider $subject
     * @param \Closure $proceed
     * @param int $scopeId it's not $websiteId (as Magento thought), it's warehouse ID.
     * @return \Magento\CatalogInventory\Api\Data\StockInterface
     */
    public function aroundGetStock(
        \Magento\CatalogInventory\Model\StockRegistryProvider $subject,
        \Closure $proceed,
        $scopeId
    ) {
        // $result = $proceed($scopeId); - don't use dumb code.
        /* get stock by scopeId (websiteId) from app cache if was cached before */
        $stock = $this->storeStockRegistry->getStock($scopeId);
        if (null === $stock) {
            /* ... or load current stock and save to app cache */
            $stockId = $this->manStock->getCurrentStockId();
            $stock = $this->daoStock->get($stockId);
            $this->storeStockRegistry->setStock($scopeId, $stock);
        }
        return $stock;
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
        // we have no '0' stock in DB
        if ($scopeId === 0) {
            $scopeId = $this->manStock->getCurrentStockId();
        }
        $result = $this->storeStockRegistry->getStockItem($productId, $scopeId);
        if (null === $result) {
            $criteria = $this->factStockItemCrit->create();
            $criteria->setProductsFilter($productId);
            $stockId = $this->manStock->getCurrentStockId();
            $criteria->setStockFilter($stockId);
            $collection = $this->daoStockItem->getList($criteria);
            $result = current($collection->getItems());
            if ($result && $result->getItemId()) {
                $this->storeStockRegistry->setStockItem($productId, $scopeId, $result);
            } else {
                $result = $this->factStockItem->create();
            }
        }
        return $result;
    }

    /**
     * @param \Magento\CatalogInventory\Model\StockRegistryProvider $subject
     * @param \Closure $proceed
     * @param int $productId
     * @param int $scopeId
     * @return \Magento\CatalogInventory\Api\Data\StockStatusInterface
     */
    public function aroundGetStockStatus(
        \Magento\CatalogInventory\Model\StockRegistryProvider $subject,
        \Closure $proceed,
        $productId,
        $scopeId
    ) {
        // we have no '0' stock in DB
        if ($scopeId === 0) {
            $scopeId = $this->manStock->getCurrentStockId();
        }
        $stockStatus = $this->storeStockRegistry->getStockStatus($productId, $scopeId);
        if (null === $stockStatus) {
            /* ... or load current stock status and save to app cache */
            $storeId = $this->modQuoteSession->getStoreId();
            if ($storeId) {
                /* backend mode */
                $stockId = $this->manStock->getStockIdByStoreId($storeId);
            } else {
                /* frontend mode */
                $stockId = $this->manStock->getCurrentStockId();
            }
            $crit = new \Magento\CatalogInventory\Model\ResourceModel\Stock\Status\StockStatusCriteria();
            $crit->setProductsFilter($productId);
            $crit->addFilter(
                null,
                \Magento\CatalogInventory\Api\Data\StockStatusInterface::STOCK_ID,
                $stockId
            );
            $collection = $this->daoStockStatus->getList($crit);
            $rows = $collection->getItems();
            $stockStatus = reset($rows);
            if ($stockStatus instanceof \Magento\CatalogInventory\Api\Data\StockStatusInterface) {
                $this->storeStockRegistry->setStockStatus($productId, $scopeId, $stockStatus);
            } else {
                $obm = \Magento\Framework\App\ObjectManager::getInstance();
                /** @var \Magento\CatalogInventory\Model\Stock\Status $stockStatus */
                $stockStatus = $obm->create(\Magento\CatalogInventory\Model\Stock\Status::class);
                $stockStatus->setProductId($productId);
                $stockStatus->setStockStatus(0);
                $stockStatus->setQty(0);
            }
        }
        return $stockStatus;
    }
}
