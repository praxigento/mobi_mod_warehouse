<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\QtyDistributor\Register\A\Sale\Item;

use Praxigento\Warehouse\Repo\Entity\Data\Quantity as EQuantity;
use Praxigento\Warehouse\Repo\Entity\Data\Quantity\Sale as EQtySale;
use Praxigento\Warehouse\Repo\Query\Lots\By\Product\Id\Get as AGet;

/**
 * Internal service for group of classes (\Praxigento\Warehouse\Service\QtyDistributor\Register\...).
 */
class Qty
{
    /** @var \Praxigento\Warehouse\Repo\Entity\Quantity */
    private $repoQty;
    /** @var \Praxigento\Warehouse\Repo\Entity\Quantity\Sale */
    private $repoQtySale;

    public function __construct(
        \Praxigento\Warehouse\Repo\Entity\Quantity $repoQty,
        \Praxigento\Warehouse\Repo\Entity\Quantity\Sale $repoQtySale
    )
    {
        $this->repoQty = $repoQty;
        $this->repoQtySale = $repoQtySale;
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
                EQuantity::ATTR_STOCK_ITEM_REF => $stockItemId,
                EQuantity::ATTR_LOT_REF => $lotId
            ];
            if ($rest < $qty) {
                /* lot's $qty is greater than $total (or $rest) */
                $qtySaleData = [
                    EQtySale::ATTR_SALE_ITEM_REF => $saleItemId,
                    EQtySale::ATTR_LOT_REF => $lotId,
                    EQtySale::ATTR_TOTAL => $rest
                ];
                $this->repoQtySale->create($qtySaleData);
                /* decrease lot's qty */
                $qtyRest = $qty - $rest;
                $qtyUpdateData = [EQuantity::ATTR_TOTAL => $qtyRest];
                $this->repoQty->updateById($qtyPk, $qtyUpdateData);
                break;
            } else {
                /* lot's $qty is less or equal to $total (or $rest) */
                $qtySaleData = [
                    EQtySale::ATTR_SALE_ITEM_REF => $saleItemId,
                    EQtySale::ATTR_LOT_REF => $lotId,
                    EQtySale::ATTR_TOTAL => $qty
                ];
                $this->repoQtySale->create($qtySaleData);
                /* delete zero quantity records from 'prxgt_wrhs_qty' */
                $this->repoQty->deleteById($qtyPk);
                /* decrease $rest of $total*/
                $rest -= $qty;
            }
            if ($rest <= 0) {
                break;
            }
        }
    }
}