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

class GetOrderGridData
{
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
    
    /**
     * Construct
     * 
     * @param LoggerInterface $logger
     * @param CollectionFactory $orderCollectionFactory
     */
    public function __construct(        
        LoggerInterface $logger,
        CollectionFactory $orderCollectionFactory,
        TransportBuilder $transportBuilder
       
    ) {
        $this->logger = $logger;
        $this->_orderCollectionFactory = $orderCollectionFactory; 
        $this->transportBuilder = $transportBuilder;
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
        //     $logger->info('===count=='."hey");
        // $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        // try{


            
            
        //     $logger->info('<br>===Start===='.date('Y-m-d h:i:s'));
        //     $logger->info('===count=='."Hello");


        //     $post = $this->getRequest()->getPostValue();
        //     $senderEmail = "rishikesh@18thdigitech.net";
        //     $senderName = "Rishikesh kumar";
        //     $recipientEmail = "rishikesh@18thdigitech.net";

        //     $identifier = 1;  // Enter your email template identifier here

        //     $requestData = array();

        //     // if($post['fname']){
        //         $requestData['fname'] = "Rishieksh";
        //     // }
        //     // if($post['address']){
        //     //     $requestData['address'] = $post['address'];
        //     // }
        //     // if($post['city']){
        //     //     $requestData['city'] = $post['city'];
        //     // }
        //     // if($post['state']){
        //     //     $requestData['state'] = $post['state'];
        //     // }
        //     // You can add more data as given above

        //     $postObject = new \Magento\Framework\DataObject();
        //     $postObject->setData($requestData);
        //     $logger->info('<br>===Start===='.date('Y-m-d h:i:s'));
        //     $logger->info('===count=='.print_r($requestData));
        //     $transport = $this->transportBuilder
        //         ->setTemplateIdentifier($identifier)
        //         ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
        //         ->setTemplateVars(['data' => $postObject])
        //         ->setFrom(['name' => $senderName,'email' => $senderEmail])
        //         ->addTo([$recipientEmail])
        //         ->getTransport();
        //     $transport->sendMessage();

        //     $this->messageManager->addSuccess(__('Email has been sent successfully.'));
        //     $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        //     return $resultRedirect;
        // }catch(\Exception $e){
        //     $this->messageManager->addError(__('Something went wrong. Please try again later.'));
        //     $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        //     $logger->info('<br>===Start===='.date('Y-m-d h:i:s'));
        //     $logger->info('===count=='."hey");
        //     return $resultRedirect;
        // }

        // die;

         if (!$this->orders) 
        {
            $currentDate = date("Y-m-d H:i:s"); // Y-m-d h:i:s
            $newDate = strtotime('-90 MINUTE', strtotime($currentDate));
            $newDate = date('Y-m-d H:i:s', $newDate);       

            // $templateId = 'my_custom_email_template'; // template id
            // $fromEmail = 'rishikesh@18thdigitech.net';  // sender Email id
            // $fromName = 'Admin'; // sender Name
            // $toEmail = 'rishikesh@18thdigitech.net'; // receiver email id

            $this->orders = $this->_orderCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status','Complete')
            ->addFieldToFilter('updated_at', ['gteq' => $newDate])
            ->addFieldToFilter('updated_at', ['lteq' => $currentDate]);
        }
        
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/GetOrderGridData2345.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);        
      
        $_orders = $this->orders;        
        
        $logger->info('<br>===Start===='.date('Y-m-d h:i:s'));
        $logger->info('===count=='.count($_orders));

        if ($_orders && count($_orders)) {
            $logger->info('===Start===='.date('Y-m-d h:i:s'));

            // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            // $quoteId = 10;
            // $customColumnValue = 1; 
            // $quote = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\CollectionFactory')->load($quoteId);
            // $quote->setCustomColumn($customColumnValue);
            // $quote->save();


            foreach ($_orders as $_order) {
                $label = $_order->getStatusLabel();
                $logger->info('===Start===='.$_order->getEntityId());
                $logger->info('===getEmailStatus===='.$_order->getEmailStatus());
                $sentMail = 'yes';
                if($sentMail = 'yes'){                   
                    $logger->info('===Starttest===='.$_order->getEmailStatus());
                }          
            }
        }
        else{
            $logger->info('===Start===='. "You have no Orders");
        }        
        die;       
    }     
}  
