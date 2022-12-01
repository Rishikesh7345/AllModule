<?php
/**
 * Class Index
 *
 * PHP version 7 & 8
 *
 * @category Sparsh
 * @package  Sparsh_ProductInquiry
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\ProductInquiry\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Store\Model\ScopeInterface;

/**
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Index extends Action
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $_dataPersistor;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Sparsh\ProductInquiry\Model\ProductInquiryFactory $inquiryFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Sparsh\ProductInquiry\Model\ProductInquiryFactory $inquiryFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->_inquiryFactory = $inquiryFactory;
        $this->_dataPersistor = $dataPersistor;
        $this->_inlineTranslation = $inlineTranslation;

        return parent::__construct($context);
    }

    /**
     * Send mail to customer & admin for the product inquiry
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $message = "0::" . __('Something went wrong. Please try again later.');
            return $this->getResponse()->setBody($message);
        }

        $this->_inlineTranslation->suspend();
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($post);

            $error = false;

            if (!\Zend_Validate::is(trim((string)$post['sparsh_product_inquiry_name']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim((string)$post['sparsh_product_inquiry_description']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim((string)$post['sparsh_product_inquiry_email']), 'EmailAddress')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim((string)$post['sparsh_product_inquiry_sku']), 'NotEmpty')) {
                $error = true;
            }
            if ($error) {
                $this->_inlineTranslation->resume();
                $message = "0::" . __('We can\'t process your request right now.');

                $this->_dataPersistor->set('sparsh_product_inquiry', $post);
                return $this->getResponse()->setBody($message);
            }

            $inquiry = $this->_inquiryFactory->create();
            $inquiry->setName($post["sparsh_product_inquiry_name"]);
            $inquiry->setPhone($post["sparsh_product_inquiry_phone"]);
            $inquiry->setEmail($post["sparsh_product_inquiry_email"]);
            $inquiry->setDescription($post["sparsh_product_inquiry_description"]);
            $inquiry->setSku($post["sparsh_product_inquiry_sku"]);

            $inquiry->save();

            $this->_inlineTranslation->resume();

            $message = "1::" . __('Thanks for contacting us with your comments. We\'ll respond to you very soon.');

            $this->_dataPersistor->clear('sparsh_product_inquiry');
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $message = "0::" . __('We can\'t process your request right now.');

            $this->_dataPersistor->set('sparsh_product_inquiry', $post);
        }

        try {
            $store = $this->_storeManager->getStore()->getId();

            $getSenderEmail = "ident_" . $this->_scopeConfig->
                getValue('product_inquiry/general/sender', ScopeInterface::SCOPE_STORE);
            $toEmail = $this->_scopeConfig->
            getValue("trans_email/" . $getSenderEmail . "/email", ScopeInterface::SCOPE_STORE);
            $toName = $this->_scopeConfig->
            getValue("trans_email/" . $getSenderEmail . "/email", ScopeInterface::SCOPE_STORE);
            $emailTemplate = $this->_scopeConfig->
            getValue('product_inquiry/general/email_template', ScopeInterface::SCOPE_STORE);

            $sender = [
                'email' => $this->_escaper->escapeHtml($post["sparsh_product_inquiry_email"]),
                'name' => $this->_escaper->escapeHtml($post["sparsh_product_inquiry_name"])
            ];

            if ($emailTemplate && $toEmail && $toName) {
                $transport = $this->_transportBuilder->setTemplateIdentifier($emailTemplate)
                    ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
                    ->setTemplateVars(
                        [
                            'Name' => $post['sparsh_product_inquiry_name'],
                            'Phone' => $post['sparsh_product_inquiry_phone'],
                            'Email' => $post['sparsh_product_inquiry_email'],
                            'Description' => $post['sparsh_product_inquiry_description'],
                            'Sku' => $post['sparsh_product_inquiry_sku']
                        ]
                    )
                    ->setFrom($sender)
                    ->addTo($toEmail, $toName)
                    ->getTransport();

                $transport->sendMessage();
            }
        } catch (\Exception $e) {
            $this->_dataPersistor->set('sparsh_product_inquiry', $post);
        }

        return $this->getResponse()->setBody($message);
    }
}
