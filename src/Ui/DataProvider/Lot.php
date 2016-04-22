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
        // parent::addField($field, $alias);
        $this->_fieldsToSelect[] = $field;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        1 + 1;
    }

    public function addOrder($field, $direction)
    {
        1 + 1;
    }

    public function getData()
    {
        $data = $this->_repo->get();
        $total = count($data);
        $result = [
            static::JSON_ATTR_TOTAL_RECORDS => $total,
            static::JSON_ATTR_ITEMS => $data
        ];
        return $result;
    }

    public function getFieldMetaInfo($fieldSetName, $fieldName)
    {
        1 + 1;
    }

    public function getFieldSetMetaInfo($fieldSetName)
    {
        1 + 1;
    }

    public function getFieldsMetaInfo($fieldSetName)
    {
        1 + 1;
    }

    public function getMeta()
    {
        1 + 1;
    }


    public function getPrimaryFieldName()
    {
        1 + 1;
    }

    public function getRequestFieldName()
    {
        1 + 1;
    }

    public function getSearchCriteria()
    {
        1 + 1;
    }

    public function getSearchResult()
    {
        1 + 1;
    }

    public function setConfigData($config)
    {
        parent::setConfigData($config);
    }

    public function setLimit($offset, $size)
    {
        1 + 1;
    }
}