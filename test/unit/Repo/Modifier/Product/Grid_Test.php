<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Modifier\Product;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Grid_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  Grid */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mConn = $this->_mockConn([]);
        $this->mResource = $this->_mockResourceConnection($this->mConn);
        /** create object to test */
        $this->obj = new Grid(
            $this->mResource
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Grid::class, $this->obj);
    }

    public function test_modifySelect()
    {
        /** === Test Data === */
        $TBL_STOCK_ITEM = 'stock item';
        $TBL_WRHS_QTY = 'qty';
        $SELECT = $this->_mock(\Magento\Framework\DB\Select::class);
        /** === Setup Mocks === */
        // $tblStockItem = $this->_resource->getTableName(self::TBL_STOCK_ITEM);
        $this->mResource
            ->shouldReceive('getTableName')->once()
            ->with(Grid::TBL_STOCK_ITEM)
            ->andReturn($TBL_STOCK_ITEM);
        // $tblWrhsQty = $this->_resource->getTableName(self::TBL_WRHS_QTY);
        $this->mResource
            ->shouldReceive('getTableName')->once()
            ->with(Grid::TBL_WRHS_QTY)
            ->andReturn($TBL_WRHS_QTY);
        // $fields = [$fldQty => $this->getEquationQty()];
        // $tbl = $this->_resource->getTableName(self::TBL_WRHS_QTY);
        $this->mResource
            ->shouldReceive('getTableName')->once()
            ->with(Grid::TBL_WRHS_QTY)
            ->andReturn($TBL_WRHS_QTY);
        //
        // $select->joinLeft($tblStockItem, $on, $fields);
        $SELECT->shouldReceive('joinLeft')->twice();
        // $select->group("$tblEntity.$fldEntityId");
        $SELECT->shouldReceive('group')->once();
        /** === Call and asserts  === */
        $res = $this->obj->modifySelect($SELECT);
        $this->assertEquals($SELECT, $res);
    }
}