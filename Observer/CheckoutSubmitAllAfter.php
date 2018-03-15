<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Observer;

/**
 * Split items qty by lots and register it (for credit cards payments events).
 */
class CheckoutSubmitAllAfter
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_ORDER = 'order';
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale */
    private $servRegSale;


    public function __construct(
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale $servRegSale
    ) {
        $this->hlpStock = $hlpStock;
        $this->servRegSale = $servRegSale;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData(self::DATA_ORDER);
        $this->splitQty($order);
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @throws \Exception
     */
    private function splitQty(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $storeId = $order->getStoreId();
        /* get stock ID for the store view */
        $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
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
        $this->servRegSale->exec($reqSale);
    }
}