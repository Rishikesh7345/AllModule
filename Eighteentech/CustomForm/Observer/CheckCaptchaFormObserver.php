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
namespace Eighteentech\CustomForm\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckCaptchaFormObserver implements ObserverInterface
{
    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlManager;
    /**
     * @var \Magento\Captcha\Observer\CaptchaStringResolver
     */
    protected $captchaStringResolver;
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @param \Magento\Captcha\Helper\Data $helper
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Framework\UrlInterface $urlManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver
     */
    public function __construct(
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\UrlInterface $urlManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver
    ) {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->_session = $session;
        $this->_urlManager = $urlManager;
        $this->redirect = $redirect;
        $this->captchaStringResolver = $captchaStringResolver;
    }
       /**
        * Validate captcha on custom form.
        *
        * @param \Magento\Framework\Event\Observer $observer
        * @return $this
        */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // $formId = 'captcha_form_1'; // this form ID should matched the one defined in the layout xml
        // $captchaModel = $this->_helper->getCaptcha($formId);

        // $controller = $observer->getControllerAction();
        // if (!$captchaModel->isCorrect($this->captchaStringResolver->resolve($controller->getRequest(), $formId))) {
        //     $this->messageManager->addError(__('Incorrect CAPTCHA'));
        //     $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
        //     $this->_session->setCustomerFormData($controller->getRequest()->getPostValue());
        //     $url = $this->_urlManager->getUrl('cms/index/index', ['_nosecret' => true]);
        //     $controller->getResponse()->setRedirect($this->redirect->error($url));
        // }

        // return $this;
    }
}
