<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Store\Model\StoreManagerInterface;
use Praxigento\Core\Repo\Criteria\IAdapter as ICriteriaAdapter;
use Praxigento\Core\Ui\DataProvider\Base as BaseDataProvider;
use Praxigento\Warehouse\Repo\Agg\IWarehouse as IRepoAggWarehouse;

class Warehouse extends BaseDataProvider
{

    public function __construct(
        UrlInterface $url,
        ICriteriaAdapter $criteriaAdapter,
        IRepoAggWarehouse $repo,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        $name,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $url,
            $criteriaAdapter,
            $repo,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $name,
            $meta,
            $data
        );
    }
}