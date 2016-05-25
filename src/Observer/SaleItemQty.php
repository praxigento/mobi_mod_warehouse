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
    /** @var \Praxigento\Warehouse\Service\IQtyDistributor */
    protected $_callQtyDistributor;

    public function __construct(
        \Praxigento\Warehouse\Service\IQtyDistributor $callQtyDistributor
    ) {
        $this->_callQtyDistributor = $callQtyDistributor;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        $item = $observer->getData('item');
        $saleItemId = $item->getItemId();
        $prodId = $item->getProductId();
        $qtyOrdered = $item->getQtyOrdered();
        $req = new \Praxigento\Warehouse\Service\QtyDistributor\Request\RegisterForSaleItem();
        $req->setItemId($saleItemId);
        $req->setProductId($prodId);
        $req->setQuantity($qtyOrdered);
        $req->setStockId(1);
        $this->_callQtyDistributor->registerForSaleItem($req);
        return;
    }
}