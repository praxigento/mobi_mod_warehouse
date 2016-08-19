<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model\Stock;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Item_UnitTest
    extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mSubject;
    /** @var  Item */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\CatalogInventory\Model\Stock\Item::class);
        /** create object to test */
        $this->obj = new Item();
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Item::class, $this->obj);
    }


    public function test_aroundGetStockId()
    {
        /** === Test Data === */
        $RESULT = 21;
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        // $result = $subject->getData(Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID);
        $this->mSubject
            ->shouldReceive('getData')->once()
            ->with(\Praxigento\Core\Config::E_CATINV_STOCK_ITEM_A_STOCK_ID)
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->aroundGetStockId(
            $this->mSubject,
            $mProceed
        );
        $this->assertEquals($RESULT, $res);
    }

}