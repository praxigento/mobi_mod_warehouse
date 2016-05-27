<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\QtyDistributor;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mSubRepo;
    /** @var  Call */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubRepo = $this->_mock(\Praxigento\Warehouse\Service\QtyDistributor\Sub\Repo::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mSubRepo
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(\Praxigento\Warehouse\Service\IQtyDistributor::class, $this->obj);
    }

    public function test_registerForSaleItem()
    {
        /** === Test Data === */
        $ITEM_ID = 21;
        $PRODUCT_ID = 432;
        $STOCK_ID = 1;
        $QTY = 4;
        /** === Setup Mocks === */
        // $lots = $this->_subRepo->getLotsByProductId($prodId, $stockId);
        $this->mSubRepo
            ->shouldReceive('getLotsByProductId')->once()
            ->andReturn('lots');
        // $this->_subRepo->registerSaleItemQty($itemId, $qty, $lots);
        $this->mSubRepo
            ->shouldReceive('registerSaleItemQty')->once();
        /** === Call and asserts  === */
        $req = new Request\RegisterForSaleItem();
        $req->setItemId($ITEM_ID);
        $req->setProductId($PRODUCT_ID);
        $req->setStockId($STOCK_ID);
        $req->setQuantity($QTY);
        $res = $this->obj->registerForSaleItem($req);
        $this->assertTrue($res->isSucceed());
    }
}