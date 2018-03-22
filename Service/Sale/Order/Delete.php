<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Service\Sale\Order;

use Praxigento\Core\App\Repo\Query\Expression as AnExpression;
use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Data\Quantity as EQty;
use Praxigento\Warehouse\Repo\Data\Quantity\Sale as EQtySale;
use Praxigento\Warehouse\Service\Sale\Order\Delete\Own\Repo\Query\GetStockItem as OwnQbGetStockItem;
use Praxigento\Warehouse\Service\Sale\Order\Delete\Request as ARequest;
use Praxigento\Warehouse\Service\Sale\Order\Delete\Response as AResponse;

/**
 * Return products of the cancelled order to the inventory then remove sale order and items..
 */
class Delete
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Praxigento\Warehouse\Service\Sale\Order\Delete\Own\Repo\Query\GetStockItem */
    private $qbGetStockItem;
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    private $repoGeneric;
    /** @var \Praxigento\Warehouse\Repo\Dao\Quantity */
    private $repoQty;
    /** @var \Praxigento\Warehouse\Repo\Dao\Quantity\Sale */
    private $repoQtySale;
    /** \Magento\Sales\Api\OrderRepositoryInterface */
    private $repoSaleOrder;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $repoSaleOrder,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric,
        \Praxigento\Warehouse\Repo\Dao\Quantity $repoQty,
        \Praxigento\Warehouse\Repo\Dao\Quantity\Sale $repoQtySale,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        OwnQbGetStockItem $qbGetStockItem
    ) {
        $this->repoSaleOrder = $repoSaleOrder;
        $this->repoGeneric = $repoGeneric;
        $this->repoQty = $repoQty;
        $this->repoQtySale = $repoQtySale;
        $this->hlpStock = $hlpStock;
        $this->qbGetStockItem = $qbGetStockItem;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     * @throws \Exception
     */
    public function exec($request)
    {
        /** define local working data */
        assert($request instanceof ARequest);
        $saleId = $request->getSaleId();
        $cleanDb = $request->getCleanDb();

        /** perform processing */
        /** @var \Magento\Sales\Api\Data\OrderInterface $sale */
        $sale = $this->repoSaleOrder->get($saleId);
        if ($sale) {
            $storeId = $sale->getStoreId();
            $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
            $items = $sale->getAllItems();
            /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
            foreach ($items as $item) {
                $saleItemId = $item->getItemId();
                $prodId = $item->getProductId();
                $stockItemId = $this->getStockItemId($prodId, $stockId);
                /* get all quantums for sale item (qty-by-lot) */
                $quants = $this->getSaleItemQty($saleItemId);
                $qtyTotal = 0;
                foreach ($quants as $quant) {
                    $lotId = $quant->getLotRef();
                    $qty = $quant->getTotal();
                    /* return quantum qty to the lot then summarize it by stock item */
                    $lots[$lotId] = $qty;
                    $this->returnQtyToLot($stockItemId, $lotId, $qty);
                    $qtyTotal += $qty;
                }
                /* return qty summary to Magento inventory */
                $this->returnQtyToCatalogInventory($stockItemId, $qtyTotal);
                $this->returnQtyToCatalogInventoryStatus($prodId, $stockId, $qtyTotal);
                if ($cleanDb) {
                    /* delete item from prxgt_wrhs_qty_sale */
                    $this->removeSaleItemQty($saleItemId);
                    /* delete sale order item (we can remove all items by one stmt using saleId) */
                    $this->removeSaleItem($saleItemId);
                }
            }
            /* delete sale order from DB */
            if ($cleanDb) {
                $this->removeSaleGrid($saleId);
                $this->removeSale($saleId);
            }
        }
        /** compose result */
        $result = new AResponse();
        $result->isSucceed();
        return $result;
    }

    /**
     * Get sale items with quantities by lots (quantums).
     * @param int $saleItemId
     * @return EQtySale[]
     */
    private function getSaleItemQty($saleItemId)
    {
        $where = EQtySale::ATTR_SALE_ITEM_REF . '=' . (int)$saleItemId;
        $result = $this->repoQtySale->get($where);
        return $result;
    }

    /**
     * Get stock item ID by product ID & stock ID.
     *
     * @param int $prodId
     * @param int $stockId
     * @return int
     */
    private function getStockItemId($prodId, $stockId)
    {
        $query = $this->qbGetStockItem->build();
        $conn = $query->getConnection();
        $bind = [
            OwnQbGetStockItem::BND_PROD_ID => $prodId,
            OwnQbGetStockItem::BND_STOCK_ID => $stockId
        ];
        $result = $conn->fetchOne($query, $bind);
        return $result;
    }

    private function removeSale($saleId)
    {
        $entity = Cfg::ENTITY_MAGE_SALES_ORDER;
        $id = [Cfg::E_SALE_ORDER_A_ENTITY_ID => $saleId];
        $this->repoGeneric->deleteEntityByPk($entity, $id);
    }

    private function removeSaleGrid($saleId)
    {
        $entity = Cfg::ENTITY_MAGE_SALES_ORDER_GRID;
        $id = [Cfg::E_SALE_ORDER_GRID_A_ENTITY_ID => $saleId];
        $this->repoGeneric->deleteEntityByPk($entity, $id);
    }

    private function removeSaleItem($saleItemId)
    {
        $entity = Cfg::ENTITY_MAGE_SALES_ORDER_ITEM;
        $id = [Cfg::E_SALE_ORDER_ITEM_A_ITEM_ID => $saleItemId];
        $this->repoGeneric->deleteEntityByPk($entity, $id);
    }

    private function removeSaleItemQty($saleItemId)
    {
        $where = EQtySale::ATTR_SALE_ITEM_REF . '=' . (int)$saleItemId;
        $this->repoQtySale->delete($where);
    }

    private function returnQtyToCatalogInventory($stockItemId, $qty)
    {
        $entity = Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM;
        $exp = '(`' . Cfg::E_CATINV_STOCK_ITEM_A_QTY . '`+' . abs((float)$qty) . ')';
        $exp = new AnExpression($exp);
        $data = [Cfg::E_CATINV_STOCK_ITEM_A_QTY => $exp];
        $where = Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID . '=' . (int)$stockItemId;
        $updated = $this->repoGeneric->updateEntity($entity, $data, $where);
        if ($updated != 1) {
            throw  new \Exception("Data inconsistency on return qty to catalog inventory item ($stockItemId/$qty).");
        }
    }

    private function returnQtyToCatalogInventoryStatus($prodId, $stockId, $qty)
    {
        $entity = Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_STATUS;
        $exp = '(`' . Cfg::E_CATINV_STOCK_STATUS_A_QTY . '`+' . abs((float)$qty) . ')';
        $exp = new AnExpression($exp);
        $data = [Cfg::E_CATINV_STOCK_STATUS_A_QTY => $exp];
        $byProd = Cfg::E_CATINV_STOCK_STATUS_A_PROD_ID . '=' . (int)$prodId;
        $byStock = Cfg::E_CATINV_STOCK_STATUS_A_STOCK_ID . '=' . (int)$stockId;
        $where = "($byProd) AND ($byStock)";
        $updated = $this->repoGeneric->updateEntity($entity, $data, $where);
        if ($updated != 1) {
            throw  new \Exception("Data inconsistency on return qty to catalog inventory status ($prodId/$stockId/$qty).");
        }
    }

    private function returnQtyToLot($stockItemId, $lotId, $qty)
    {
        $exp = '(`' . EQty::ATTR_TOTAL . '`+' . abs((float)$qty) . ')';
        $exp = new AnExpression($exp);
        $data = [EQty::ATTR_TOTAL => $exp];
        $byStock = EQty::ATTR_STOCK_ITEM_REF . '=' . (int)$stockItemId;
        $byLot = EQty::ATTR_LOT_REF . '=' . (int)$lotId;
        $where = "($byStock) AND ($byLot)";
        $updated = $this->repoQty->update($data, $where);
        if ($updated != 1) {
            throw  new \Exception("Data inconsistency on return qty to warehouse lot ($stockItemId/$lotId/$qty).");
        }
    }
}