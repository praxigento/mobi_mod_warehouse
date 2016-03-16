<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api;

/**
 * Service to operate with 'lot' entity in MOBI applications.
 * @api
 */
interface LotInterface {

    /**
     * @param int $id
     *
     * @return null
     */
    public function read($id = null);

}