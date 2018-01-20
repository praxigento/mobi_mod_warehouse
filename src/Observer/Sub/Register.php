<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Observer\Sub;


class Register
{
    /** @var  \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    protected $_manStock;
    /** @var \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale */
    protected $_sale;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Warehouse\Api\Helper\Stock $manStock,
        \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale $sale
    ) {
        $this->_manObj = $manObj;
        $this->_manStock = $manStock;
        $this->_sale = $sale;
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
            /* qty of the product can be changed in invoice, but we use ordered qtys only  */
            $qty = $item->getQtyOrdered();
            /* register sale item (fragment total qty by lots) */
            $itemData = new \Praxigento\Warehouse\Service\QtyDistributor\Data\Item();
            $itemData->setItemId($itemId);
            $itemData->setProductId($prodId);
            $itemData->setQuantity($qty);
            $itemData->setStockId($stockId);
            $itemsData[] = $itemData;
        }
        $reqSale = new \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale\Request();
        $reqSale->setSaleItems($itemsData);
        $this->_sale->exec($reqSale);
    }
}