<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @category Eighteentech
 * @package  Eighteentech_CustomForm
 *
 */
namespace Eighteentech\CustomForm\Controller\Adminhtml\CustomForm;

use Magento\Backend\App\Action\Context;
use Eighteentech\CustomForm\Model\CustomForm;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var CustomForm $_categoryModel
     */
    protected $_categoryModel;

    /**
     * @param Context $context
     * @param CustomForm $categoryModel
     */
    public function __construct(Context $context, CustomForm $categoryModel)
    {
        $this->_categoryModel = $categoryModel;
        parent::__construct($context);
    }
   
    /**
     * Delete Action
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
                $image = $this->_categoryModel->load($id);
                $image->delete();
                $this->messageManager->addSuccess(
                    __('Delete successfully !')
                );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
