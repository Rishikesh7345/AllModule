<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @category Eighteentech
 * @package  Eighteentech_CustomForm
 */
namespace Eighteentech\CustomForm\Controller\Index;

class CaptchaFormPost extends \Magento\Framework\App\Action\Action
{
    /**
     * Retrieve url of custom form
     */
    public function execute()
    {
        $this->messageManager->addSuccess(__('Success!'));
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setUrl('/');
        return $redirect;
    }
}
