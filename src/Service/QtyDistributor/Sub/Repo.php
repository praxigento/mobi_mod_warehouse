<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\QtyDistributor\Sub;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Data\Entity\Lot;
use Praxigento\Warehouse\Data\Entity\Quantity;
use Praxigento\Warehouse\Data\Entity\Quantity\Sale as QtySale;

class Repo
{
    /** @var  \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;
    /** @var \Praxigento\Warehouse\Repo\Entity\IQuantity */
    protected $_repoQty;
    /** @var \Praxigento\Warehouse\Repo\Entity\Quantity\ISale */
    protected $_repoQtySale;

    public function __construct(
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Praxigento\Core\Repo\IGeneric $repoGeneric,
        \Praxigento\Warehouse\Repo\Entity\IQuantity $repoQty,
        \Praxigento\Warehouse\Repo\Entity\Quantity\ISale $repoQtySale
    ) {
        $this->_manTrans = $manTrans;
        $this->_repoGeneric = $repoGeneric;
        $this->_repoQty = $repoQty;
        $this->_repoQtySale = $repoQtySale;
    }

    public function getLotsByProductId($prodId, $stockId)
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $conn */
        $conn = $this->_repoGeneric->getConnection();
        /* aliases and tables */
        $asStockItem = 'csi';
        $asQty = 'pwq';
        $asLot = 'pwl';
        $tblStockItem = [$asStockItem => $conn->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM)];
        $tblQty = [$asQty => $conn->getTableName(Quantity::ENTITY_NAME)];
        $tblLot = [$asLot => $conn->getTableName(Lot::ENTITY_NAME)];
        /* SELECT FROM cataloginventory_stock_item */
        $query = $conn->select();
        $cols = [Alias::AS_STOCK_ITEM_ID => Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID];
        $query->from($tblStockItem, $cols);
        /* LEFT JOIN prxgt_wrhs_qty pwq */
        $on = $asQty . '.' . Quantity::ATTR_STOCK_ITEM_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $cols = [Alias::AS_QTY => Quantity::ATTR_TOTAL];
        $query->joinLeft($tblQty, $on, $cols);
        // LEFT JOIN prxgt_wrhs_lot pwl
        $on = $asLot . '.' . Lot::ATTR_ID . '=' . $asQty . '.' . Quantity::ATTR_LOT_REF;
        $cols = [
            Alias::AS_LOT_ID => Lot::ATTR_ID,
            Alias::AS_LOT_CODE => Lot::ATTR_CODE,
            Alias::AS_LOT_EXP_DATE => Lot::ATTR_EXP_DATE
        ];
        $query->joinLeft($tblLot, $on, $cols);
        /* where */
        $where = $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . (int)$prodId;
        $where .= ' AND ' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '=' . (int)$stockId;
        $query->where($where);
        /* order by */
        $order = $asLot . '.' . Lot::ATTR_EXP_DATE . ' ASC';
        $query->order($order);
        /* fetch data */
        $result = $conn->fetchAll($query);
        return $result;
    }

    /**
     * Get lot(s) with the closest expiration date and write off quantity from this lot(s). Register write off
     * quantity in 'prxgt_wrhs_qty_sale'.
     *
     * @param int $saleItemId
     * @param double $total
     * @param array $lotsData
     */
    public function registerSaleItemQty($saleItemId, $total, $lotsData)
    {
        $rest = $total;
        $trans = $this->_manTrans->transactionBegin();
        try {
            foreach ($lotsData as $lot) {
                $stockItemId = $lot[Alias::AS_STOCK_ITEM_ID];
                $lotId = $lot[Alias::AS_LOT_ID];
                $qty = $lot[Alias::AS_QTY];
                $qtyPk = [
                    Quantity::ATTR_STOCK_ITEM_REF => $stockItemId,
                    Quantity::ATTR_LOT_REF => $lotId
                ];
                if ($rest < $qty) {
                    /* lot's $qty is greater than $total (or $rest) */
                    $qtySaleData = [
                        QtySale::ATTR_SALE_ITEM_REF => $saleItemId,
                        QtySale::ATTR_LOT_REF => $lotId,
                        QtySale::ATTR_TOTAL => $rest
                    ];
                    $this->_repoQtySale->create($qtySaleData);
                    /* decrease lot's qty */
                    $qtyRest = $qty - $rest;
                    $qtyUpdateData = [Quantity::ATTR_TOTAL => $qtyRest];
                    $this->_repoQty->updateById($qtyPk, $qtyUpdateData);
                    break;
                } else {
                    /* lot's $qty is less or equal to $total (or $rest) */
                    $qtySaleData = [
                        QtySale::ATTR_SALE_ITEM_REF => $saleItemId,
                        QtySale::ATTR_LOT_REF => $lotId,
                        QtySale::ATTR_TOTAL => $qty
                    ];
                    $this->_repoQtySale->create($qtySaleData);
                    /* delete zero quantity records from 'prxgt_wrhs_qty' */
                    $this->_repoQty->deleteById($qtyPk);
                    /* decrease $rest of $totol*/
                    $rest -= $qty;
                }
                if ($rest <= 0) {
                    break;
                }
            }
            $this->_manTrans->transactionCommit($trans);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->transactionClose($trans);
        }
    }
}