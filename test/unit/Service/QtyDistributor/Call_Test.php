<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\QtyDistributor;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mSubRepo;
    /** @var  Call */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mSubRepo = $this->_mock(\Praxigento\Warehouse\Service\QtyDistributor\Sub\Repo::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mManTrans,
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

    public function test_registerSale()
    {
        /** === Test Data === */
        $ITEM_ID = 32;
        $PROD_ID = 16;
        $STOCK_ID = 2;
        $QTY = 4;
        $REQ = $this->_mock(Request\RegisterSale::class);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $reqItems = $req->getSaleItems();
        $mReqItem = $this->_mock(\Praxigento\Warehouse\Service\QtyDistributor\Data\Item::class);
        $REQ->shouldReceive('getSaleItems')->once()
            ->andReturn([$mReqItem]);
        // $itemId = $item->getItemId();
        $mReqItem->shouldReceive('getItemId')->once()->andReturn($ITEM_ID);
        // $prodId = $item->getProductId();
        $mReqItem->shouldReceive('getProductId')->once()->andReturn($PROD_ID);
        // $stockId = $item->getStockId();
        $mReqItem->shouldReceive('getStockId')->once()->andReturn($STOCK_ID);
        // $qty = $item->getQuantity();
        $mReqItem->shouldReceive('getQuantity')->once()->andReturn($QTY);
        // $lots = $this->_subRepo->getLotsByProductId($prodId, $stockId);
        $mLots = ['lots'];
        $this->mSubRepo
            ->shouldReceive('getLotsByProductId')->once()
            ->andReturn($mLots);
        // $this->_subRepo->registerSaleItemQty($itemId, $qty, $lots);
        $this->mSubRepo
            ->shouldReceive('registerSaleItemQty')->once()
            ->with($ITEM_ID, $QTY, $mLots);
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once()
            ->with($mDef);
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once()
            ->with($mDef);
        /** === Call and asserts  === */
        $res = $this->obj->registerSale($REQ);
        $this->assertTrue($res->isSucceed());
    }

}