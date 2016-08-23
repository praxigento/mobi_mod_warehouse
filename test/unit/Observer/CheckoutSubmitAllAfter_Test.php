<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Observer;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class CheckoutSubmitAllAfter_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{

    /** @var  \Mockery\MockInterface */
    private $mObserver;
    /** @var  \Mockery\MockInterface */
    private $mSubRegister;
    /** @var  CheckoutSubmitAllAfter */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubRegister = $this->_mock(Sub\Register::class);
        $this->mObserver = $this->_mock(\Magento\Framework\Event\Observer::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new CheckoutSubmitAllAfter(
            $this->mSubRegister
        );
    }

    public function test_execute()
    {
        /** === Setup Mocks === */
        // $order = $observer->getData(self::DATA_ORDER);
        $mOrder = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $this->mObserver
            ->shouldReceive('getData')->once()
            ->with('order')
            ->andReturn($mOrder);
        // $this->_subRegister->splitQty($order);
        $this->mSubRegister
            ->shouldReceive('splitQty')->once();
        /** === Call and asserts  === */
        $this->obj->execute($this->mObserver);
    }
}