<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsPayment\Controller\Vendors\Type;

class Index extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Vnecoms_VendorsCms::page';

    /**
     * Index action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_initAction();
        $title = $this->_view->getPage()->getConfig()->getTitle();
        $this->_addBreadcrumb(__('CMS'), __('CMS'));
        $this->_addBreadcrumb(__('Manage Pages'), __('Manage Pages'));
        $title->prepend(__('Pages'));

        $dataPersistor = $this->_objectManager->get('Magento\Framework\App\Request\DataPersistorInterface');
        $dataPersistor->clear('vendor_cms_page');

        $this->_view->renderLayout();
    }
}
