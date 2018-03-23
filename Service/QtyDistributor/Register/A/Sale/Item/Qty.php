<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\QtyDistributor\Register\A\Sale\Item;

use Praxigento\Warehouse\Repo\Data\Quantity as EQuantity;
use Praxigento\Warehouse\Repo\Data\Quantity\Sale as EQtySale;
use Praxigento\Warehouse\Repo\Query\Lots\By\Product\Id\Get as AGet;

/**
 * Internal service for group of classes (\Praxigento\Warehouse\Service\QtyDistributor\Register\...).
 */
class Qty
{
    /** @var \Praxigento\Warehouse\Repo\Dao\Quantity */
    private $daoQty;
    /** @var \Praxigento\Warehouse\Repo\Dao\Quantity\Sale */
    private $daoQtySale;

    public function __construct(
        \Praxigento\Warehouse\Repo\Dao\Quantity $daoQty,
        \Praxigento\Warehouse\Repo\Dao\Quantity\Sale $daoQtySale
    )
    {
        $this->daoQty = $daoQty;
        $this->daoQtySale = $daoQtySale;
    }

    /**
     * Get lot(s) with the closest expiration date and write off quantity from this lot(s). Register write off
     * quantity in 'prxgt_wrhs_qty_sale'.
     *
     * registerSaleItemQty
     *
     * @param int $saleItemId
     * @param double $total
     * @param array $lotsData
     */
    public function exec($saleItemId, $total, $lotsData)
    {
        $rest = $total;
        foreach ($lotsData as $lot) {
            $stockItemId = $lot[AGet::A_STOCK_ITEM_ID];
            $lotId = $lot[AGet::A_LOT_ID];
            $qty = $lot[AGet::A_QTY];
            $qtyPk = [
                EQuantity::A_STOCK_ITEM_REF => $stockItemId,
                EQuantity::A_LOT_REF => $lotId
            ];
            if ($rest < $qty) {
                /* lot's $qty is greater than $total (or $rest) */
                $qtySaleData = [
                    EQtySale::A_SALE_ITEM_REF => $saleItemId,
                    EQtySale::A_LOT_REF => $lotId,
                    EQtySale::A_TOTAL => $rest
                ];
                $this->daoQtySale->create($qtySaleData);
                /* decrease lot's qty */
                $qtyRest = $qty - $rest;
                $qtyUpdateData = [EQuantity::A_TOTAL => $qtyRest];
                $this->daoQty->updateById($qtyPk, $qtyUpdateData);
                break;
            } else {
                /* lot's $qty is less or equal to $total (or $rest) */
                $qtySaleData = [
                    EQtySale::A_SALE_ITEM_REF => $saleItemId,
                    EQtySale::A_LOT_REF => $lotId,
                    EQtySale::A_TOTAL => $qty
                ];
                $this->daoQtySale->create($qtySaleData);
                /* delete zero quantity records from 'prxgt_wrhs_qty' */
                $this->daoQty->deleteById($qtyPk);
                /* decrease $rest of $total*/
                $rest -= $qty;
            }
            if ($rest <= 0) {
                break;
            }
        }
    }
}