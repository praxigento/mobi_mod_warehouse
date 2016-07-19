<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\QtyDistributor\Sub;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Repo_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  \Mockery\MockInterface */
    private $mRepoQty;
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  \Mockery\MockInterface */
    private $mRepoQtySale;
    /** @var  Repo */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mResource = $this->_mockResourceConnection();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        $this->mRepoQty = $this->_mock(\Praxigento\Warehouse\Repo\Entity\IQuantity::class);
        $this->mRepoQtySale = $this->_mock(\Praxigento\Warehouse\Repo\Entity\Quantity\ISale::class);
        /** create object to test */
        $this->obj = new Repo(
            $this->mResource,
            $this->mManTrans,
            $this->mRepoGeneric,
            $this->mRepoQty,
            $this->mRepoQtySale
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Repo::class, $this->obj);
    }

    public function test_getLotsByProductId()
    {
        /** === Test Data === */
        $PROD_ID = 32;
        $STOCK_ID = 54;
        /** === Setup Mocks === */
        // $conn = $this->_repoGeneric->getConnection();
        $mConn = $this->_mockConn(['getTableName', 'fetchAll']);
        $this->mRepoGeneric
            ->shouldReceive('getConnection')->once()
            ->andReturn($mConn);
        // $query = $conn->select();
        $mQuery = $this->_mockDbSelect(['from', 'joinLeft', 'where', 'order']);
        $mConn->shouldReceive('select')->once()
            ->andReturn($mQuery);
        /** === Call and asserts  === */
        $this->obj->getLotsByProductId($PROD_ID, $STOCK_ID);
    }

    public function test_registerSaleItemQty()
    {
        /** === Test Data === */
        $SALE_ITEM_ID = 21;
        $TOTAL = 23;
        $LOTS_DATA = [
            [
                Alias::AS_STOCK_ITEM_ID => 1,
                Alias::AS_LOT_ID => 2,
                Alias::AS_QTY => 3
            ],
            [
                Alias::AS_STOCK_ITEM_ID => 1,
                Alias::AS_LOT_ID => 4,
                Alias::AS_QTY => 300
            ]
        ];
        /** === Setup Mocks === */
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        /**
         * First loop
         */
        // $this->_repoQtySale->create($qtySaleData);
        $this->mRepoQtySale
            ->shouldReceive('create')->once();
        // $this->_repoQty->deleteById($qtyPk);
        $this->mRepoQty
            ->shouldReceive('deleteById')->once();
        /**
         * Second loop
         */
        // $this->_repoQtySale->create($qtySaleData);
        $this->mRepoQtySale
            ->shouldReceive('create')->once();
        // $this->_repoQty->updateById($qtyPk, $qtyUpdateData);
        $this->mRepoQty
            ->shouldReceive('updateById')->once();
        /**
         * Close transaction.
         */
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $this->obj->registerSaleItemQty($SALE_ITEM_ID, $TOTAL, $LOTS_DATA);
    }
}