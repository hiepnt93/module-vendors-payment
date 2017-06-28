<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsPayment\Controller\Vendors\Type;

class Delete extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Vnecoms_VendorsCms::page';

    protected $vendorSession;

    /**
     * Delete action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('page_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = '';
            try {
                // init model and delete
                $model = $this->_objectManager->create('Vnecoms\VendorsCms\Model\Page');
                $model->load($id);
                if($model->getVendorId() !== $this->getVendor()->getId()) {
                    $this->messageManager->addError(__('You can\'t delete this Page.'));
                    return $resultRedirect->setPath('*/*/');
                }
                $title = $model->getTitle();
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The page has been deleted.'));
                // go to grid
                $this->_eventManager->dispatch(
                    'vendor_cmspage_on_delete',
                    ['title' => $title, 'status' => 'success']
                );

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'vendor_cmspage_on_delete',
                    ['title' => $title, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['page_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a page to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor()
    {
        if ($this->vendorSession) {
            return $this->vendorSession->getVendor();
        } else {
            $this->vendorSession = $this->_objectManager->get('Vnecoms\Vendors\Model\Session');

            return $this->vendorSession->getVendor();
        }
    }
}
