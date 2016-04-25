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
use Praxigento\Core\Ui\DataProvider\Base as BaseDataProvider;
use Praxigento\Warehouse\Repo\Entity\ILot as IRepoEntityLot;

class Lot extends BaseDataProvider
{

    /**
     * @param UrlInterface $url
     * @param IRepoEntityLot $repo
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param FilterBuilder $name
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
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $url,
            $repo,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $name,
            'entity_id',
            'id',
            $meta,
            $data
        );
    }

}