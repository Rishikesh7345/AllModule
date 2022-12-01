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
    
    /**
     * Construct
     * 
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection
    ) {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
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
        $scopedate = "now() - interval 5 day";
      
        $select = $connection->select()
            ->from(
                ['c' => $tableName]           
            )->where("entity_id > ?",0);            
        $whereCondition = str_replace("'","", $connection->quoteInto("date(c.created_at) > ?", $scopedate));      
        $select->where($whereCondition);
        $records = $connection->fetchAll($select);
        
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/UpdateOrderGridData.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);        
        $logger->info('===Start===='.date('Y-m-d h:i:s'));
      
        $jsonOrder = json_encode($records);       
        return $jsonOrder;
    }     
}  
