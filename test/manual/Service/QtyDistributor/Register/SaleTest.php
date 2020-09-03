<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Praxigento\Warehouse\Service\QtyDistributor\Register;


use Praxigento\Warehouse\Service\QtyDistributor\Data\Item as DItem;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class SaleTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var  \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale */
    private $obj;
    /** @var  \Magento\Sales\Api\OrderRepositoryInterface */
    private $repoSaleOrder;

    /**
     * Process qty for sale items for one sale order.
     *
     * @param int $saleId
     * @throws \Exception
     */
    private function oneItem($saleId)
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $sale */
        $sale = $this->repoSaleOrder->get($saleId);
        // see \Praxigento\Warehouse\Observer\CheckoutSubmitAllAfter::splitQty
        $storeId = $sale->getStoreId();
        /* get stock ID for the store view */
        $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
        /** @var \Magento\Sales\Api\Data\OrderItemInterface[] $items */
        $items = $sale->getItems();
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
        $req = new \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale\Request();
        $req->setSaleItems($itemsData);
        $resp = $this->obj->exec($req);
        $this->assertInstanceOf(\Praxigento\Warehouse\Service\QtyDistributor\Register\Sale\Response::class, $resp);
    }

    protected function setUp(): void
    {
        $this->hlpStock = $this->manObj->get(\Praxigento\Warehouse\Api\Helper\Stock::class);
        $this->repoSaleOrder = $this->manObj->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $this->obj = $this->manObj->create(\Praxigento\Warehouse\Service\QtyDistributor\Register\Sale::class);
    }

    public function test_create()
    {
        $this->oneItem(3421);
    }
}
