<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\Magento\CatalogInventory\Model;

/**
 * Update stock items on sales.
 */
class StockManagement
{
    /** @var \Magento\CatalogInventory\Api\StockConfigurationInterface */
    protected $_configStock;
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    protected $_manStock;
    /** @var  \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    protected $_manTrans;
    /** @var \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface */
    protected $_providerStockRegistry;
    /** @var \Magento\CatalogInventory\Model\ResourceModel\Stock */
    protected $_resourceStock;
    /** @var \Magento\CatalogInventory\Model\StockState */
    protected $_stockState;

    public function __construct(
        \Magento\CatalogInventory\Model\ResourceModel\Stock $resourceStock,
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $providerStockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $configStock,
        \Magento\CatalogInventory\Model\StockState $stockState,
        \Praxigento\Warehouse\Api\Helper\Stock $manStock,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans
    ) {
        $this->_resourceStock = $resourceStock;
        $this->_providerStockRegistry = $providerStockRegistry;
        $this->_configStock = $configStock;
        $this->_stockState = $stockState;
        $this->_manStock = $manStock;
        $this->_manTrans = $manTrans;
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
        $result = $stockItem->getManageStock() && $this->_configStock->canSubtractQty();
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
        $stockId = $this->_manStock->getCurrentStockId();
        $def = $this->_manTrans->begin();
        $lockedItems = $this->_resourceStock->lockProductsStock(array_keys($items), $stockId);
        $fullSaveItems = $registeredItems = [];
        foreach ($lockedItems as $lockedItemRecord) {
            $productId = $lockedItemRecord['product_id'];
            $orderedQty = $items[$productId];
            /** @var \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem */
            $stockItem = $this->_providerStockRegistry->getStockItem($productId, $stockId);
            $stockItemId = $stockItem->getItemId();
            $canSubtractQty = $stockItemId && $this->_canSubtractQty($stockItem);
            if (!$canSubtractQty || !$this->_configStock->isQty($lockedItemRecord['type_id'])) {
                continue;
            }
            if (
                !$stockItem->hasAdminArea() &&
                !$this->_stockState->checkQty($productId, $orderedQty)
            ) {
                $this->_manTrans->rollback($def);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Not all of your products are available in the requested quantity.')
                );
            }
            if ($this->_canSubtractQty($stockItem)) {
                $stockItem->setQty($stockItem->getQty() - $orderedQty);
            }
            $registeredItems[$productId] = $orderedQty;
            if (!$this->_stockState->verifyStock($productId)
                || $this->_stockState->verifyNotification($productId)
            ) {
                $fullSaveItems[] = $stockItem;
            }
        }
        $this->_resourceStock->correctItemsQty($registeredItems, $stockId, '-');
        $this->_manTrans->commit($def);
        return $fullSaveItems;
    }
}