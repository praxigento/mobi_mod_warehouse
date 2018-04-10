<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Setup;

use Praxigento\Warehouse\Repo\Data\Group\Price;
use Praxigento\Warehouse\Repo\Data\Lot;
use Praxigento\Warehouse\Repo\Data\Quantity;
use Praxigento\Warehouse\Repo\Data\Quantity\Sale;
use Praxigento\Warehouse\Repo\Data\Quote as EQuote;
use Praxigento\Warehouse\Repo\Data\Stock\Item;
use Praxigento\Warehouse\Repo\Data\Warehouse;

class InstallSchema extends \Praxigento\Core\App\Setup\Schema\Base
{

    protected function setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Warehouse';
        $demPackage = $this->toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Stock Item  */
        $demEntity = $demPackage->get('package/Stock/entity/Item');
        $this->toolDem->createEntity(Item::ENTITY_NAME, $demEntity);

        /* Group Price */
        $demEntity = $demPackage->get('package/Group/entity/Price');
        $this->toolDem->createEntity(Price::ENTITY_NAME, $demEntity);

        /* Quote */
        $demEntity = $demPackage->get('entity/Quote');
        $this->toolDem->createEntity(EQuote::ENTITY_NAME, $demEntity);

        /* Warehouse */
        $demEntity = $demPackage->get('entity/Warehouse');
        $this->toolDem->createEntity(Warehouse::ENTITY_NAME, $demEntity);

        /* Lot */
        $demEntity = $demPackage->get('entity/Lot');
        $this->toolDem->createEntity(Lot::ENTITY_NAME, $demEntity);

        /* Quant */
        $demEntity = $demPackage->get('entity/Quantity');
        $this->toolDem->createEntity(Quantity::ENTITY_NAME, $demEntity);

        /* Quant / Sale */
        $demEntity = $demPackage->get('package/Quantity/entity/Sale');
        $this->toolDem->createEntity(Sale::ENTITY_NAME, $demEntity);

    }
}