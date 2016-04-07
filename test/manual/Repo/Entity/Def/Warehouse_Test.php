<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Lib\Entity\Def;

use Magento\Framework\App\ObjectManager;
use Praxigento\Warehouse\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Warehouse\Repo\Entity\IWarehouse;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Warehouse_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
{

    public function test_create()
    {
        $obm = ObjectManager::getInstance();
        /** @var  $repo IWarehouse */
        $repo = $obm->get(IWarehouse::class);
        /** @var  $data AggWarehouse */
        $data = $obm->create(AggWarehouse::class);
        $data->setWebsiteId(self::DEF_WEBSITE_ID_BASE);
        $data->setCode('TEST STOCK 2');
        $data->setNote('Сделано из теста');
        $created = $repo->create($data);
        return;
    }

    public function test_getById()
    {
        $obm = ObjectManager::getInstance();
        /** @var  $repo IWarehouse */
        $repo = $obm->get(IWarehouse::class);
        $data = $repo->getById(3);
        return;
    }

}