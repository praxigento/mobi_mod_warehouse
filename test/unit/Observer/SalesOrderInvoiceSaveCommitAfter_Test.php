<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Observer;

use Praxigento\Warehouse\Data\Entity\Quantity;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class SalesOrderInvoiceSaveCommitAfter_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{

    /** @var  \Mockery\MockInterface */
    private $mObserver;
    /** @var  \Mockery\MockInterface */
    private $mSubRegister;
    /** @var  SalesOrderInvoiceSaveCommitAfter */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubRegister = $this->_mock(Sub\Register::class);
        $this->mObserver = $this->_mock(\Magento\Framework\Event\Observer::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new SalesOrderInvoiceSaveCommitAfter(
            $this->mSubRegister
        );
    }

    public function test_execute()
    {
        /** === Setup Mocks === */
        // $invoice = $observer->getData(self::DATA_INVOICE);
        $mInvoice = $this->_mock(\Magento\Sales\Model\Order\Invoice::class);
        $this->mObserver
            ->shouldReceive('getData')->once()
            ->with('invoice')
            ->andReturn($mInvoice);
        // $state = $invoice->getState();
        $mInvoice->shouldReceive('getState')->once()
            ->andReturn(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
        // $order = $invoice->getOrder();
        $mOrder = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $mInvoice->shouldReceive('getOrder')->once()
            ->andReturn($mOrder);
        // $this->_subRegister->splitQty($order);
        $this->mSubRegister
            ->shouldReceive('splitQty')->once();
        /** === Call and asserts  === */
        $this->obj->execute($this->mObserver);
    }
}