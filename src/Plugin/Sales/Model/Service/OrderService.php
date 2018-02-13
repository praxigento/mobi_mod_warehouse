<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Plugin\Sales\Model\Service;

use Praxigento\Warehouse\Service\Sale\Order\Delete\Request as ARequest;

class OrderService
{
    /** @var \Praxigento\Core\App\Transaction\Database\IManager */
    private $manTrans;
    /** @var \Praxigento\Warehouse\Service\Sale\Order\Delete */
    private $servSaleDelete;

    public function __construct(
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\Warehouse\Service\Sale\Order\Delete $servSaleDelete
    ) {
        $this->manTrans = $manTrans;
        $this->servSaleDelete = $servSaleDelete;
    }


    /**
     * @param \Magento\Sales\Model\Service\OrderService $subject
     * @param \Closure $proceed
     * @param int $id
     * @return bool
     * @throws \Throwable
     */
    public function aroundCancel(
        \Magento\Sales\Model\Service\OrderService $subject,
        \Closure $proceed,
        $id
    ) {
        $def = $this->manTrans->begin();
        try {
            $result = $proceed($id);
            if ($result === true) {
                $req = new ARequest();
                $req->setSaleId($id);
                $req->setCleanDb(true);
                $this->servSaleDelete->exec($req);
            }
            $this->manTrans->commit($def);
        } finally {
            /* rollback transaction on exception (nothing is done if transaction was committed before) */
            $this->manTrans->end($def);
        }
        return $result;
    }
}