<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsPayment\Block\Vendors\Type;

/**
 * Vendors cms pages grid.
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Vnecoms\VendorsPayment\Model\ResourceModel\Type\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Vnecoms\VendorsPayment\Model\Page
     */
    protected $_cmsPage;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context                          $context
     * @param \Magento\Backend\Helper\Data                                     $backendHelper
     * @param \Vnecoms\VendorsCms\Model\Page                                   $cmsPage
     * @param \Vnecoms\VendorsCms\Model\ResourceModel\Page\CollectionFactory   $collectionFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Vnecoms\VendorsCms\Model\Page $cmsPage,
        \Vnecoms\VendorsCms\Model\ResourceModel\Page\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_cmsPage = $cmsPage;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('cmsPageGrid');
        $this->setDefaultSort('identifier');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare collection.
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Vnecoms\VendorsCms\Model\ResourceModel\Page\Collection */
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns.
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('title', ['header' => __('Title'), 'index' => 'title']);

        $this->addColumn('identifier', ['header' => __('URL Key'), 'index' => 'identifier']);

        $this->addColumn(
            'page_layout',
            [
                'header' => __('Layout'),
                'index' => 'page_layout',
                'type' => 'options',
                'options' => $this->pageLayoutBuilder->getPageLayoutsConfig()->getOptions(),
            ]
        );

        /*
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                [
                    'header' => __('Store View'),
                    'index' => 'store_id',
                    'type' => 'store',
                    'store_all' => true,
                    'store_view' => true,
                    'sortable' => false,
                    'filter_condition_callback' => [$this, '_filterStoreCondition'],
                ]
            );
        }

        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => $this->_cmsPage->getAvailableStatuses(),
            ]
        );

        $this->addColumn(
            'creation_time',
            [
                'header' => __('Created'),
                'index' => 'creation_time',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date',
            ]
        );

        $this->addColumn(
            'update_time',
            [
                'header' => __('Modified'),
                'index' => 'update_time',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date',
            ]
        );

        $this->addColumn(
            'page_actions',
            [
                'header' => __('Action'),
                'sortable' => false,
                'filter' => false,
                'renderer' => 'Vnecoms\VendorsCms\Block\Vendors\Page\Grid\Renderer\Action',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * After load collection.
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * Filter store condition.
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Framework\DataObject      $column
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _filterStoreCondition($collection, \Magento\Framework\DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * Row click url.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['page_id' => $row->getId()]);
    }
}
