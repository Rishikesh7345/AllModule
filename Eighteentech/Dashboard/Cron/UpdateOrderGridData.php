<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @category Eighteentech
 * @package  Eighteentech_Dashboard
 *
 */
namespace Eighteentech\Dashboard\Cron;


use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class UpdateOrderGridData
{
    /** 
     * @var LoggerInterface
     */
    private $logger;

    /** 
     * @var ResourceConnection
     */
    private $resourceConnection;

    /** 
     * @var Array
     */
    protected $entityIdList=[];

    /** 
     * @var Array
     */
    protected $entityIdListWithErpInvoiceId=[];  
    
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;
    /**
     * Construct
     * 
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state
    ) {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
    }

    /**
     * Get Order data of number of day
     * 
     * @return Json
     */
    public function getOrderData(){

        // print_r($_REQUEST); die;
        $tableName = $this->resourceConnection->getTableName('sales_order_grid');

        $connection = $this->resourceConnection->getConnection();
        $scopedate = "now() - interval 1 day";
      
        $select = $connection->select()
            ->from(
                ['c' => $tableName]           
            )->where("entity_id > ?",0);            
        $whereCondition = str_replace("'","", $connection->quoteInto("date(c.updated_at) > ?", $scopedate));      
        $select->where($whereCondition);
        $status = "complete";
        $select->where("status=?",$status);
        $records = $connection->fetchAll($select);

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/UpdateOrderGridData3.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);  
        $emailStatus = 1;
        foreach($records as $data){
            
            if($data['status'] == 'complete'){
                //$logger->info("====".$data['status']);
                $templateId = 'my_custom_email_template'; // template id
                $fromEmail = 'rishikesh@18thdigitech.net';  // sender Email id
                $fromName = 'Admin'; // sender Name
                $toEmail = 'rishikesh@18thdigitech.net'; // receiver email id     
                try {
                    
                    $templateVars = [
                        'msg' => 'test',
                        'msg1' => 'test1'
                    ];
                    $logger->info("====".print_r($templateVars,true));
                    $storeId = $this->storeManager->getStore()->getId();              
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
                    
                } catch (\Exception $e) {
                    $this->_logger->info($e->getMessage());
                    $logger->info('===Start===='.$e->getMessage());
                }
            }
        }              
        $logger->info('===Start===='.date('Y-m-d h:i:s'));
        $logger->info();
        $logger->info('===='.print_r($records,true));
      die;
        $jsonOrder = json_encode($records);       
        return $jsonOrder;
    }     
}  
