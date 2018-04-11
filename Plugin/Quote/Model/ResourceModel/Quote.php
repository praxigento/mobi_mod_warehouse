<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Quote\Model\ResourceModel;

use Praxigento\Warehouse\Repo\Data\Quote as EWrhsQuote;

class Quote
{
    /** @var \Praxigento\Warehouse\Repo\Dao\Quote */
    private $daoWrhsQuote;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Framework\Session\SessionManager */
    private $sessionManager;

    public function __construct(
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Praxigento\Warehouse\Repo\Dao\Quote $daoWrhsQuote,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->sessionManager = $sessionManager;
        $this->daoWrhsQuote = $daoWrhsQuote;
        $this->hlpStock = $hlpStock;
    }

    /**
     * Register relation between new quote and stock on save.
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote $object
     * @return mixed
     * @throws \Exception
     */
    public function aroundSave(
        \Magento\Quote\Model\ResourceModel\Quote $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $object
    ) {
        /* TODO: remove it if not used */
        $idBefore = $object->getId();
        $result = $proceed($object);
        $isDeleted = $object->isDeleted();
        $isPreventSaving = $object->isPreventSaving();
//        if (!$idBefore && !$isDeleted && !$isPreventSaving) {
//            $id = $object->getId();
//            $custId = $object->getCustomerId();
//            $storeId = $object->getStoreId();
//            $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
//            $entity = new EWrhsQuote();
//            $entity->setCustRef($custId);
//            $entity->setQuoteRef($id);
//            $entity->setStockRef($stockId);
//            $this->daoWrhsQuote->create($entity);
//        }
        return $result;
    }
}