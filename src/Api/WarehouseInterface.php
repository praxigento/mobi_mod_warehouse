<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api;

use Praxigento\Warehouse\Service\Warehouse\Request;
use Praxigento\Warehouse\Service\Warehouse\Response;

/**
 * Service to operate with 'warehouse' entity in MOBI applications.
 * @api
 */
interface WarehouseInterface
{
    /**
     * Create new warehouse instance.
     *
     * @param \Praxigento\Warehouse\Service\Warehouse\Request\ICreate $data
     *
     * @return \Praxigento\Warehouse\Service\Warehouse\Response\ICreate ID of the newly created instance.
     */
    public function create(Request\ICreate $data);


    /**
     * Get data for warehouse instance.
     *
     * @param \Praxigento\Warehouse\Service\Warehouse\Request\IGet $data Magento ID of the related stock.
     *
     * @return \Praxigento\Warehouse\Service\Warehouse\Response\IGet
     */
    public function get(Request\IGet $data);

}