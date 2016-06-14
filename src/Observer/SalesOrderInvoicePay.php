<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Register downline on new customer create event.
 */
class SalesOrderInvoicePay implements ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_INVOICE = 'invoice';

    /** @var \Praxigento\Warehouse\Service\IQtyDistributor */
    protected $_callQtyDistributor;
    /** @var  \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Warehouse\Service\IQtyDistributor $callQtyDistributor
    ) {
        $this->_manStock = $manStock;
        $this->_callQtyDistributor = $callQtyDistributor;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData(self::DATA_INVOICE);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $invoice->getOrder();
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
            /* qty of the product can be changed in invoice */
            $qtyInvoiced = $item->getQtyInvoiced();
            /* register sale item (fragment total qty by lots) */
            $itemData = new \Praxigento\Warehouse\Service\QtyDistributor\Data\Item;
            $itemData->setItemId($itemId);
            $itemData->setProductId($prodId);
            $itemData->setQuantity($qtyInvoiced);
            $itemData->setStockId($stockId);
            $itemsData[] = $itemData;
        }
        $reqSale = new \Praxigento\Warehouse\Service\QtyDistributor\Request\RegisterSale();
        $reqSale->setSaleItems($itemsData);
        $this->_callQtyDistributor->registerSale($reqSale);
        return;
    }
}