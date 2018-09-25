<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Plugin\Magento\Sales\Model\Service;

use Praxigento\Warehouse\Service\Sale\Order\Delete\Request as ARequest;

class OrderService
{
    /** @var \Praxigento\Warehouse\Helper\Config */
    private $hlpConfig;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Warehouse\Service\Sale\Order\Delete */
    private $servSaleDelete;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Warehouse\Helper\Config $hlpConfig,
        \Praxigento\Warehouse\Service\Sale\Order\Delete $servSaleDelete
    ) {
        $this->manTrans = $manTrans;
        $this->hlpConfig = $hlpConfig;
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
                $deleteSales = $this->hlpConfig->getSalesGeneralDeleteCancelled();
                $req->setCleanDb($deleteSales);
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