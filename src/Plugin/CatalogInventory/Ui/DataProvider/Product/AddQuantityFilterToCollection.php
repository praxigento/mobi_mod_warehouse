<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Ui\DataProvider\Product;

use Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityFilterToCollection as Subject;

class AddQuantityFilterToCollection {

    /**
     * Disable original "Quantity" filter in the grid.
     *
     * @param Subject  $subject
     * @param \Closure $proceed
     */
    public function aroundAddField(Subject $subject, \Closure $proceed) {
        //   $proceed();
        return;
    }
}