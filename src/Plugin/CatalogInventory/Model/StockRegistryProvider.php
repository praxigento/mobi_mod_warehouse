<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model;


class StockRegistryProvider
{
    /**
     * @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory
     */
    protected $factStockItem;
    /**
     * @var \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory
     */
    protected $factStockItemCrit;
    /**
     * @var \Magento\CatalogInventory\Api\StockRepositoryInterface
     */
    protected $repoStock;
    /**
     * @var  \Magento\CatalogInventory\Api\StockItemRepositoryInterface
     */
    protected $repoStockItem;
    /**
     * @var  \Magento\CatalogInventory\Model\StockRegistryStorage
     */
    protected $storeStockRegistry;
    /**
     * @var  \Praxigento\Warehouse\Tool\IStockManager
     */
    protected $toolStockManager;

    public function __construct(
        \Magento\CatalogInventory\Model\StockRegistryStorage $storeStockRegistry,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $factStockItem,
        \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory $factStockItemCrit,
        \Magento\CatalogInventory\Api\StockRepositoryInterface $repoStock,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $repoStockItem,
        \Praxigento\Warehouse\Tool\IStockManager $toolStockMan
    ) {
        $this->storeStockRegistry = $storeStockRegistry;
        $this->factStockItem = $factStockItem;
        $this->factStockItemCrit = $factStockItemCrit;
        $this->repoStock = $repoStock;
        $this->repoStockItem = $repoStockItem;
        $this->toolStockManager = $toolStockMan;
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
    )
    {
        // $result = $proceed($scopeId); - don't use dumb code.
        /* get stock by scopeId (websiteId) from app cache if was cached before */
        $stock = $this->storeStockRegistry->getStock($scopeId);
        if (null === $stock) {
            /* ... or load current stock and save to app cache */
            $stockId = $this->toolStockManager->getCurrentStockId();
            $stock = $this->repoStock->get($stockId);
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
        // $result = $proceed($productId, $scopeId); // original method will create empty item for stock registry
        $result = $this->storeStockRegistry->getStockItem($productId, $scopeId);
        if (null === $result) {
            $criteria = $this->factStockItemCrit->create();
            $criteria->setProductsFilter($productId);
            $stockId = $this->toolStockManager->getCurrentStockId();
            $criteria->setStockFilter($stockId);
            $collection = $this->repoStockItem->getList($criteria);
            $result = current($collection->getItems());
            if ($result && $result->getItemId()) {
                $this->storeStockRegistry->setStockItem($productId, $scopeId, $result);
            } else {
                $result = $this->factStockItem->create();
            }
        }
        return $result;
    }
}