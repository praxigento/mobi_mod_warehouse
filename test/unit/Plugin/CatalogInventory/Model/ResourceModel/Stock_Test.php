<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model\ResourceModel;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Stock_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mSubject;
    /** @var  Stock */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\CatalogInventory\Model\ResourceModel\Stock::class);
        /** create object to test */
        $this->obj = new Stock();
    }

    public function test_aroundCorrectItemsQty_empty()
    {
        /** === Test Data === */
        $ITEMS = [];
        $STOCK_ID = 1;
        $OPERATOR = '+';
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        /** === Call and asserts  === */
        $res = $this->obj->aroundCorrectItemsQty(
            $this->mSubject,
            $mProceed,
            $ITEMS,
            $STOCK_ID,
            $OPERATOR
        );
        $this->assertEquals($this->mSubject, $res);
    }

    public function test_aroundCorrectItemsQty_process()
    {
        /** === Test Data === */
        $PROD_ID = 21;
        $QTY = 43;
        $ITEMS = [$PROD_ID => $QTY];
        $STOCK_ID = 1;
        $OPERATOR = '+';
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        // $conn = $subject->getConnection();
        $mConn = $this->_mockConn();
        $this->mSubject
            ->shouldReceive('getConnection')->once()
            ->andReturn($mConn);
        //
        // FIRST ITERATION
        //
        // $quotedId = $conn->quoteInto('?', $productId);
        $mConn->shouldReceive('quoteInto')->once()->andReturn("'$PROD_ID'");
        // $quotedQty = $conn->quoteInto("qty{$operator}?", $qty);
        $mConn->shouldReceive('quoteInto')->once()->andReturn("qty{'$QTY'}");
        //
        // $value = $conn->getCaseSql('product_id', $conditions, 'qty');
        $mValue = 'subSQL';
        $mConn->shouldReceive('getCaseSql')->once()->andReturn($mValue);
        // $conn->beginTransaction();
        $mConn->shouldReceive('beginTransaction')->once();
        // $conn->update($subject->getTable('cataloginventory_stock_item'), ['qty' => $value], $where);
        $this->mSubject->shouldReceive('getTable')->once();
        $mConn->shouldReceive('update')->once();
        // $conn->commit();
        $mConn->shouldReceive('commit')->once();
        /** === Call and asserts  === */
        $res = $this->obj->aroundCorrectItemsQty(
            $this->mSubject,
            $mProceed,
            $ITEMS,
            $STOCK_ID,
            $OPERATOR
        );
        $this->assertNull($res);
    }

    public function test_aroundLockProductsStock_empty()
    {
        /** === Test Data === */
        $PROD_IDS = [];
        $STOCK_ID = 1;
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        /** === Call and asserts  === */
        $res = $this->obj->aroundLockProductsStock(
            $this->mSubject,
            $mProceed,
            $PROD_IDS,
            $STOCK_ID
        );
        $this->assertTrue(empty($res));
    }

    public function test_aroundLockProductsStock_process()
    {
        /** === Test Data === */
        $PROD_IDS = [[]];
        $STOCK_ID = 1;
        $TAB_STOCK = 'table 01';
        $TAB_PROD = 'table 02';
        $RESULT = 'result';
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        // $conn = $subject->getConnection();
        $mConn = $this->_mockConn();
        $this->mSubject
            ->shouldReceive('getConnection')->once()
            ->andReturn($mConn);
        // $itemTable = $subject->getTable('cataloginventory_stock_item');
        $this->mSubject
            ->shouldReceive('getTable')->once()
            ->with('cataloginventory_stock_item')
            ->andReturn($TAB_STOCK);
        // $productTable = $subject->getTable('catalog_product_entity');
        $this->mSubject
            ->shouldReceive('getTable')->once()
            ->with('catalog_product_entity')
            ->andReturn($TAB_PROD);
        // $select = $conn->select();
        $mSelect = $this->_mockDbSelect(['from', 'join', 'where', 'forUpdate']);
        $mConn->shouldReceive('select')->once()
            ->andReturn($mSelect);
        // $result = $conn->fetchAll($select);
        $mConn->shouldReceive('fetchAll')->once()
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->aroundLockProductsStock(
            $this->mSubject,
            $mProceed,
            $PROD_IDS,
            $STOCK_ID
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Stock::class, $this->obj);
    }
}