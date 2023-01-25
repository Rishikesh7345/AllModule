<?php
/**
 * @author 18th DigiTech Team
 * @copyright Copyright (c) 2022 18th DigiTech (https://www.18thdigitech.com)
 * @package Eighteentech_Buildbox
 */
namespace Eighteentech\Buildbox\Controller\Submit;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Session;

class CheckCartConfigProduct extends Action
{
    /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     */
    protected $jsonResultFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;
    
    /**
     * @var ProductFactory
     */
    protected $_productloader;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepositoryFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Session
     */
    protected $configurable;
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    
    protected $cart;
    /**
     * Constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultRawFactory
     * @param ProductFactory $_productloader
     * @param Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param Config $config
     * @param Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory
     * @param StoreManagerInterface $storeManager
     * @param Configurable $configurable
     * @param PriceCurrencyInterface $priceCurrency
     */

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        ProductFactory $_productloader,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Checkout\Model\Cart $cart
    ) {
            $this->resultPageFactory    = $resultPageFactory;
            $this->resultRawFactory     = $resultRawFactory;
            $this->_productloader       = $_productloader;
            $this->jsonResultFactory    = $jsonResultFactory;
            $this->config               = $config;
            $this->productCollectionFactory = $productCollectionFactory;
            $this->_productRepositoryFactory = $productRepositoryFactory;
            $this->storeManager = $storeManager;
            $this->configurable = $configurable;
            $this->priceCurrency = $priceCurrency;
            $this->cart = $cart;
            parent::__construct($context);
    }

    /**
     * Execute the function for get Item color
     *
     * @return array
     */
    public function execute()
    {
        $result = $this->resultRawFactory->create();
        // retrieve quote items collection
        $itemsCollection = $this->cart->getQuote()->getItemsCollection();

        // get array of all items what can be display directly
        $itemsVisible = $this->cart->getQuote()->getAllVisibleItems();

        // retrieve quote items array
        $items = $this->cart->getQuote()->getAllItems();
        $storeId = [];
        foreach($items as $item) {
            if($item->getProductType() == 'configurable'){
                $cart = $this->cart;
                $quote = $cart->getQuote();  
                $qouteItem = $quote->getItemById($item->getItemId());
                if($qouteItem->getItemId() == $item->getParentItemId()){
                    echo $item->getName();
                }
                
            }
            $parentConfigObject = $this->configurable->getParentIdsByChild($item->getProductId());
            $id = '';
            if (isset($parentConfigObject[0])) {
                //set id as parent product id...
                $id = $parentConfigObject[0];
                $storeId[] = $id;               
            }
            
            $product = $this->_productloader->create()->load($id);
            $product->getTypeId();
        }
        $html = "";
        if(count(array_unique($storeId)) > 1){
            $html = "<p class='allowproduct'>Only one configurable product is allowed for Kit.</p>";
        } else {
            $data = ['success' => 'true', 'msg' => 'Product added to cart successfully!'];
            $result = $this->jsonResultFactory->create();
            $result->setData($data);
            return $result;
        }
        $result->setContents($html);
        return $result;
    }
}
