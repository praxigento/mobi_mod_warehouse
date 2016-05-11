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
        $this->mFactorySelect = $this->_mock(Warehouse\SelectFactory::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new Warehouse(
            $this->mManObj,
            $this->mManTrans,
            $this->mResource,
            $this->mRepoGeneric,
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

    public function test_create()
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
        // $trans = $this->_manTrans->transactionBegin();
        $mTrans = $this->_mock(\Praxigento\Core\Repo\ITransactionDefinition::class);
        $this->mManTrans
            ->shouldReceive('transactionBegin')->once()
            ->andReturn($mTrans);
        // $id = $this->_repoBasic->addEntity($tbl, $bind);
        $this->mRepoGeneric
            ->shouldReceive('addEntity')->once()
            ->andReturn($ID);
        // $this->_repoBasic->addEntity($tbl, $bind);
        $this->mRepoGeneric
            ->shouldReceive('addEntity')->once();
        // $this->_manTrans->transactionCommit($trans);
        $this->mManTrans
            ->shouldReceive('transactionCommit')->once()
            ->with($mTrans);
        // $this->_manTrans->transactionClose($trans);
        $this->mManTrans
            ->shouldReceive('transactionClose')->once()
            ->with($mTrans);
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
        // $query = $this->_factorySelect->getSelectQuery();
        $mQuery = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getSelectQuery')->once()
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

}