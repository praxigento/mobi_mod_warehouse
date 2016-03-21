<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class EavCollectionAbstractLoadBefore implements ObserverInterface {
    const AS_TBL = 'pwq';
    const AS_FLD_QTY = 'qty';

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $obj = $observer->getCollection();
        if($observer->getCollection() instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection) {
            /** @var  $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            //            $collection = $observer->getCollection();
            //            $rsrc = $collection->getResource();
            //            $tblQuant = $rsrc->getTable(Quantity::ENTITY_NAME);
            //            $as = self::AS_TBL;
            //            $tbl = [ $as => $tblQuant ];
            //            $eid = Cfg::E_COMMON_A_ENTITY_ID;
            //            $on = Quantity::ATTR_PRODUCT_REF . '=' . $eid;
            //            $fields = [ self::AS_FLD_QTY => 'SUM(' . $as . '.' . Quantity::ATTR_TOTAL . ')' ];
            //            $collection->joinTable($tbl, $on, $fields, null, 'left');
            //            $collection->groupByAttribute($eid);
            // $sql = $collection->getSelectSql(true);
        }
        return;
    }
}