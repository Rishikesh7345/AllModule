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
 
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Get layout output.
     */
    public function execute()
    {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
    }
}
