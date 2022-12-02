<?php
namespace Eighteentech\Dashboard\Model\Plugin;

class OrderStatePlugin
{

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $result
     * @return mixed
     * @throws \Exception
     */
    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        $result
    ) {
        // if($result->getState() == Order::STATE_COMPLETE) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/UpdateOrderGridData312.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);        
        $logger->info('===Start===='.date('Y-m-d h:i:s'));
        $logger->info($result);
        $logger->info('===='.print_r($result->getState(),true));
     
        // }
        // return $result;
    }
}