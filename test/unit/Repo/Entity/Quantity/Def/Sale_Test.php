<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Quantity\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Item_UnitTest extends \Praxigento\Core\Test\BaseCase\Repo\Entity
{
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  Sale */
    private $obj;
    /** @var array Constructor arguments for object mocking */
    private $objArgs = [];

    protected function setUp()
    {
        parent::setUp();
        $this->mManObj = $this->_mockObjectManager();
        /** reset args. to create mock of the tested object */
        $this->objArgs = [
            $this->mManObj,
            $this->mResource,
            $this->mRepoGeneric
        ];
        /** create object to test */
        $this->obj = new Sale(
            $this->mManObj,
            $this->mResource,
            $this->mRepoGeneric
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Sale::class, $this->obj);
    }

    public function test_getBySaleItemId()
    {
        /** === Test Data === */
        $ID = 32;
        /** === Mock object itself === */
        $this->mResource
            ->shouldReceive('getConnection')->once()
            ->andReturn($this->mConn);
        $this->obj = \Mockery::mock(Sale::class . '[get]', $this->objArgs);
        /** === Setup Mocks === */
        // $rows = $this->get($where);
        $mRow = 'init data';
        $mRows = [$mRow];
        $this->obj->shouldReceive('get')->once()
            ->with('=' . $ID)
            ->andReturn($mRows);
        // $item = $this->_manObj->create(\Praxigento\Warehouse\Data\Entity\Quantity\Sale::class, ['arg1' => $row]);
        $mItem = 'item';
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(\Praxigento\Warehouse\Data\Entity\Quantity\Sale::class, ['arg1' => $mRow])
            ->andReturn($mItem);
        /** === Call and asserts  === */
        $res = $this->obj->getBySaleItemId($ID);
        $this->assertTrue(is_array($res));
        $this->assertEquals($mItem, current($res));
    }
}