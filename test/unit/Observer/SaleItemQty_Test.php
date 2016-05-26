<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Observer;

use Praxigento\Warehouse\Data\Entity\Quantity;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class SaleItemQty_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{

    /** @var  \Mockery\MockInterface */
    private $mCallCustolmer;
    /** @var  \Mockery\MockInterface */
    private $mCallQtyDistributor;
    /** @var  \Mockery\MockInterface */
    private $mObserver;
    /** @var  SaleItemQty */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mCallCustolmer = $this->_mock(\Praxigento\Warehouse\Service\ICustomer::class);
        $this->mCallQtyDistributor = $this->_mock(\Praxigento\Warehouse\Service\IQtyDistributor::class);
        $this->mObserver = $this->_mock(\Magento\Framework\Event\Observer::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new SaleItemQty(
            $this->mCallCustolmer,
            $this->mCallQtyDistributor
        );
    }

    public function test_execute()
    {
        /** === Test Data === */
        $SALE_ITEM_ID = 'sale item id';
        $PROD_ID = 'prod id';
        $QTY = 'quantity';
        $STOCK_ID = 'stock id';
        /** === Setup Mocks === */
        // $item = $observer->getData('item');
        $mItem = $this->_mock(\Magento\Sales\Api\Data\OrderItemInterface::class);
        $this->mObserver
            ->shouldReceive('getData')->once()
            ->with('item')
            ->andReturn($mItem);
        // $saleItemId = $item->getItemId();
        $mItem->shouldReceive('getItemId')->once()
            ->andReturn($SALE_ITEM_ID);
        // $prodId = $item->getProductId();
        $mItem->shouldReceive('getProductId')->once()
            ->andReturn($PROD_ID);
        // $qtyOrdered = $item->getQtyOrdered();
        $mItem->shouldReceive('getQtyOrdered')->once()
            ->andReturn($QTY);
        // $respStock = $this->_callCustomer->getCurrentStock($reqStock);
        $mRespStock = $this->_mock(\Praxigento\Warehouse\Service\Customer\Response\GetCurrentStock::class);
        $this->mCallCustolmer
            ->shouldReceive('getCurrentStock')->once()
            ->andReturn($mRespStock);
        // $stockId = $respStock->getStockId();
        $mRespStock->shouldReceive('getStockId')->once()
            ->andReturn($STOCK_ID);
        // $this->_callQtyDistributor->registerForSaleItem($reqSaleItem);
        $this->mCallQtyDistributor
            ->shouldReceive('registerForSaleItem')->once();
        /** === Call and asserts  === */
        $this->obj->execute($this->mObserver);
    }
}