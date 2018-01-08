<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Lib\Test\Story01;

use Magento\CatalogInventory\Api\Data\StockInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Api\StockRepositoryInterface;
use Praxigento\Warehouse\Repo\Entity\Data\Lot;
use Praxigento\Warehouse\Repo\Entity\Data\Quantity;
use Praxigento\Warehouse\Repo\Entity\Data\Stock\Item as EntityStockItem;
use Praxigento\Warehouse\Repo\Entity\Data\Warehouse;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Main_IntegrationTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    private $_repoGeneric;
    /** @var  \Magento\CatalogInventory\Api\StockRepositoryInterface */
    private $_repoMageStock;
    /** @var  \Magento\CatalogInventory\Api\StockItemRepositoryInterface */
    private $_repoMageStockItem;
    /** @var  \Praxigento\Warehouse\Repo\Entity\Stock\Item */
    private $_repoStockItem;
    /** @var  \Praxigento\Core\Api\Helper\Date */
    private $_toolDate;

    public function __construct()
    {
        parent::__construct();
        $this->_repoGeneric = $this->_manObj->get(\Praxigento\Core\App\Repo\IGeneric::class);
        $this->_toolDate = $this->_manObj->get(\Praxigento\Core\Api\Helper\Date::class);
        $this->_repoMageStock = $this->_manObj->get(StockRepositoryInterface::class);
        $this->_repoMageStockItem = $this->_manObj->get(StockItemRepositoryInterface::class);
        $this->_repoStockItem = $this->_manObj->get(\Praxigento\Warehouse\Repo\Entity\Stock\Item::class);
    }

    /**
     * @param string $code
     * @param datetime $expDate
     *
     * @return int ID of the new entity
     */
    private function _createLot($code, $expDate)
    {
        $tbl = Lot::ENTITY_NAME;
        $bind = [
            Lot::ATTR_CODE => $code,
            Lot::ATTR_EXP_DATE => $expDate
        ];
        $result = $this->_repoGeneric->addEntity($tbl, $bind);
        return $result;
    }

    private function _createMageCategory($name)
    {
        /**
         * Initialize factories using Object Manager.
         */
        /** @var  $categoryFactory \Magento\Catalog\Api\CategoryRepositoryInterface */
        $categoryFactory = $this->_manObj->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class);
        /** @var  $category \Magento\Catalog\Api\Data\CategoryInterface */
        $category = $this->_manObj->create(\Magento\Catalog\Api\Data\CategoryInterface::class);
        $category->setName($name);
        $category->setIsActive(true);
        $saved = $categoryFactory->save($category);
        $result = $saved->getId();
        return $result;
    }

    /**
     * @param int $catId category ID
     * @param string $sku
     *
     * @return int ID of the new entity
     */
    private function _createMageProduct($catId, $sku)
    {
        /**
         * Initialize factories using Object Manager.
         */
        /** @var  $entityTypeFactory \Magento\Eav\Model\Entity\TypeFactory */
        $entityTypeFactory = $this->_manObj->get(\Magento\Eav\Model\Entity\TypeFactory::class);
        /** @var  $attrSetFactory \Magento\Eav\Model\Entity\Attribute\SetFactory */
        $attrSetFactory = $this->_manObj->get(\Magento\Eav\Model\Entity\Attribute\SetFactory::class);
        /** @var  $catProdLinkFactory \Magento\Catalog\Model\CategoryLinkRepository */
        $catProdLinkFactory = $this->_manObj->get(\Magento\Catalog\Model\CategoryLinkRepository::class);
        /** @var  $productFactory \Magento\Catalog\Api\ProductRepositoryInterface */
        $productFactory = $this->_manObj->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        /**
         * Retrieve entity type ID & attribute set ID.
         */
        /** @var  $entityType \Magento\Eav\Model\Entity\Type */
        $entityType = $entityTypeFactory
            ->create()
            ->loadByCode(\Magento\Catalog\Model\Product::ENTITY);
        $entityTypeId = $entityType->getId();
        $attrSet = $attrSetFactory
            ->create()
            ->load($entityTypeId, \Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID);
        $attrSetId = $attrSet->getId();
        /**
         * Create simple product.
         */
        /** @var  $product \Magento\Catalog\Api\Data\ProductInterface */
        $product = $this->_manObj->create(\Magento\Catalog\Api\Data\ProductInterface::class);
        $product->setSku($sku);
        $product->setName('Product ' . $sku);
        $product->setPrice(12.34);
        $product->setAttributeSetId($attrSetId);
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $saved = $productFactory->save($product);
        /* link product with category */
        /** @var  $catProdLink \Magento\Catalog\Api\Data\CategoryProductLinkInterface */
        $catProdLink = $this->_manObj->create(\Magento\Catalog\Api\Data\CategoryProductLinkInterface::class);
        $catProdLink->setCategoryId($catId);
        $catProdLink->setSku($sku);
        $catProdLink->setPosition(1);
        $catProdLinkFactory->save($catProdLink);
        /* return product ID */
        $result = $saved->getId();
        return $result;
    }

    /**
     * @param string $name Stock name.
     *
     * @return StockInterface
     */
    private function _createMageStock($name)
    {
        /** @var  $stock \Magento\CatalogInventory\Api\Data\StockInterface */
        $stock = $this->_manObj->create(StockInterface::class);
        $stock->setStockName($name);
        $saved = $this->_repoMageStock->save($stock);
        $result = $this->_repoMageStock->get($saved->getStockId());
        return $result;
    }

    private function _createMageStockItem($stockId, $prodId)
    {
        /* check if stock item already exist */
        /** @var  $criteria \Magento\CatalogInventory\Api\StockItemCriteriaInterface */
        $criteria = $this->_manObj->create(\Magento\CatalogInventory\Api\StockItemCriteriaInterface::class);
        $criteria->addFilter('byStock', StockItemInterface::STOCK_ID, $stockId);
        $criteria->addFilter('byProduct', StockItemInterface::PRODUCT_ID, $prodId);
        $list = $this->_repoMageStockItem->getList($criteria);
        $items = $list->getItems();
        /** @var  $stockItem StockItemInterface */
        if (count($items)) {
            $stockItem = reset($items);
            $result = $stockItem->getItemId();
        } else {
            $stockItem = $this->_manObj->create(StockItemInterface::class);
            $stockItem->setStockId($stockId);
            $stockItem->setProductId($prodId);
            $saved = $this->_repoMageStockItem->save($stockItem);
            $result = $saved->getItemId();
        }
        return $result;
    }

    private function _createQty($stockItemId, $lotId, $total)
    {
        $tbl = Quantity::ENTITY_NAME;
        $bind = [
            Quantity::ATTR_STOCK_ITEM_REF => $stockItemId,
            Quantity::ATTR_LOT_REF => $lotId,
            Quantity::ATTR_TOTAL => $total
        ];
        $result = $this->_repoGeneric->addEntity($tbl, $bind);
        /* update qty of the stock item */
        $stockItem = $this->_repoMageStockItem->get($stockItemId);
        $qty = $stockItem->getQty();
        $qty += $total;
        $stockItem->setQty($qty);
        $stockItem->setIsInStock(true);
        $this->_repoMageStockItem->save($stockItem);
        return $result;
    }

    private function _createStockItem($mageStockItemRef, $price)
    {
        $this->_repoStockItem->create([
            EntityStockItem::ATTR_STOCK_ITEM_REF => $mageStockItemRef,
            EntityStockItem::ATTR_PRICE => $price
        ]);
    }

    /**
     * @param null $code
     * @param null $note
     *
     * @return int ID of the new entity
     */
    private function _createWarehouse($code, $note)
    {
        /* create stock */
        $stock = $this->_createMageStock($code);
        $result = $stock->getStockId();
        /* ... then create warehouse itself */
        $tbl = Warehouse::ENTITY_NAME;
        $bind = [
            Warehouse::ATTR_STOCK_REF => $result,
            Warehouse::ATTR_CODE => $code,
            Warehouse::ATTR_NOTE => $note
        ];
        $this->_repoGeneric->addEntity($tbl, $bind);
        return $result;
    }

    public function test_main()
    {
        $this->_logger->debug('Story01 in Warehouse Integration tests is started.');
        $this->_conn->beginTransaction();
        try {
            /* create Magento customers, category & product */
            $this->_createMageCustomers();
            $catId = $this->_createMageCategory('All Products');
            $prodId = $this->_createMageProduct($catId, 'sku001');
            /* create 2 stocks (warehouses)*/
            $stockId01 = $this->_createWarehouse('wrhs01', 'First warehouse');
            $stockId02 = $this->_createWarehouse('wrhs02', 'Second warehouse');
            /* create stock items */
            $stockItemIdDef = $this->_createMageStockItem(1, $prodId);
            $stockItemId01 = $this->_createMageStockItem($stockId01, $prodId);
            $stockItemId02 = $this->_createMageStockItem($stockId02, $prodId);
            /* create warehouse stock items (for prices) */
            $this->_createStockItem($stockItemIdDef, '100.32');
            $this->_createStockItem($stockItemId01, '100.43');
            $this->_createStockItem($stockItemId02, '100.54');
            /* create 2 lots */
            $expDate = $this->_toolDate->getMageNowForDb();
            $lotId01 = $this->_createLot('lot01', $expDate);
            $lotId02 = $this->_createLot('lot02', $expDate);
            /* add qtys to products */
            $this->_createQty($stockItemIdDef, $lotId01, 100);
            $this->_createQty($stockItemIdDef, $lotId02, 200);
            $this->_createQty($stockItemId01, $lotId01, 10);
            $this->_createQty($stockItemId01, $lotId02, 20);
            $this->_createQty($stockItemId02, $lotId01, 30);
            $this->_createQty($stockItemId02, $lotId02, 40);
        } finally {
//            $this->_conn->commit();
            $this->_conn->rollBack();
        }
        $this->_logger->debug('Story01 in Warehouse Integration tests is completed, all transactions are rolled back.');
    }
}