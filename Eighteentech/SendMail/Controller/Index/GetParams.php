<?php
namespace Eighteentech\SendMail\Controller\Index;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;

class GetParams extends Action implements HttpGetActionInterface
{
    /** 
     * @var CollectionFactory 
     **/
    protected $_orderCollectionFactory;

    protected $orderRepository;
    /**
     * @param Context $context
     * @param \Vendor\Module\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        \Eighteentech\SendMail\Helper\Helper $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        CollectionFactory $orderCollectionFactory
    ) {
        $this->helper        = $helper;
        $this->resultFactory = $resultFactory;
        $this->_storeManager = $storeManager;
        $this->orderRepository = $orderRepository;
        $this->_orderCollectionFactory = $orderCollectionFactory; 
        
        parent::__construct($context);
    }

    /**
     * Execute Method to display the parameter value
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
     
        $userIdHash = $this->getRequest()->getParam("id", "");
        $userId = 0;
        if (!empty($userIdHash)) {
            $decryptedHash = $this->helper->decodeUrl($userIdHash);
            $decryptedHash = str_replace(" ", "+", $decryptedHash);
            $userId = (int)$this->helper->decryptString($decryptedHash);
            echo "USER ID:$userId";



            $orderId = $userId;

        $order = $this->orderRepository->get($orderId);
        $this->orders = $this->_orderCollectionFactory->create()->get($orderId);
        foreach ($this->orders as $item) { 
            echo "======".$item->getCustomerId();
        }
// die;
        echo $order->getIncrementId();
        echo $order->getGrandTotal();
        echo $order->getSubtotal();
        
        //fetch whole payment information
        print_r($order->getPayment()->getData());
        
            
        //fetch customer information
        echo $order->getCustomerId();
        echo $order->getCustomerEmail();
        echo $order->getCustomerFirstname();
        echo $order->getCustomerLastname();
        
        //fetch whole billing information
        print_r($order->getBillingAddress()->getData());
        
        //Or fetch specific billing information
        echo $order->getBillingAddress()->getCity();
        echo $order->getBillingAddress()->getRegionId();
        echo $order->getBillingAddress()->getCountryId();
        
        //fetch whole shipping information
        print_r($order->getShippingAddress()->getData());
        
        //Or fetch specific shipping information
        echo $order->getShippingAddress()->getCity();
        echo $order->getShippingAddress()->getRegionId();
        echo $order->getShippingAddress()->getCountryId();























            die();
        }
        $resultRedirect = $this->resultFactory->create(
            \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
        );
        $redirectionUrl = $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $result = $resultRedirect->setUrl($redirectionUrl);

        




        die;
        return $result;
    }
}