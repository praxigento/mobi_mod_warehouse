<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Praxigento\Warehouse\Data\Entity\Lot;
use Praxigento\Warehouse\Data\Entity\Quantity;
use Praxigento\Warehouse\Data\Entity\Warehouse;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{

    protected function _setup(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Warehouse';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Warehouse */
        $entityAlias = Warehouse::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Warehouse');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Lot */
        $entityAlias = Lot::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Lot');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Quant */
        $entityAlias = Quantity::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Quantity');
        $this->_toolDem->createEntity($entityAlias, $demEntity);
    }
}