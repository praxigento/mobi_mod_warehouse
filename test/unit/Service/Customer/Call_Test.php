<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\Customer;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mRepoCustomer;
    /** @var  \Mockery\MockInterface */
    private $mSession;
    /** @var  \Mockery\MockInterface */
    private $mSubRepo;
    /** @var  Call */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSession = $this->_mock(\Magento\Customer\Model\Session::class);
        $this->mRepoCustomer = $this->_mock(\Praxigento\Warehouse\Repo\Entity\ICustomer::class);
        $this->mSubRepo = $this->_mock(\Praxigento\Warehouse\Service\Customer\Sub\Repo::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mSession,
            $this->mRepoCustomer,
            $this->mSubRepo
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(\Praxigento\Warehouse\Service\ICustomer::class, $this->obj);
    }

    public function test_getCurrentStock_isCustomer_isLink()
    {
        /** === Test Data === */
        $CUST_ID = 21;
        $STOCK_ID = 32;
        $LINK = new \Praxigento\Warehouse\Data\Entity\Customer();
        $LINK->setStockRef($STOCK_ID);
        /** === Setup Mocks === */
        // $link = $this->_repoCustomer->getById($custId);
        $this->mRepoCustomer
            ->shouldReceive('getById')->once()
            ->andReturn($LINK);
        /** === Call and asserts  === */
        $req = new Request\GetCurrentStock ();
        $req->setCustomerId($CUST_ID);
        $resp = $this->obj->getCurrentStock($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($STOCK_ID, $resp->getStockId());
    }
    public function test_getCurrentStock_noCustomer_noLink()
    {
        /** === Test Data === */
        $CUST_ID = 21;
        $STOCK_ID = 32;
        $LINK = new \Praxigento\Warehouse\Data\Entity\Customer();
        $LINK->setStockRef($STOCK_ID);
        /** === Setup Mocks === */
        // $custId = $this->_session->getCustomerId();
        $this->mSession
            ->shouldReceive('getCustomerId')->once()
            ->andReturn($CUST_ID);
        // $link = $this->_repoCustomer->getById($custId);
        $this->mRepoCustomer
            ->shouldReceive('getById')->once()
            ->andReturn(null);
        // $stockId = $this->_subRepo->getStockId();
        $this->mSubRepo
            ->shouldReceive('getStockId')->once()
            ->andReturn($STOCK_ID);
        // $this->_repoCustomer->create($data);
        $this->mRepoCustomer
            ->shouldReceive('create')->once();
        /** === Call and asserts  === */
        $req = new Request\GetCurrentStock ();
        $resp = $this->obj->getCurrentStock($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($STOCK_ID, $resp->getStockId());
    }
}