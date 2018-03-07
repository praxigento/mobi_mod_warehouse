<?php
/**
 * Join 'cataloginventory_stock_item' & 'prxgt_wrhs_qty' tables with grouping by product_id and add 'qty' as SUM of the all quantities.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Ui\DataProvider\Product;

use Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityFieldToCollection as Subject;

class AddQuantityFieldToCollection
{
    /** @var \Praxigento\Warehouse\Repo\Modifier\Product\Grid */
    protected $_queryModGrid;

    public function __construct(
        \Praxigento\Warehouse\Repo\Modifier\Product\Grid $queryModGrid
    ) {
        $this->_queryModGrid = $queryModGrid;
    }

    /**
     * @param Subject $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param $field
     * @param null $alias
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundAddField(
        Subject $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        $select = $collection->getSelect();
        $this->_queryModGrid->modifySelect($select);
        return;
    }
}