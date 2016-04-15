<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api;

use Praxigento\Core\Data\Api\IHasId;
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
     * Read warehouse instance by ID.
     *
     * @param IHasId $data contains ID of the Warehouse instance.
     *
     * @return \Praxigento\Warehouse\Data\Api\WarehouseInterface
     */
    //public function get(IHasId $data);

}