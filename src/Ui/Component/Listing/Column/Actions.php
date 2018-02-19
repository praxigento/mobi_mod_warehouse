<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Ui\Component\Listing\Column;

/**
 * Add
 */
class Actions
    extends \Magento\Ui\Component\Listing\Columns\Column
{
    const A_PRODUCT_ID = \Praxigento\Warehouse\Ui\DataProvider\Grid\Remnants\QueryBuilder::A_PRODUCT_ID;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;


    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $name = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$name]['edit'] = [
                    'label' => __('Edit'),
                    'id' => $item[self::A_PRODUCT_ID]
                ];
            }
        }

        return $dataSource;
    }
}
