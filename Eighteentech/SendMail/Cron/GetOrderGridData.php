<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @category Eighteentech
 * @package  Eighteentech_SendMail
 *
 */
namespace Eighteentech\SendMail\Cron;

use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Psr\Log\LoggerInterface;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Area;
use Eighteentech\SendMail\Helper\Helper;
class GetOrderGridData
{
    /**
     * @var MailInterface
     */
    private $mail;

    /** 
     * @var LoggerInterface
     */
    private $logger;

    /** 
     * @var Array
     */
    protected $entityIdList=[];

    /** 
     * @var Array
     */
    protected $entityIdListWithErpInvoiceId=[];  
    
    /** 
     * @var CollectionFactory 
     **/
    protected $_orderCollectionFactory;
    
    /** 
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection 
     **/
    protected $orders;
    
    protected $storeManager;
    protected $inlineTranslation;
    protected $helper;
    protected $resultRedirectFactory;
    /**
     * Construct
     * 
     * @param LoggerInterface $logger
     * @param CollectionFactory $orderCollectionFactory
     */
    public function __construct(        
        LoggerInterface $logger,
        CollectionFactory $orderCollectionFactory,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        Helper $helper,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
       
    ) {
        $this->logger = $logger;
        $this->_orderCollectionFactory = $orderCollectionFactory; 
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->helper = $helper;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * Get Order data of number of day
     * 
     * @return Json
     */
    public function getAllOrderData()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/GetOrderGridData2345.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);        
            $logger->info('<br>===Start===='.date('Y-m-d h:i:s'));        

        if (!$this->orders) 
        {
            $currentDate = date("Y-m-d H:i:s"); // Y-m-d h:i:s
            $newDate = strtotime('-1090 MINUTE', strtotime($currentDate));
            $newDate = date('Y-m-d H:i:s', $newDate);

            $this->orders = $this->_orderCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status','Complete')
            ->addFieldToFilter('updated_at', ['gteq' => $newDate])
            ->addFieldToFilter('updated_at', ['lteq' => $currentDate]);
       
        
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/crontabSendmail.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);        
        
            $_orders = $this->orders;        
            
            $logger->info('<br>===Start===='.date('Y-m-d h:i:s'));
            $logger->info('===count=='.count($_orders)); 
            // $logger->info('===Starttest===='.print_r($_orders));
            if ($_orders && count($_orders)) {                
                foreach ($_orders as $_order) {  

                    $order = $this->_orders->load($_order->getId());
                    $order->setData('email_status', 1);
                    $order->save();

                    $logger->info('===getId===='.$_order->setEmailStatus(1));                  

                 

                    // this is an example and you can change template id,fromEmail,toEmail,etc as per your need.
                    $templateId = 'eighteentech_sendMail_email_template'; // template id
                    $fromEmail = 'rishikesh@18thdigitech.net';  // sender Email id
                    $fromName = 'Admin';             // sender Name
                    $toEmail = 'rishikesh@18thdigitech.net'; // receiver email id
                    // $this->encryptData();



                    // $logger->info('===getId===='.print_r($this->encryptData()));
                    $orderId = $_order->getId();
                    $id = $this->helper->encryptString($orderId);
                    $encryptedId = $this->helper->encodeUrl($id);
                    $redirectionUrl = "";
                    $baseUrl = $this->storeManager->getStore()->getBaseUrl();
                    $redirectionUrl ="orderdata/index/getParams/id/".$encryptedId;
                    $logger->info('===getId===='.$id);

                    // $logger->info('===encryptedId===='.$encryptedId);
                    // $logger->info('===baseUrl===='.$baseUrl);

                    // $logger->info('===redirectionUrl===='.$redirectionUrl);



                    /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                    // $resultRedirect = $this->resultRedirectFactory->create();
                    // $resultRedirect->setUrl($redirectionUrl);

                    // $logger->info('===gettype===='.gettype($resultRedirect));
                    // return $resultRedirect;
















                    $a = 'yess';
                    try {
                        if($a == 'yes'){
                            $logger->info('===getId====Hello');
                        // template variables pass here
                        // $url = $this->storeManager->getStore()->getBaseUrl().'?'.$_order->getId();
                        $templateVars = [
                            'orderId' => $orderId,
                            'url' => $redirectionUrl
                        ];

                        $storeId = $this->storeManager->getStore()->getId();

                        $from = ['email' => $fromEmail, 'name' => $fromName];
                        $this->inlineTranslation->suspend();

                        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                        $templateOptions = [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $storeId
                        ];
                        $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                            ->setTemplateOptions($templateOptions)
                            ->setTemplateVars($templateVars)
                            ->setFrom($from)
                            ->addTo($toEmail)
                            ->getTransport();
                        $transport->sendMessage();
                        $this->inlineTranslation->resume();
                        $logger->info('===Successfully===='. "Successfully");
                    }
                    } catch (\Exception $e) {
                        $logger->info($e->getMessage());
                    }
                }
            }
            else{
                $logger->info('===Start===='. "You have no Orders");
            }  
        }           
    }
    public function encryptData()
    {
        $userId = "10";
        $id = $this->helper->encryptString($userId);
        $encryptedId = $this->helper->encodeUrl($id);
        $redirectionUrl = "";
        $baseUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $redirectionUrl = $baseUrl."demo/index/getParams/id/".$encryptedId;
        
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($redirectionUrl);
        return $resultRedirect;
    }         
   
}  
