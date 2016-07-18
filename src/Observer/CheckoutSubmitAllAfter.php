<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Observer;

/**
 * Split items qty by lots and register it (for credit cards payments).
 */
class CheckoutSubmitAllAfter
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_ORDER = 'order';
    /** @var \Praxigento\Warehouse\Observer\Sub\Register */
    protected $_subRegister;

    public function __construct(
        \Praxigento\Warehouse\Observer\Sub\Register $subRegister
    ) {
        $this->_subRegister = $subRegister;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData(self::DATA_ORDER);
        $state = $order->getState();
        if ($state == \Magento\Sales\Model\Order::STATE_PROCESSING) {
            $this->_subRegister->splitQty($order);
        }
        return;
    }
}