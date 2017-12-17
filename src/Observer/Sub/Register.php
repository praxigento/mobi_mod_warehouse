<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Observer\Sub;


class Register
{
    /** @var \Praxigento\Warehouse\Service\IQtyDistributor */
    protected $_callQtyDistributor;
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    protected $_manStock;
    /** @var  \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Warehouse\Api\Helper\Stock $manStock,
        \Praxigento\Warehouse\Service\IQtyDistributor $callQtyDistributor
    ) {
        $this->_manObj = $manObj;
        $this->_manStock = $manStock;
        $this->_callQtyDistributor = $callQtyDistributor;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function splitQty(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $storeId = $order->getStoreId();
        /* get stock ID for the store view */
        $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        /** @var \Magento\Sales\Api\Data\OrderItemInterface[] $items */
        $items = $order->getItems();
        $itemsData = [];
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($items as $item) {
            $prodId = $item->getProductId();
            $itemId = $item->getItemId();
            /* qty of the product can be changed in invoice, but we use ordered only  */
            $qty = $item->getQtyOrdered();
            /* register sale item (fragment total qty by lots) */
            $itemData = new \Praxigento\Warehouse\Service\QtyDistributor\Data\Item();
            $itemData->setItemId($itemId);
            $itemData->setProductId($prodId);
            $itemData->setQuantity($qty);
            $itemData->setStockId($stockId);
            $itemsData[] = $itemData;
        }
        $reqSale = new \Praxigento\Warehouse\Service\QtyDistributor\Request\RegisterSale();
        $reqSale->setSaleItems($itemsData);
        $this->_callQtyDistributor->registerSale($reqSale);
    }
}