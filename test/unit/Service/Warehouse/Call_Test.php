<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\Warehouse;

use Praxigento\Warehouse\Api\WarehouseInterface;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mRepoEntityWarehouse;
    /** @var  Call */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoEntityWarehouse = $this->_mock(\Praxigento\Warehouse\Repo\Entity\IWarehouse::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mManObj,
            $this->mManTrans,
            $this->mRepoEntityWarehouse
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(WarehouseInterface::class, $this->obj);
    }

    public function test_create()
    {
        /** === Test Data === */
        $WRHS = new \Praxigento\Warehouse\Data\Api\Def\Warehouse();
        $ID = 432;
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(Response\Create::class);
        $mResult = new Response\Create();
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(Response\Create::class)
            ->andReturn($mResult);
        // $tran = $this->_manTrans->transactionBegin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $warehouse = $data->getWarehouse();
        // $id = $this->_repoEntityWarehouse->create($bind);
        $this->mRepoEntityWarehouse
            ->shouldReceive('create')->once()
            ->andReturn($ID);
        // $this->_manTrans->transactionCommit($tran);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->transactionClose($tran);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\Create();
        $req->setWarehouse($WRHS);
        $res = $this->obj->create($req);
        $this->assertEquals($ID, $res->getId());
    }

    public function test_get()
    {
        /** === Test Data === */
        $WRHS = new \Praxigento\Warehouse\Data\Api\Def\Warehouse();
        $ID = 432;
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(Response\Get::class);
        $mResult = new Response\Get();
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(Response\Get::class)
            ->andReturn($mResult);
        // $data = $this->_repoEntityWarehouse->getById($id);
        $this->mRepoEntityWarehouse
            ->shouldReceive('getById')->once()
            ->with($ID)
            ->andReturn($WRHS);
        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setId($ID);
        $res = $this->obj->get($req);
        $this->assertTrue($res->isSucceed());
        $this->assertEquals($WRHS, $res->getWarehouse());
    }
}