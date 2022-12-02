<?php

namespace Eighteentech\Dashboard\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class SalesOrderAfterSave implements ObserverInterface
{

protected $orderCommentSender;

public function __construct(
    OrderCommentSender $orderCommentSender
)
{
    $this->orderCommentSender = $orderCommentSender;
}

public function execute(\Magento\Framework\Event\Observer $observer)
{
    $order = $observer->getEvent()->getOrder();
    if ($order instanceof \Magento\Framework\Model\AbstractModel) {
       if($order->getState() == 'canceled' || $order->getState() == 'closed') {
            //Your code here
       }
    }
    return $this;
}
}