<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Setup;

class InstallData extends \Praxigento\Core\Setup\Data\Base {

    /**
     * InstallSchema constructor.
     */
    public function __construct() {
        parent::__construct('Praxigento\Warehouse\Lib\Setup\Data');
    }
}