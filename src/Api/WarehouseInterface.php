<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api;

/**
 * CRUD service to operate with 'warehouse' entity in MOBI applications.
 * @api
 */
interface WarehouseInterface {
    /**
     * Create new warehouse instance.
     *
     * @param \Praxigento\Warehouse\Api\Data\IWarehouse $data
     *
     * @return int ID of the newly created instance.
     */
    public function create(Data\IWarehouse $data);

    /**
     * Delete warehouse instance by ID.
     *
     * @param int $id ID of the Warehouse instance.
     *
     * @return boolean 'true' if instance is deleted.
     */
    public function delete($id);

    /**
     * Read warehouse instance by ID.
     *
     * @param int $id ID of the Warehouse instance.
     *
     * @return \Praxigento\Warehouse\Api\Data\IWarehouse
     */
    public function read($id);

    /**
     * Update warehouse instance by ID.
     *
     * @param int                                       $id ID of the warehouse instance to update.
     * @param \Praxigento\Warehouse\Api\Data\IWarehouse $data data to update.
     *
     * @return boolean 'true' if instance is updated.
     */
    public function update($id, Data\IWarehouse $data);

}