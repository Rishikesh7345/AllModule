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
namespace Eighteentech\CustomForm\Controller\Index;
 
class SignForm extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	/**
     * Get layout output.
     */
    public function execute()
    {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
    }
}
