<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\Catalog\Model\ResourceModel\Product;


class Collection_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var \Mockery\MockInterface */
    private $mQueryModGrid;
    /** @var \Mockery\MockInterface */
    private $mSubject;
    /** @var Collection */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        $this->mQueryModGrid = $this->_mock(\Praxigento\Warehouse\Repo\Modifier\Product\Grid::class);
        /** create object to test */
        $this->obj = new Collection(
            $this->mQueryModGrid
        );
    }

    public function test_aroundAddFieldToFilter_skip()
    {
        /** === Test Data === */
        $ATTR = 'attribute';
        $COND = 'condition';
        $RESULT = 'processing result';
        /** === Setup Mocks === */
        $mProceed = function ($attrIn, $condIn) use ($ATTR, $COND, $RESULT) {
            $this->assertEquals($ATTR, $attrIn);
            $this->assertEquals($COND, $condIn);
            return $RESULT;
        };
        /** === Call and asserts  === */
        $res = $this->obj->aroundAddFieldToFilter(
            $this->mSubject,
            $mProceed,
            $ATTR,
            $COND
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_aroundAddFieldToFilter_process()
    {
        /** === Test Data === */
        $ATTR = \Praxigento\Warehouse\Repo\Modifier\Product\Grid::FLD_QTY;
        $COND = 'condition';
        /** === Setup Mocks === */
        $mProceed = function () {

        };
        // $result = $subject;
        $mResult = $this->mSubject;
        // $conn = $result->getConnection();
        $mConn = $this->_mockConn();
        $mResult->shouldReceive('getConnection')->once()
            ->andReturn($mConn);
        // $query = $conn->prepareSqlCondition($alias, $condition);
        $mQuery = 'some sql query';
        $mConn->shouldReceive('prepareSqlCondition')->once()
            ->andReturn($mQuery);
        // $select = $result->getSelect();
        $mSelect = $this->_mockDbSelect();
        $mResult
            ->shouldReceive('getSelect')->once()
            ->andReturn($mSelect);
        // $select->where($query);
        $mSelect->shouldReceive('where')->once();
        /** === Call and asserts  === */
        $res = $this->obj->aroundAddFieldToFilter(
            $this->mSubject,
            $mProceed,
            $ATTR,
            $COND
        );
        $this->assertEquals($mResult, $res);
    }

    public function test_aroundAddOrder_skip()
    {
        /** === Test Data === */
        $FIELD = 'field';
        $DIR = 'direction';
        $RESULT = 'processing result';
        /** === Setup Mocks === */
        $mProceed = function ($attrIn, $condIn) use ($FIELD, $DIR, $RESULT) {
            $this->assertEquals($FIELD, $attrIn);
            $this->assertEquals($DIR, $condIn);
            return $RESULT;
        };
        /** === Call and asserts  === */
        $res = $this->obj->aroundAddOrder(
            $this->mSubject,
            $mProceed,
            $FIELD,
            $DIR
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_aroundAddOrder_process()
    {
        /** === Test Data === */
        $FIELD = \Praxigento\Warehouse\Repo\Modifier\Product\Grid::FLD_QTY;
        $DIR = 'direction';
        /** === Setup Mocks === */
        $mProceed = function () {

        };
        // $result = $subject;
        $mResult = $this->mSubject;
        // $select = $result->getSelect();
        $mSelect = $this->_mockDbSelect();
        $mResult->shouldReceive('getSelect')->once()
            ->andReturn($mSelect);
        // $select->order($order);
        $mSelect->shouldReceive('order')->once();
        /** === Call and asserts  === */
        $res = $this->obj->aroundAddOrder(
            $this->mSubject,
            $mProceed,
            $FIELD,
            $DIR
        );
        $this->assertEquals($mResult, $res);
    }


    public function test_aroundGetSelectCountSql()
    {
        /** === Test Data === */
        $RESULT = $this->_mock(\Magento\Framework\DB\Select::class);
        /** === Setup Mocks === */
        $mProceed = function () use ($RESULT) {
            return $RESULT;
        };
        // $this->_queryModGrid->modifySelect($result);
        $this->mQueryModGrid
            ->shouldReceive('modifySelect')->once($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->aroundGetSelectCountSql(
            $this->mSubject,
            $mProceed
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_constructor()
    {
        $this->assertInstanceOf(Collection::class, $this->obj);
    }
}