<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Api\Data\Catalog;

/**
 * Additional attributes for Magento's "Catalog Product" model.
 */
interface Product
{

    /**
     * Additional Product attributes. They are used to get data from DB and put in
     * Catalog Product model in query builders and to get data from Catalog Product model in other classes
     * (plugins, observers, services, ...)
     */
    const A_PRICE_WRHS = 'prxgt_wrhs_price';
    const A_PRICE_WRHS_GROUP = 'prxgt_wrhs_price_group';
}