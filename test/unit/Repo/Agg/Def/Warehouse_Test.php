<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Agg\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

use Praxigento\Warehouse\Data\Agg\Warehouse as AggWarehouse;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Warehouse_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo
{
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
    /** @var  Warehouse */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        $this->mRepoEntityWarehouse = $this->_mock(\Praxigento\Warehouse\Repo\Entity\Warehouse::class);
        $this->mFactorySelect = $this->_mock(Warehouse\SelectFactory::class);
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
        /** === Call and asserts  === */
        $this->assertInstanceOf(Warehouse::class, $this->obj);
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function test_create_isStockId()
    {
        /** === Test Data === */
        $id = 32;
        $data = new AggWarehouse([
            AggWarehouse::AS_CODE => 'code',
            AggWarehouse::AS_WEBSITE_ID => 'website_id',
            AggWarehouse::AS_CURRENCY => 'currency',
            AggWarehouse::AS_NOTE => 'note',
            AggWarehouse::AS_ID => $id
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
            ->andReturn($id);
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
        $res = $this->obj->create($data);
        $this->assertInstanceOf(AggWarehouse::class, $res);
        $this->assertEquals($id, $res->getId());
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function test_create_noStockId()
    {
        /** === Test Data === */
        $id = 32;
        $data = new AggWarehouse([
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
            ->andReturn($id);
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
        $res = $this->obj->create($data);
        $this->assertInstanceOf(AggWarehouse::class, $res);
        $this->assertEquals($id, $res->getId());
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function test_getById()
    {
        /** === Test Data === */
        $id = 21;
        $data = ['data'];
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
            ->andReturn($data);
        // $result = $this->_initResultRead($data);
        // $result = $this->_manObj->create(AggWarehouse::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn(new AggWarehouse());
        /** === Call and asserts  === */
        $res = $this->obj->getById($id);
        $this->assertInstanceOf(AggWarehouse::class, $res);
    }

    public function test_getQueryToSelect()
    {
        /** === Test Data === */
        $query = 'query';
        /** === Setup Mocks === */
        // $result = $this->_factorySelect->getQueryToSelect();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelect')->once()
            ->andReturn($query);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelect();
        $this->assertEquals($query, $res);
    }

    public function test_getQueryToSelectCount()
    {
        /** === Test Data === */
        $query = 'query';
        /** === Setup Mocks === */
        // $result = $this->_factorySelect->getQueryToSelectCount();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelectCount')->once()
            ->andReturn($query);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelectCount();
        $this->assertEquals($query, $res);
    }

}