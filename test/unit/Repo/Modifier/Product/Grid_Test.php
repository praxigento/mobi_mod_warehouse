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
        $SELECT = $this->_mock(\Magento\Framework\DB\Select::class);
        /** === Setup Mocks === */
        // $select->joinLeft($tblStockItem, $on, $fields);
        $SELECT->shouldReceive('joinLeft')->once();
        // $select->group("$tblEntity.$fldEntityId");
        $SELECT->shouldReceive('group')->once();
        /** === Call and asserts  === */
        $res = $this->obj->modifySelect($SELECT);
    }
}