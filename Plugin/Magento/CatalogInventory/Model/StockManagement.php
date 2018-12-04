<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\Magento\CatalogInventory\Model;

use Magento\Framework\Exception\LocalizedException as AMageExcept;

/**
 * Update stock items on sales.
 */
class StockManagement
{
    /** @var \Magento\CatalogInventory\Api\StockConfigurationInterface */
    private $configStock;
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    private $manStock;
    /** @var \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface */
    private $providerStockRegistry;
    /** @var \Magento\CatalogInventory\Model\ResourceModel\Stock */
    private $resourceStock;
    /** @var \Magento\CatalogInventory\Model\StockState */
    private $stockState;

    public function __construct(
        \Magento\CatalogInventory\Model\ResourceModel\Stock $resourceStock,
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $providerStockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $configStock,
        \Magento\CatalogInventory\Model\StockState $stockState,
        \Praxigento\Warehouse\Api\Helper\Stock $manStock
    ) {
        $this->resourceStock = $resourceStock;
        $this->providerStockRegistry = $providerStockRegistry;
        $this->configStock = $configStock;
        $this->stockState = $stockState;
        $this->manStock = $manStock;
    }

    /**
     * Check if is possible subtract value from item qty
     *
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return bool
     */
    protected function _canSubtractQty(
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
    ) {
        $result = $stockItem->getManageStock() && $this->configStock->canSubtractQty();
        return $result;
    }

    /**
     * Update stock item on the stock and distribute qty by lots.
     *
     * @param \Magento\CatalogInventory\Model\StockManagement $subject
     * @param \Closure $proceed
     * @param array $items
     * @param int $websiteId is not used
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface[]
     */
    public function aroundRegisterProductsSale(
        \Magento\CatalogInventory\Model\StockManagement $subject,
        \Closure $proceed,
        array $items,
        $websiteId
    ) {
        /* This code is moved from original 'registerProductsSale' method. */
        /* replace websiteId by stockId */
        $stockId = $this->manStock->getCurrentStockId();
        $lockedItems = $this->resourceStock->lockProductsStock(array_keys($items), $stockId);
        $fullSaveItems = $registeredItems = [];
        foreach ($lockedItems as $lockedItemRecord) {
            $productId = $lockedItemRecord['product_id'];
            $orderedQty = $items[$productId];
            /** @var \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem */
            $stockItem = $this->providerStockRegistry->getStockItem($productId, $stockId);
            $stockItemId = $stockItem->getItemId();
            $canSubtractQty = $stockItemId && $this->_canSubtractQty($stockItem);
            if (!$canSubtractQty || !$this->configStock->isQty($lockedItemRecord['type_id'])) {
                continue;
            }
            if (
                !$stockItem->hasAdminArea() &&
                !$this->stockState->checkQty($productId, $orderedQty)
            ) {
                $msg = 'Not all of your products are available in the requested quantity. ';
                $msg .= 'Product #%1, stock item #%2, stock #%3.';
                throw new AMageExcept(__($msg, $productId, $stockItemId, $stockId));
            }
            if ($this->_canSubtractQty($stockItem)) {
                $stockItem->setQty($stockItem->getQty() - $orderedQty);
            }
            $registeredItems[$productId] = $orderedQty;
            if (!$this->stockState->verifyStock($productId)
                || $this->stockState->verifyNotification($productId)
            ) {
                $fullSaveItems[] = $stockItem;
            }
        }
        $this->resourceStock->correctItemsQty($registeredItems, $stockId, '-');
        return $fullSaveItems;
    }
}