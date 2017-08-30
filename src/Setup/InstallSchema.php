<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Setup;

use Praxigento\Warehouse\Repo\Entity\Data\Customer;
use Praxigento\Warehouse\Repo\Entity\Data\Group\Price;
use Praxigento\Warehouse\Repo\Entity\Data\Lot;
use Praxigento\Warehouse\Repo\Entity\Data\Quantity;
use Praxigento\Warehouse\Repo\Entity\Data\Quantity\Sale;
use Praxigento\Warehouse\Repo\Entity\Data\Stock\Item;
use Praxigento\Warehouse\Repo\Entity\Data\Warehouse;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{

    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Warehouse';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Stock Item  */
        $entityAlias = Item::ENTITY_NAME;
        $demEntity = $demPackage->get('package/Stock/entity/Item');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Group Price */
        $entityAlias = Price::ENTITY_NAME;
        $demEntity = $demPackage->get('package/Group/entity/Price');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Warehouse */
        $entityAlias = Warehouse::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Warehouse');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Lot */
        $entityAlias = Lot::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Lot');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Quant */
        $entityAlias = Quantity::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Quantity');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Quant / Sale */
        $entityAlias = Sale::ENTITY_NAME;
        $demEntity = $demPackage->get('package/Quantity/entity/Sale');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Customer */
        $entityAlias = Customer::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Customer');
        $this->_toolDem->createEntity($entityAlias, $demEntity);
    }
}