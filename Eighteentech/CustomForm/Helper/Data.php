<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @category Eighteentech
 * @package  Eighteentech_CustomForm
 */
namespace Eighteentech\CustomForm\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Check either module is enable or not.
 */
class Data extends AbstractHelper
{
    /**
     * @var Context $context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * Get value of admin field.
     */
    public function isEnable()
    {
        return $this->scopeConfig->getValue('eighteentech_customform/general/enable', ScopeInterface::SCOPE_STORE);
    }
}
