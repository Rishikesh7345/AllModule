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
 
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Eighteentech\CustomForm\Model\CustomForm;
 
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var CustomForm $categoryModel
     */
    protected $_categoryModel;
    
    /**
     * @var Filter
     */
    protected $filter;
 
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
 
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param CustomForm $categoryModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        CustomForm $categoryModel
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryModel = $categoryModel;
    }

    /**
     * Edi action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        //$resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        //return $resultPage;
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->_categoryModel;
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            //echo '<pre>';print_r($rowData->debug());exit;
            $rowTitle = $rowData->getName();
            if (!$rowData->getId()) {
                $this->messageManager->addError(__('custom form data no longer exist.'));
                $this->_redirect('eighteentech_customform/customform/');
                return;
            }
        }
 
        $this->_coreRegistry->register('eighteentech_customform_view', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('View Custom Form ').$rowTitle : __('Add Row Data');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
