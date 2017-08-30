<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\Customer;

use Praxigento\Warehouse\Repo\Entity\Data\Customer as EntityCustomer;
use Praxigento\Warehouse\Service\Customer;

class Call implements \Praxigento\Warehouse\Service\ICustomer
{
    /** @var \Praxigento\Warehouse\Repo\Entity\Customer */
    protected $_repoCustomer;
    /** @var \Magento\Customer\Model\Session */
    protected $_session;
    /** @var Sub\Repo */
    protected $_subRepo;

    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Praxigento\Warehouse\Repo\Entity\Customer $repoCustomer,
        \Praxigento\Warehouse\Service\Customer\Sub\Repo $subRepo
    ) {
        $this->_session = $session;
        $this->_repoCustomer = $repoCustomer;
        $this->_subRepo = $subRepo;
    }

    /** @inheritdoc */
    public function getCurrentStock(Customer\Request\GetCurrentStock $req)
    {
        $result = new Response\GetCurrentStock();
        $custId = $req->getCustomerId();
        if (!$custId) {
            $custId = $this->_session->getCustomerId();
        }
        $link = $this->_repoCustomer->getById($custId);
        if ($link) {
            $stockId = $link->getStockRef();
        } else {
            $stockId = $this->_subRepo->getStockId();
            $data = [
                EntityCustomer::ATTR_CUST_REF => $custId,
                EntityCustomer::ATTR_STOCK_REF => $stockId
            ];
            $this->_repoCustomer->create($data);
        }
        $result->setStockId($stockId);
        $result->markSucceed();
        return $result;
    }


}