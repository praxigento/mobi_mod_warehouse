<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Store\Model\StoreManagerInterface;
use Praxigento\Warehouse\Repo\Entity\ILot as IRepoEntityLot;

class Lot extends DataProvider
{

    const JSON_ATTR_ITEMS = 'items';
    const JSON_ATTR_TOTAL_RECORDS = 'totalRecords';

    /**#@+
     * UI XML arguments and default values to configure this component.
     */
    const UICD_UPDATE_URL = 'mui/index/render';
    const UIC_CONFIG = 'config';
    const UIC_UPDATE_URL = 'update_url';
    /**#@-*/

    /** @var  IRepoEntityLot */
    protected $_repo;

    /**
     * Warehouse constructor.
     * @param UrlInterface $url
     * @param IRepoEntityLot $repo
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param FilterBuilder $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        UrlInterface $url,
        IRepoEntityLot $repo,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        $name,
        $primaryFieldName = 'entity_id',
        $requestFieldName = 'id',
        array $meta = [],
        array $data = []
    ) {
        /* add default Update URL */
        if (!isset($data[static::UIC_CONFIG][static::UIC_UPDATE_URL])) {
            $val = $url->getRouteUrl(static::UICD_UPDATE_URL);
            $data[static::UIC_CONFIG][static::UIC_UPDATE_URL] = $val;
        }
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        /* post construction setup */
        $this->_repo = $repo;
    }

    public function addField($field, $alias = null)
    {
        return parent::addField($field, $alias);
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return parent::addFilter($filter);
    }

    public function addOrder($field, $direction)
    {
        return parent::addOrder($field, $direction);
    }

    public function getData()
    {
        $criteria = $this->getSearchCriteria();
        $pageSize = $criteria->getPageSize();
        $pageIndx = $criteria->getCurrentPage();
        $where = null;
        $order = null;
        /** @var \Magento\Framework\DB\Select $queryTotal */
        $queryTotal = $this->_repo->getQueryToSelectCount();
        $total = $queryTotal->getConnection()->fetchOne($queryTotal);
        /** @var \Magento\Framework\DB\Select $query */
        $query = $this->_repo->getQueryToSelect();
        $query->limitPage($pageIndx, $pageSize);
        $data = $query->getConnection()->fetchAll($query);
        $result = [
            static::JSON_ATTR_TOTAL_RECORDS => $total,
            static::JSON_ATTR_ITEMS => $data
        ];
        return $result;
    }

    public function getFieldMetaInfo($fieldSetName, $fieldName)
    {
        return parent::getFieldMetaInfo($fieldSetName, $fieldName);
    }

    public function getFieldSetMetaInfo($fieldSetName)
    {
        return parent::getFieldSetMetaInfo($fieldSetName);
    }

    public function getFieldsMetaInfo($fieldSetName)
    {
        return parent::getFieldsMetaInfo($fieldSetName);
    }

    public function getMeta()
    {
        return parent::getMeta();
    }


    public function getPrimaryFieldName()
    {
        return parent::getPrimaryFieldName();
    }

    public function getRequestFieldName()
    {
        return parent::getRequestFieldName();
    }

    public function getSearchCriteria()
    {
        return parent::getSearchCriteria();
    }

    public function getSearchResult()
    {
        return parent::getSearchResult();
    }

    public function setConfigData($config)
    {
        return parent::setConfigData($config);
    }

    public function setLimit($offset, $size)
    {
        return parent::setLimit($offset, $size);
    }
}