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

class Index extends \Magento\Backend\App\Action
{
    /**
     * PageFactory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory = false;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Eighteentech_CustomForm::customform_manage');
        $resultPage->addBreadcrumb(__('Eighteentech'), __('Eighteentech'));
        $resultPage->addBreadcrumb(__('CustomForm'), __('CustomForm'));
        $resultPage->getConfig()->getTitle()->prepend(__('CustomForm'));
        return $resultPage;
    }
}
