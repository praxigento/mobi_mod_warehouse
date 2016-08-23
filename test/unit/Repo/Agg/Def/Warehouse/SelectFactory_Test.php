<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Agg\Def\Warehouse;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class SelectFactory_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var SelectFactory */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mConn = $this->_mockConn();
        $this->mResource = $this->_mockResourceConnection($this->mConn);
        /** setup mocks for constructor */
        $this->mResource
            ->shouldReceive('getConnection')->once()
            ->andReturn($this->mConn);
        /** create object to test */
        $this->obj = new SelectFactory (
            $this->mResource
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(SelectFactory::class, $this->obj);
    }

    public function test_getSelectCountQuery()
    {
        /** === Setup Mocks === */
        // $result = $this->_conn->select();
        $mResult = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')->once()
            ->andReturn($mResult);
        // $tblStock = [$asStock => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK)];
        // $tblWrhs = [$asWrhs => $this->_resource->getTableName(EntityWarehouse::ENTITY_NAME)];
        $this->mResource
            ->shouldReceive('getTableName')->twice();
        // $result->from($tblStock, $cols);
        $mResult->shouldReceive('from', 'joinLeft');
        /** === Call and asserts  === */
        $this->obj->getQueryToSelectCount();
    }

    public function test_getSelectQuery()
    {
        /** === Setup Mocks === */
        // $result = $this->_conn->select();
        $mResult = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')->once()
            ->andReturn($mResult);
        // $tblStock = [$asStock => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK)];
        // $tblWrhs = [$asWrhs => $this->_resource->getTableName(EntityWarehouse::ENTITY_NAME)];
        $this->mResource
            ->shouldReceive('getTableName')->twice();
        // $result->from($tblStock, $cols);
        $mResult->shouldReceive('from', 'joinLeft');
        /** === Call and asserts  === */
        $this->obj->getQueryToSelect();
    }

}