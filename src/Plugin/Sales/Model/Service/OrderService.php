<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Plugin\Sales\Model\Service;

use Praxigento\Warehouse\Service\Sale\Order\Delete\Request as ARequest;

class OrderService
{
    /** @var \Praxigento\Warehouse\Service\Sale\Order\Delete */
    private $servSaleDelete;

    public function __construct(
        \Praxigento\Warehouse\Service\Sale\Order\Delete $servSaleDelete
    ) {
        $this->servSaleDelete = $servSaleDelete;
    }


    /**
     * @param \Magento\Sales\Model\Service\OrderService $subject
     * @param \Closure $proceed
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function aroundCancel(
        \Magento\Sales\Model\Service\OrderService $subject,
        \Closure $proceed,
        $id
    ) {
        $result = $proceed($id);
        if ($result === true) {
            $req = new ARequest();
            $req->setSaleId($id);
            $req->setCleanDb(true);
            $resp = $this->servSaleDelete->exec($req);
        }
        return $result;
    }
}