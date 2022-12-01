<?php

namespace Sparsh\ProductInquiry\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class GetData implements ResolverInterface
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
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $entity_id = $args['entity_id'] ?? null;
        $pageSize = $args['pageSize'] ?? 0;
        $currentPage = $args['currentPage'] ?? 0;
        $tableName = $this->resourceConnection->getTableName('sparsh_product_inquiry');

        $connection = $this->resourceConnection->getConnection();
        if($entity_id != null){
          $select = $connection->select()
            ->from(
                ['c' => $tableName]           
            )->where("entity_id =".$args['entity_id']);
            $select->limit($pageSize,$currentPage);
         
        }else{
            $select = $connection->select()
            ->from(
                ['c' => $tableName]           
            )->where("entity_id > ?",0); 
            $select->limit($pageSize,$currentPage);
        
        }
        $records = $connection->fetchAll($select);
        return $records;
    }
}
