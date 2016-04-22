<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Praxigento\Warehouse\Model\ResourceModel\Warehouse\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends AbstractDb implements SearchResultInterface
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable,
        $resourceModel
    ) {
//        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        /* should we use parent constructor? */
        1 + 1;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        /* leave this method */
        1 + 1; // TODO: Implement addFilter() method.
    }

    public function addFilter($field, $value = null, $type = 'and')
    {/* leave this method */
        1 + 1; // TODO: Implement addFilter() method.
    }

    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {/* leave this method */
        1 + 1; // TODO: Implement addOrder() method.
    }

    public function getAggregations()
    {
        1 + 1; // TODO: Implement setLimit() method.
    }

    public function getItems()
    {/* leave this method */
        $result = [];
        /** @var \Magento\Framework\View\Element\UiComponent\DataProvider\Document $result */
        $item = ObjectManager::getInstance()->create(\Magento\Framework\View\Element\UiComponent\DataProvider\Document::class);
        $item->setCustomAttribute('customer_id', 456);
        $result[] = $item;
        $item = ObjectManager::getInstance()->create(\Magento\Framework\View\Element\UiComponent\DataProvider\Document::class);
        $item->setCustomAttribute('customer_id', 457);
        $result[] = $item;
        $item = ObjectManager::getInstance()->create(\Magento\Framework\View\Element\UiComponent\DataProvider\Document::class);
        $item->setCustomAttribute('customer_id', 458);
        $result[] = $item;
        $item = ObjectManager::getInstance()->create(\Magento\Framework\View\Element\UiComponent\DataProvider\Document::class);
        $item->setCustomAttribute('customer_id', 459);
        $result[] = $item;
        return $result;
    }

    public function getResource()
    {
        1 + 1; // TODO: Implement setLimit() method.
    }

    public function getSearchCriteria()
    {
        1 + 1; // TODO: Implement setLimit() method.
    }

    public function getTotalCount()
    {
        return 1;
    }

    public function loadWithFilter($printQuery = false, $logQuery = false)
    {
//        $this->_beforeLoad();
//        $this->_renderFilters()->_renderOrders()->_renderLimit();
//        $this->printLogQuery($printQuery, $logQuery);
//        $data = $this->getData();
//        $this->resetData();
//        if (is_array($data)) {
//            foreach ($data as $row) {
//                $item = $this->getNewEmptyItem();
//                if ($this->getIdFieldName()) {
//                    $item->setIdFieldName($this->getIdFieldName());
//                }
//                $item->addData($row);
//                $this->beforeAddLoadedItem($item);
//                $this->addItem($item);
//            }
//        }
//        $this->_setIsLoaded();
//        $this->_afterLoad();
        return $this;
    }

    public function setAggregations($aggregations)
    {
        1 + 1; // TODO: Implement setLimit() method.
    }

    /**
     * Set current page
     *
     * @param   int $page
     * @return $this
     */
    public function setCurPage($page)
    {/* leave this method */
        $this->_curPage = $page;
        return $this;
    }

    public function setItems(array $items = null)
    {
        1 + 1; // TODO: Implement setLimit() method.
    }

    public function setLimit($offset, $size)
    {
        1 + 1; // TODO: Implement setLimit() method.
    }

    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {/* leave this method */
        1 + 1;
    }

    public function setPageSize($size)
    {/* leave this method */
        $this->_pageSize = $size;
        return $this;
    }

    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        1 + 1; // TODO: Implement setLimit() method.
    }

    public function setTotalCount($totalCount)
    {
        1 + 1; // TODO: Implement setLimit() method.
    }
}
