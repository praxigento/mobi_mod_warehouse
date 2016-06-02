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
        /** @var \Magento\Sales\Model\Order\Item $item */
        $item = $observer->getData('item');
        /* MOBI-299: register lots for newly saved order items */
        $isNew = $item->isObjectNew();
        if ($isNew) {
            $saleItemId = $item->getItemId();
            $prodId = $item->getProductId();
            $qtyOrdered = $item->getQtyOrdered();
            /* get stock ID for the customer */
            /** @var \Magento\Sales\Model\Order $order */
            $order = $item->getOrder();
            $custId = $order->getCustomerId();
            $reqStock = new \Praxigento\Warehouse\Service\Customer\Request\GetCurrentStock();
            $reqStock->setCustomerId($custId);
            $respStock = $this->_callCustomer->getCurrentStock($reqStock);
            $stockId = $respStock->getStockId();
            /* register sale item (fragment total qty by lots) */
            $reqSaleItem = new \Praxigento\Warehouse\Service\QtyDistributor\Request\RegisterForSaleItem();
            $reqSaleItem->setItemId($saleItemId);
            $reqSaleItem->setProductId($prodId);
            $reqSaleItem->setQuantity($qtyOrdered);
            $reqSaleItem->setStockId($stockId);
            $this->_callQtyDistributor->registerForSaleItem($reqSaleItem);
        }
        return;
    }
}