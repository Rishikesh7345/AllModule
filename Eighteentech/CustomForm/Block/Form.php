<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @category Eighteentech
 * @package  Eighteentech_CustomForm
 */
namespace Eighteentech\CustomForm\Block;
 
use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Visitor custom form block
 */
class Form extends Template
{
    /**
     * @var \Eighteentech\CustomForm\Helper\Data
     */
    protected $helperData;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Eighteentech\CustomForm\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Eighteentech\CustomForm\Helper\Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }
 
    /**
     * Retrieve form action
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('customform/index/submit', ['_secure' => true]);
    }

    /**
     * Retrieve Heler data
    */
    public function isEnable()
    {
        return $this->helperData->isEnable();
    }

    /**
     * Retrieve Heler data
    */
    public function countDownDate()
    {
        return $this->helperData->countDownDate();
    }
}
