<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Agg\Def;

use Praxigento\Warehouse\Data\Agg\Warehouse as AggWarehouse;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Warehouse_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  Warehouse\SelectFactory */
    private $mFactorySelect;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mRepoEntityWarehouse;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  Warehouse */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mConn = $this->_mockConn();
        $this->mResource = $this->_mockResourceConnection($this->mConn);
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        $this->mRepoEntityWarehouse = $this->_mock(\Praxigento\Warehouse\Repo\Entity\IWarehouse::class);
        $this->mFactorySelect = $this->_mock(Warehouse\SelectFactory::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new Warehouse(
            $this->mManObj,
            $this->mManTrans,
            $this->mResource,
            $this->mRepoGeneric,
            $this->mRepoEntityWarehouse,
            $this->mFactorySelect
        );
    }

    public function test_constructor()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        /** === Call and asserts  === */
        $this->assertInstanceOf(Warehouse::class, $this->obj);
    }

    public function test_create_isStockId()
    {
        /** === Test Data === */
        $ID = 32;
        $DATA = new AggWarehouse([
            AggWarehouse::AS_CODE => 'code',
            AggWarehouse::AS_WEBSITE_ID => 'website_id',
            AggWarehouse::AS_CURRENCY => 'currency',
            AggWarehouse::AS_NOTE => 'note',
            AggWarehouse::AS_ID => $ID
        ]);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mock(\Praxigento\Core\Transaction\Database\IDefinition::class);
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $stockData = $this->_repoBasic->getEntityByPk($tbl, [Cfg::E_CATINV_STOCK_A_STOCK_ID => $stockId]);
        $this->mRepoGeneric
            ->shouldReceive('getEntityByPk')->once()
            ->andReturn(null);
        // $id = $this->_repoBasic->addEntity($tbl, $bind);
        $this->mRepoGeneric
            ->shouldReceive('addEntity')->once()
            ->andReturn($ID);
        // $this->_repoBasic->addEntity($tbl, $bind);
        $this->mRepoGeneric
            ->shouldReceive('addEntity')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once()
            ->with($mDef);
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once()
            ->with($mDef);
        /** === Call and asserts  === */
        $res = $this->obj->create($DATA);
        $this->assertInstanceOf(AggWarehouse::class, $res);
        $this->assertEquals($ID, $res->getId());
    }

    public function test_create_noStockId()
    {
        /** === Test Data === */
        $ID = 32;
        $DATA = new AggWarehouse([
            AggWarehouse::AS_CODE => 'code',
            AggWarehouse::AS_WEBSITE_ID => 'website_id',
            AggWarehouse::AS_CURRENCY => 'currency',
            AggWarehouse::AS_NOTE => 'note'
        ]);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mock(\Praxigento\Core\Transaction\Database\IDefinition::class);
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $id = $this->_repoBasic->addEntity($tbl, $bind);
        $this->mRepoGeneric
            ->shouldReceive('addEntity')->once()
            ->andReturn($ID);
        // $this->_repoBasic->addEntity($tbl, $bind);
        $this->mRepoGeneric
            ->shouldReceive('addEntity')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once()
            ->with($mDef);
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once()
            ->with($mDef);
        /** === Call and asserts  === */
        $res = $this->obj->create($DATA);
        $this->assertInstanceOf(AggWarehouse::class, $res);
        $this->assertEquals($ID, $res->getId());
    }

    public function test_getById()
    {
        /** === Test Data === */
        $ID = 21;
        $DATA = ['data'];
        /** === Setup Mocks === */
        // $query = $this->_factorySelect->getQueryToSelect();
        $mQuery = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelect')->once()
            ->andReturn($mQuery);
        // $query->where(self::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . '=:id');
        $mQuery->shouldReceive('where')->once();
        // $data = $this->_conn->fetchRow($query, ['id' => $id]);
        $this->mConn
            ->shouldReceive('fetchRow')->once()
            ->andReturn($DATA);
        // $result = $this->_initResultRead($data);
        // $result = $this->_manObj->create(AggWarehouse::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn(new AggWarehouse());
        /** === Call and asserts  === */
        $res = $this->obj->getById($ID);
        $this->assertInstanceOf(AggWarehouse::class, $res);
    }

    public function test_getQueryToSelect()
    {
        /** === Test Data === */
        $QUERY = 'query';
        /** === Setup Mocks === */
        // $result = $this->_factorySelect->getQueryToSelect();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelect')->once()
            ->andReturn($QUERY);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelect();
        $this->assertEquals($QUERY, $res);
    }

    public function test_getQueryToSelectCount()
    {
        /** === Test Data === */
        $QUERY = 'query';
        /** === Setup Mocks === */
        // $result = $this->_factorySelect->getQueryToSelectCount();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelectCount')->once()
            ->andReturn($QUERY);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelectCount();
        $this->assertEquals($QUERY, $res);
    }

}