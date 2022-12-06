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

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Eighteentech\CustomForm\Model\CustomFormFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Controller\Result\JsonFactory;
/**
 * Custom Form submit action.
 */
class Submit extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    protected $httpHeader;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Eighteentech\CustomForm\Model\CustomFormFactory $customFormFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\HTTP\Header $httpHeader
     * @param Magento\Framework\Controller\Result\JsonFactory;
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomFormFactory $customFormFactory,
        RemoteAddress $remoteAddress,
        Header $httpHeader,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customFormFactory = $customFormFactory;
        $this->remoteAddress = $remoteAddress;
        $this->httpHeader = $httpHeader;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Submit the form.
     */
    public function execute()
    {
        try {
            $userAgent = $this->httpHeader->getHttpUserAgent();
            $browser = $this->getBrowserName($userAgent);
            $ip = $this->remoteAddress->getRemoteAddress();
            $data = (array)$this->getRequest()->getPost();
            if ($data) {
                $model = $this->customFormFactory->create();
                $model->setData($data);
                $model->setData('ip', $ip);
                $model->setData('browser', $browser);
                $model->save();
                
                $resultJson = $this->resultJsonFactory->create();
                return $resultJson->setData(['json_data' => 'successfully','json_msg'=>1]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e, __("We can\'t submit your request, Please try again."));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        
        return $resultRedirect;
    }

    /**
     * Retrieve exact browser name.
     *
     * @param string $userAgent
     */
    public function getBrowserName($userAgent)
    {
        $t = strtolower($userAgent);
        $t = " " . $t;
        if (str_contains($t, 'opera') || str_contains($t, 'opr/')) {
            return 'Opera'            ;
        } elseif (str_contains($t, 'edge')) {
            return 'Edge'             ;
        } elseif (str_contains($t, 'chrome')) {
            return 'Chrome'           ;
        } elseif (str_contains($t, 'safari')) {
            return 'Safari'           ;
        } elseif (str_contains($t, 'firefox')) {
            return 'Firefox'          ;
        } elseif (str_contains($t, 'msie') || str_contains($t, 'trident/7')) {
            return 'Internet Explorer';
        }
        return 'Unkown';
    }
}
