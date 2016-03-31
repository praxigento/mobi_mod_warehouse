<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Lib\Repo\Entity\Def;

use Praxigento\Core\Lib\Context as Ctx;
use Praxigento\Warehouse\Lib\Repo\Entity\ILot;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Lot_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
{


    public function test_getById()
    {
        $obm = Ctx::instance()->getObjectManager();
        /** @var  $repo ILot */
        $repo = $obm->get(ILot::class);
        $data = $repo->getById(1);
        return;
    }

}