<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Praxigento\Warehouse\Service\Sale\Order\Delete;

use Praxigento\Warehouse\Service\Sale\Order\Delete as AService;
use Praxigento\Warehouse\Service\Sale\Order\Delete\Request as ARequest;
use Praxigento\Warehouse\Service\Sale\Order\Delete\Response as AResponse;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Delete
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_execute()
    {
        $this->setAreaCode();
        $req = new ARequest();
        $req->setSaleId(5);
        /** @var AService $serv */
        $serv = $this->manObj->get(AService::class);
        $def = $this->manTrans->begin();
        $resp = $serv->exec($req);
        $this->manTrans->rollback($def);
        $this->assertInstanceOf(AResponse::class, $resp);
    }
}