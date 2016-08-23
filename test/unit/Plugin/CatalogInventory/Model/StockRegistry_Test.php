<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class StockRegistry_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{

    /** @var  \Mockery\MockInterface */
    private $mSubject;
    /** @var  StockRegistry */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\CatalogInventory\Model\StockRegistry::class);
        /** create object to test */
        $this->obj = new StockRegistry();
    }

    public function test_aroundUpdateStockItemBySku()
    {
        /** === Test Data === */
        $SKU = 'product SKU';
        $STOCK_ITEM = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $STOCK_ID = 2;
        $RESULT = 'processing result';
        /** === Setup Mocks === */
        $mProceed = function ($skuIn, $stockItemIn) use ($SKU, $STOCK_ITEM, $RESULT) {
            $this->assertEquals($SKU, $skuIn);
            $this->assertEquals($STOCK_ITEM, $stockItemIn);
            return $RESULT;
        };
        // $stockId = $stockItem->getStockId();
        $STOCK_ITEM->shouldReceive('getStockId')->once()
            ->andReturn($STOCK_ID);
        /** === Call and asserts  === */
        $res = $this->obj->aroundUpdateStockItemBySku(
            $this->mSubject,
            $mProceed,
            $SKU,
            $STOCK_ITEM
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(StockRegistry::class, $this->obj);
    }
}