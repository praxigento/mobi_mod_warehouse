<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Setup;

use Praxigento\Warehouse\Data\Entity\Lot;
use Praxigento\Warehouse\Data\Entity\Quantity;
use Praxigento\Warehouse\Data\Entity\Stock\Item;
use Praxigento\Warehouse\Data\Entity\Warehouse;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class InstallSchema_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mContext;
    /** @var  \Mockery\MockInterface */
    private $mSetup;
    /** @var  \Mockery\MockInterface */
    private $mToolDem;
    /** @var  InstallSchema */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /* create mocks */
        $this->mConn = $this->_mockConn();
        $this->mToolDem = $this->_mock(\Praxigento\Core\Setup\Dem\Tool::class);
        $this->mSetup = $this->_mock(\Magento\Framework\Setup\SchemaSetupInterface::class);
        $this->mContext = $this->_mock(\Magento\Framework\Setup\ModuleContextInterface::class);
        /* create object */
        $mResource = $this->_mockResourceConnection($this->mConn);
        $this->obj = new InstallSchema($mResource, $this->mToolDem);
    }

    public function test_install()
    {
        /* === Test Data === */
        /* === Setup Mocks === */
        // $setup->startSetup();
        $this->mSetup
            ->shouldReceive('startSetup')->once();
        // $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);
        $mDemPackage = $this->_mock(DataObject::class);
        $this->mToolDem
            ->shouldReceive('readDemPackage')->once()
            ->withArgs([anything(), '/dBEAR/package/Praxigento/package/Warehouse'])
            ->andReturn($mDemPackage);
        // $demEntity = $demPackage->getData('package/Type/entity/Asset');
        $mDemPackage->shouldReceive('getData');
        //
        // $this->_toolDem->createEntity($entityAlias, $demEntity);
        //
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Item::ENTITY_NAME, anything()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Warehouse::ENTITY_NAME, anything()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Lot::ENTITY_NAME, anything()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Quantity::ENTITY_NAME, anything()]);
        // $setup->endSetup();
        $this->mSetup
            ->shouldReceive('endSetup')->once();
        /* === Call and asserts  === */
        $this->obj->install($this->mSetup, $this->mContext);
    }
}