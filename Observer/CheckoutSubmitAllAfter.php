<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Observer;

use Praxigento\Warehouse\Plugin\Quote\Model\QuoteRepository as AQuoteRepo;
use Praxigento\Warehouse\Service\QtyDistributor\Data\Item as DItem;
use Praxigento\Warehouse\Service\QtyDistributor\Register\Sale\Request as ARequest;

/**
 * 1) Split items qty by lots and register it (for credit cards payments events).
 * 2) Remove placed quote from warehouse registry.
 */
class CheckoutSubmitAllAfter
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_ORDER = 'order';
    const DATA_QUOTE = 'quote';

    /** @var \Praxigento\Warehouse\Repo\Dao\Quote */
    private $daoWrhsQuote;
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale */
    private $servRegSale;
    /** @var \Magento\Checkout\Model\Session */
    private $sessCheckout;
    /** @var \Magento\Customer\Model\Session */
    private $sessCustomer;

    public function __construct(
        \Magento\Checkout\Model\Session $sessCheckout,
        \Magento\Customer\Model\Session $sessCustomer,
        \Praxigento\Warehouse\Repo\Dao\Quote $daoWrhsQuote,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale $servRegSale
    ) {
        $this->sessCheckout = $sessCheckout;
        $this->sessCustomer = $sessCustomer;
        $this->daoWrhsQuote = $daoWrhsQuote;
        $this->hlpStock = $hlpStock;
        $this->servRegSale = $servRegSale;
    }

    /**
     * Remove placed quote from warehouse registry.
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     */
    private function deactivateQuote($quote)
    {
        $quoteId = $quote->getId();
        if ($this->sessCustomer->isLoggedIn()) {
            $this->daoWrhsQuote->deleteById($quoteId);
        } else {
            $quoteReg = $this->sessCustomer->getData(AQuoteRepo::SESS_QUOTE_REGISTRY);
            if (
                is_array($quoteReg) &&
                isset($quoteReg[$quoteId])
            ) {
                unset($quoteReg[$quoteId]);
                $this->sessCustomer->setData(AQuoteRepo::SESS_QUOTE_REGISTRY, $quoteReg);
            }
        }
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** define local working data */
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData(self::DATA_ORDER);
        /** @var \Magento\Quote\Api\Data\CartInterface $quote */
        $quote = $observer->getData(self::DATA_QUOTE);

        /** perform processing */
        $this->splitQty($order);
        $this->deactivateQuote($quote);
    }

    /**
     * Split items qty by lots and register it (for credit cards payments events).
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @throws \Exception
     */
    private function splitQty(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $storeId = $order->getStoreId();
        /* get stock ID for the store view */
        $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
        /** @var \Magento\Sales\Api\Data\OrderItemInterface[] $items */
        $items = $order->getItems();
        $itemsData = [];
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($items as $item) {
            $prodId = $item->getProductId();
            $itemId = $item->getItemId();
            /* qty of the product can be changed in invoice, but we use ordered qtys only  */
            $qty = $item->getQtyOrdered();
            /* register sale item (fragment total qty by lots) */
            $itemData = new DItem();
            $itemData->setItemId($itemId);
            $itemData->setProductId($prodId);
            $itemData->setQuantity($qty);
            $itemData->setStockId($stockId);
            $itemsData[] = $itemData;
        }
        $reqSale = new ARequest();
        $reqSale->setSaleItems($itemsData);
        $this->servRegSale->exec($reqSale);
    }
}