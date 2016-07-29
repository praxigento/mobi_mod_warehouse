<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model;

/**
 * Update stock items on sales.
 */
class StockManagement
    extends \Magento\CatalogInventory\Model\StockManagement
{
    /** @var  \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;

    public function __construct(
        \Magento\CatalogInventory\Model\ResourceModel\Stock $stockResource,
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider,
        \Magento\CatalogInventory\Model\StockState $stockState,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\CatalogInventory\Model\ResourceModel\QtyCounterInterface $qtyCounter,
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Warehouse\Service\IQtyDistributor $callQtyDistributor
    ) {
        parent::__construct(
            $stockResource,
            $stockRegistryProvider,
            $stockState,
            $stockConfiguration,
            $productRepository,
            $qtyCounter
        );
        $this->_manStock = $manStock;
        $this->_callQtyDistributor = $callQtyDistributor;
    }

    /**
     * Update stock item on the stock and distribute qty by lots.
     *
     * @param \Magento\CatalogInventory\Model\StockManagement $subject
     * @param \Closure $proceed
     * @param array $items
     * @param int $websiteId is not used
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return null
     */
    public function aroundRegisterProductsSale(
        \Magento\CatalogInventory\Model\StockManagement $subject,
        \Closure $proceed,
        array $items,
        $websiteId
    ) {
        //if (!$websiteId) {
        /* replace websiteId by stockId */
        $stockId = $this->_manStock->getCurrentStockId();
        //}
        $this->getResource()->beginTransaction();
        $lockedItems = $this->getResource()->lockProductsStock(array_keys($items), $stockId);
        $fullSaveItems = $registeredItems = [];
        foreach ($lockedItems as $lockedItemRecord) {
            $productId = $lockedItemRecord['product_id'];
            $orderedQty = $items[$productId];
            /** @var \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem */
            $stockItem = $this->stockRegistryProvider->getStockItem($productId, $stockId);
            $canSubtractQty = $stockItem->getItemId() && $this->canSubtractQty($stockItem);
            if (!$canSubtractQty || !$this->stockConfiguration->isQty($lockedItemRecord['type_id'])) {
                continue;
            }
            if (!$stockItem->hasAdminArea()
                && !$this->stockState->checkQty($productId, $orderedQty, $stockItem->getWebsiteId())
            ) {
                $this->getResource()->commit();
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Not all of your products are available in the requested quantity.')
                );
            }
            if ($this->canSubtractQty($stockItem)) {
                $stockItem->setQty($stockItem->getQty() - $orderedQty);
            }
            $registeredItems[$productId] = $orderedQty;
            if (!$this->stockState->verifyStock($productId, $stockItem->getWebsiteId())
                || $this->stockState->verifyNotification(
                    $productId,
                    $stockItem->getWebsiteId()
                )
            ) {
                $fullSaveItems[] = $stockItem;
            }
        }
        $this->resource->correctItemsQty($registeredItems, $stockId, '-');
        $this->getResource()->commit();
        return $fullSaveItems;
    }
}