<?php
/**
 * Fake implementation for the interface. We don't use this functionality in Santegra project.
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2021
 */

namespace Praxigento\Warehouse\Plugin\Magento\InventorySourceSelectionApi\Api;

class SourceSelectionService
    implements \Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface {

    private \Magento\InventorySourceSelection\Model\Result\SourceSelectionResultFactory $factory;

    public function __construct(
        \Magento\InventorySourceSelection\Model\Result\SourceSelectionResultFactory $factory
    ) {
        $this->factory = $factory;
    }


    public function execute(
        \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest,
        string $algorithmCode
    ): \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface {
        return $this->factory->create();
    }
}
