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
class SaleItemQty implements ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_INVOICE = 'invoice';
    /** @var \Praxigento\Warehouse\Service\ICustomer */
    protected $_callCustomer;
    /** @var \Praxigento\Warehouse\Service\IQtyDistributor */
    protected $_callQtyDistributor;

    public function __construct(
        \Praxigento\Warehouse\Service\ICustomer $callCustomer,
        \Praxigento\Warehouse\Service\IQtyDistributor $callQtyDistributor
    ) {
        $this->_callCustomer = $callCustomer;
        $this->_callQtyDistributor = $callQtyDistributor;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData(self::DATA_INVOICE);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $invoice->getOrder();
        $custId = $order->getCustomerId();
        /* get stock ID for the customer */
        $stockId = $this->getStockIdByCustomer($custId);
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

    /**
     * Define current stock/warehouse for the customer.
     *
     * @param $custId
     * @return int
     */
    private function getStockIdByCustomer($custId)
    {
        /* TODO: move stock id to the service */
        /* get stock ID for the customer */
        $reqStock = new \Praxigento\Warehouse\Service\Customer\Request\GetCurrentStock();
        $reqStock->setCustomerId($custId);
        $respStock = $this->_callCustomer->getCurrentStock($reqStock);
        $result = $respStock->getStockId();
        return $result;
    }
}