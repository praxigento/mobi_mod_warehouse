<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Observer;

/**
 * Don't save special price in DB. We use warehouse related special prices.
 */
class CatalogProductSaveBefore
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_OBJECT = 'data_object';
    const DATA_PRODUCT = 'product';

    /**
     * Don't save special price in DB. We use warehouse related special prices.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** define local working data */
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getData(self::DATA_PRODUCT);

        /** perform processing */
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $product->setSpecialPrice(null);
        }
    }

}