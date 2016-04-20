<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Stock\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Item_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  Item */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create mocks */
        $this->mConn = $this->_mockConn();
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        /* setup mocks for constructor */
        // parent::__construct($resource);
        $mResource = $this->_mockResourceConnection($this->mConn);
        /* create object to test */
        $this->obj = new Item(
            $mResource,
            $this->mRepoGeneric
        );
    }

    public function test_constructor()
    {
        /* === Test Data === */
        /* === Setup Mocks === */
        /* === Call and asserts  === */
        $this->assertInstanceOf(Item::class, $this->obj);
    }

}