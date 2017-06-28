<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsPayment\Controller\Vendors\Type;

class Edit extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Vnecoms_VendorsCms::page';


    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $vendorSession;

    /**
     * @param \Vnecoms\Vendors\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Init actions.
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        parent::_initAction();
        $title = $this->_view->getPage()->getConfig()->getTitle();

        $this->_setActiveMenu('Vnecoms_VendorsCms::page')
            ->_addBreadcrumb(__('CMS'), __('CMS'))
            ->_addBreadcrumb(__('Manage Pages'), __('Manage Pages'));
    }

    /**
     * Edit CMS page.
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('page_id');
        $model = $this->_objectManager->create('Vnecoms\VendorsCms\Model\Page');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This page no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
            if ($model->getVendorId() !== $this->getVendor()->getId()) {
                $this->messageManager->addError(__('You can\'t access this Page.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $this->_coreRegistry->register('cms_page', $model);
        $this->_coreRegistry->register('current_cms_page', $model);

        // 5. Build edit form
        $this->_initAction();
        $this->_addBreadcrumb(
            $id ? __('Edit Page') : __('New Page'),
            $id ? __('Edit Page') : __('New Page')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Pages'));
        $this->_view->getPage()->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Page'));

        return $resultPage;
    }

    public function getVendor()
    {
        return ($this->vendorSession != null) ? $this->vendorSession->getVendor() : \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Vnecoms\Vendors\Model\Session')->getVendor();
    }
}
