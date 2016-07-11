<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Ui\DataProvider\Product;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class AddQuantityFieldToCollection_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{


    /** @var  AddQuantityFieldToCollection */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new AddQuantityFieldToCollection();
    }

    public function test_aroundAddField()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        $mSubject = $this->_mock(\Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityFieldToCollection::class);
        $mProceed = function () {
        };
        $mCollection = $this->_mock(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        // $resource = $collection->getResource();
        $mResource = $this->_mockConn(['getTableName']);
        $mCollection
            ->shouldReceive('getResource')->once()
            ->andReturn($mResource);
        // $select = $collection->getSelect();
        $mSelect = $this->_mockDbSelect(['joinLeft', 'group']);
        $mCollection
            ->shouldReceive('getSelect')->once()
            ->andReturn($mSelect);


        /** === Call and asserts  === */
        $this->obj->aroundAddField($mSubject, $mProceed, $mCollection);

    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(AddQuantityFieldToCollection::class, $this->obj);
    }

}