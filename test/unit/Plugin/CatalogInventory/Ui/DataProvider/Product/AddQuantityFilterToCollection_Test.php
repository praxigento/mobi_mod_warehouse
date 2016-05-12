<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Ui\DataProvider\Product;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class AddQuantityFilterToCollection_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{


    /** @var  AddQuantityFilterToCollection */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new AddQuantityFilterToCollection();
    }

    public function test_aroundAddFilter()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        $mSubject = $this->_mock(\Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityFilterToCollection::class);
        $mProceed = function () {
        };
        /** === Call and asserts  === */
        $this->obj->aroundAddFilter($mSubject, $mProceed);

    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(AddQuantityFilterToCollection::class, $this->obj);
    }

}