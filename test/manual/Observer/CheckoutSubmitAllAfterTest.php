<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Warehouse\Service\QtyDistributor\Register;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Delete
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_execute()
    {
        $this->setAreaCode();
        /** @var \Praxigento\Warehouse\Observer\CheckoutSubmitAllAfter $obj */
        $obj = $this->manObj->get(\Praxigento\Warehouse\Observer\CheckoutSubmitAllAfter::class);
        $def = $this->manTrans->begin();
        /** @var \Magento\Sales\Api\OrderRepositoryInterface $repoSales */
        $repoSales = $this->manObj->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $sale = $repoSales->get(224);
        $quoteId = $sale->getQuoteId();
        /** @var \Magento\Quote\Api\CartRepositoryInterface $repoQuotes */
        $repoQuotes = $this->manObj->get(\Magento\Quote\Api\CartRepositoryInterface::class);
        $quote = $repoQuotes->get($quoteId);
        /** @var \Magento\Framework\Event\Observer $observer */
        $observer = $this->manObj->get(\Magento\Framework\Event\Observer::class);
        $observer->setData('order', $sale);
        $observer->setData('quote', $quote);
        $obj->execute($observer);
        $this->manTrans->rollback($def);
        $this->assertTrue(true);
    }
}