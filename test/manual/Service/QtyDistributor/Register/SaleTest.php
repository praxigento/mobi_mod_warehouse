<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Warehouse\Service\QtyDistributor\Register;

use Praxigento\Warehouse\Service\QtyDistributor\Register\Sale as AService;
use Praxigento\Warehouse\Service\QtyDistributor\Register\Sale\Request as ARequest;
use Praxigento\Warehouse\Service\QtyDistributor\Register\Sale\Response as AResponse;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Delete
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_execute()
    {
        $this->setAreaCode();
        $req = new ARequest();
        $req->set(5);
        $req->setCleanDb(true);
        /** @var AService $serv */
        $serv = $this->manObj->get(AService::class);
        $def = $this->manTrans->begin();
        $resp = $serv->exec($req);
        $this->manTrans->rollback($def);
        $this->assertInstanceOf(AResponse::class, $resp);
    }
}