<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api;

/**
 * Service to operate with 'warehouse' entity in MOBI applications.
 * @api
 */
interface WarehouseInterface {
    /**
     * Create new warehouse instance.
     *
     * @param \Praxigento\Warehouse\Api\Data\IWarehouseCreate $data
     *
     * @return int ID of the newly created instance.
     */
    public function create(Data\IWarehouseCreate $data);

    /**
     * Delete warehouse instance by ID.
     *
     * @param int $id ID of the Warehouse instance.
     *
     * @return boolean 'true' if instnace is deleted.
     */
    public function delete($id);

    /**
     * Read warehouse instance by ID.
     *
     * @param int $id ID of the Warehouse instance.
     *
     * @return \Praxigento\Warehouse\Api\Data\IWarehouseRead
     */
    public function read($id);

    /**
     * Update warehouse instance by ID.
     *
     * @param int                                             $id ID of the warehouse instance to update.
     * @param \Praxigento\Warehouse\Api\Data\IWarehouseUpdate $data data to update.
     *
     * @return boolean 'true' if instnace is updated.
     */
    public function update($id, Data\IWarehouseUpdate $data);

}