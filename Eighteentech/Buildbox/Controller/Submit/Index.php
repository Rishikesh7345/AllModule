<?php
/**
 * @author 18th DigiTech Team
 * @copyright Copyright (c) 2022 18th DigiTech (https://www.18thdigitech.com)
 * @package Eighteentech_Buildbox
 */
namespace Eighteentech\Buildbox\Controller\Submit;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\QuoteRepository;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Index Controller
 */
class Index extends Action
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var _productCollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var jsonResultFactory
     */
    protected $jsonResultFactory;

    /**
     * @var jsonResultFactory
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var StockItemRepository $_stockItemRepository
     */
    protected $_stockItemRepository;

    /**
     * @var \Magento\Catalog\Model\Product\Option $customOptions
     */
    protected $customOptions;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param FormKey $formKey
     * @param Cart $cart
     * @param CollectionFactory $productCollectionFactory
     * @param Product $product
     * @param JsonFactory $jsonResultFactory
     * @param ResourceConnection $resource
     * @param Session $checkoutSession
     * @param Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param ProductFactory $productFactory
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param \Magento\Catalog\Model\Product\Option $customOptions
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        Cart $cart,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        Product $product,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Catalog\Model\Product\Option $customOptions
    ) {
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->product = $product;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->resource = $resource;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->productFactory = $productFactory;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_customOptions = $customOptions;
        parent::__construct($context);
    }

    /**
     * Execute build box functionality
     */
    public function execute()
    {
		
        $post = $this->getRequest()->getPostValue();
        // store Selected product qty for each box 
        // print_r($post);
        // die;
        $kitProQty = 1;
        if(isset($post['kitProQty'])){
            $kitProQty = $post['kitProQty'];
        }
        // die;
        $proIdKit=[];
        if(isset($post['proIdKit'])){
            $proIdKit = json_decode($post['proIdKit'],true);
        }
        $storeAddonsPrice = 0;
        if(isset($post['storeAddonsPrice'])){
            $storeAddonsPrice = $post['storeAddonsPrice'];
        }
        // echo $storeAddonsPrice;
        // die;
        $selectKitProId='';

        if(isset($post['kitConfChildQtyId'])){
            $selectKitProId = unserialize(base64_decode($post['selectKitProId'][0]));
            $arrProSize = unserialize(base64_decode($post['arrProSize']));

            $kitConfChildQtyId = json_decode($post['kitConfChildQtyId'],true);
            $selectkitParentId = $post['selectkitParentId'];
            $configProSize = unserialize(base64_decode($post['configProSize']));
            $ProSize = implode(",",$configProSize);

            $cart = $this->cart;
            $quote = $cart->getQuote();  

            $cartItems = $cart->getQuote()->getAllItems();
            foreach($post['getItem'] as $selectedId){            
                foreach ($cartItems as $item) {
                    if($item->getItemId() == $selectedId){
                        if($item->getProductType() != 'configurable'){
                            $quoteItem = $quote->getItemById($item->getItemId());                    
                            $quoteItem->setQty($post['totalQty']);
                            $quoteItem->save();
                        }
                    }
                }
            }

            $params = [];
            $options = [];
            
            $removePro = 1; 
            $cartItems = $cart->getQuote()->getAllItems();
            foreach($kitConfChildQtyId as $kitProId => $qty){
                if($removePro ==1){
                    
                    foreach ($cartItems as $item) {
                        if($item->getProductId() == $selectkitParentId){
                                $qouteItem = $quote->getItemById($item->getItemId());                    
                                $qouteItem->delete();
                                $qouteItem->save();
                                continue;
                        }
                    }
                    $removePro=0;
                }
                
                $parent = $this->productFactory->create()->load($selectkitParentId);
                $child = $this->productFactory->create()->load($kitProId);
                
                $params['product'] = $parent->getId();
                $params['qty'] = $qty;

                $productAttributeOptions = $parent->getTypeInstance(true)->getConfigurableAttributesAsArray($parent);

                foreach ($productAttributeOptions as $option) {
                    $options[$option['attribute_id']] = $child->getData($option['attribute_code']);
                    
                }
                $params['super_attribute'] = $options;

                /*Add product to cart */      
            
                $cart->addProduct($parent, $params);
                $cart->save();

                $cartItems = $cart->getQuote()->getAllItems();
                foreach ($cartItems as $item) {
                    if($item->getProductId() == $kitProId){
                        $quoteItem = $quote->getItemById($item->getParentItemId());                    
                        $quoteItem->setBoxType("yes");   
                        $quoteItem->setBoxProductId($post['box_parent_Id']);
                        $quoteItem->setBoxItemId($post['box_parent_Id']);
                        if(array_key_exists($quoteItem->getProductId(),$proIdKit)){
                            $quoteItem->setProductQtyEachBox($proIdKit[$quoteItem->getProductId()]);
                        }
                       
                        foreach($arrProSize as $arrproId => $arrProVal){
                            if($item->getProductId() == $arrproId){
                                $quoteItem->setKitProductSize($arrProVal);
                            }
                        }
                        $quoteItem->save();
                    }
                }
            }

        }

        $productId = $post['parentProductId'];
        $cart = $this->cart;
 
        $itemInBox = 0;
        if (!empty($post['itemInBox'])) {
            $itemInBox = 1;
        } else {
            $itemInBox = 0;
        }

        $sum = 0;
        $proDim=[];
        foreach ($post['prodDim'] as $key => $value) {
            $proDim[] = $value;
            $sum += $value;
        }

        /**
         * @var $childWCId
         * store products child id
         */
        $childWCId = '' ;
        if (!isset($post["optionProId"])) {
            $childWCId = $post['choose-buildbox'];
        } else {
            $childWCId = $post["optionProId"];
        }

        /**
         * @var $childProduct
         * Load product by product id
         */
        $childProduct = $this->productFactory->create()->load($childWCId);
        $parent = $this->productFactory->create()->load($productId);

        /**
         * @var $params
         * Store product parameters
         */
        $params = [];
        $options = [];
        /**
         * @var $prodId
         * Store product id
         */
        $prodId=[];

        /**
         * @var $itemNum
         * get store item Number
         */
        $itemNum = '';
   
        $params['product'] = $parent->getId();
        // $params['qty'] = $post['boxQty'];
        if(isset($post['totalQty'])){
            $params['qty']= $post['totalQty'];
        }else{
            $params['qty']=$kitProQty;
        }

        $productAttributeOptions = $parent->getTypeInstance(true)->getConfigurableAttributesAsArray($parent);
        $customOptions = $this->_customOptions->getProductOptionCollection($parent);
        $optionId = '';

        foreach ($customOptions as $option) {
            $optionId = $option->getId();
        }

        foreach ($productAttributeOptions as $option) {
            $options[$option['attribute_id']] = $childProduct->getData($option['attribute_code']);
        }

        $params['super_attribute'] = $options;

        // add custom column value in cart product with box
        if (!empty($post['getItem'])) {
            $avbQty = '';
            $prodId = $post['getItem'];
            $itemNum = count($post['getItem']);
            $itemProId = '';
            for ($i = 0; $i < $itemNum; $i++) {
                $quote = $this->cart->getQuote();
                $item = $quote->getItemById($prodId[$i]);
                
                $quote1 = $this->quoteRepository->get($item->getQuoteId());
                $quote1->setData('esdc_enable', $itemInBox); // Fill data
                $this->quoteRepository->save($quote1);
                 $item->setEsdcPricing($itemInBox);
                $item->setBoxType("yes"); //don't change
                 
                $item->setBoxProductId($productId);
                if(array_key_exists($item->getProductId(),$proIdKit)){
                    $item->setProductQtyEachBox($proIdKit[$item->getProductId()]);
                    if(isset($post['totalQty'])){
                        $item->setQty($post['totalQty'] * $proIdKit[$item->getProductId()]);
                    }else{
                        $item->setQty($kitProQty * $proIdKit[$item->getProductId()]);
                    }
                }
                
                if ($parent->getId()==$item->getBoxProductId()) {
                    $item->setProductDim($proDim[$i]);
                }
             
                $option = [
                    $optionId  => $parent->getName().'_'. $item->getItemId()
                ];
                $item->save();
            }
        }
        
        $params['options'] = $option;
        $params['options_'.$childWCId[0].'_file_action'] = 'save_new';

        $cart->addProduct($parent, $params);      
        $cart->save();
       
       //message for response
        $data = ['success' => 'true', 'msg' => 'Product added to cart successfully!'];
        $result = $this->jsonResultFactory->create();
        $result->setData($data);

        /** 
         * ** After Add To cart save Same value in table **
         * ------------------------------------------------
         */
        $quoteId = $this->cart->getQuote()->getId();
        $itemsArray = $this->cart->getQuote()->getAllItems();
       
        /**
         * @var $itemId
         * Store Cart Itemid when productid == exist product id in cart
         */
        $itemId ='';
        $parentId = '';
        $ItemProId = '';
        $getBoxItems = [];
        foreach ($itemsArray as $items) {
            if ($items->getProductId() == $productId) {
                $itemId = $items->getItemId();
                $parentId=$itemId;
                $quotebox = $this->cart->getQuote();
                $setBoxId = $quotebox->getItemById($itemId);
                $getBoxItems[] = $setBoxId->getItemId();
                $boxPrice= $setBoxId->getPrice();
                $setBoxId->setBoxId(1);
                $setBoxId->setProductQtyEachBox(1);
                if($storeAddonsPrice != 0){
                    $boxTot = $boxPrice + (int)$storeAddonsPrice;
                    $setBoxId->setPrice($boxTot);
                    $setBoxId->setKitAddonPrice($storeAddonsPrice);
                }
                if(isset($post['totalQty'])){
                    $item->setQty($post['totalQty']);
                }else{
                    $item->setQty($kitProQty);
                }       

                if ($setBoxId->getBoxId() == '1') {
                    $ItemProId = $setBoxId->getItemId();
                    $this->SetKitItemId($ItemProId);
                }
                if(isset($post['kitConfChildQtyId'])){
                if ($setBoxId->getKitProductSize() == '') {
                    $this->CheckProduct($itemId);
                }
            }
                $setBoxId->setProductDim($sum);
                $setBoxId->save();
            }
            if (!empty($parentId) && ($parentId == $items->getParentItemId())) {
               
                    $additionalOptions = $items->getOptionByCode('info_buyRequest');
                    $quotebox = $this->cart->getQuote();
                    $parentItem = $quotebox->getItemById($parentId);
                    $buyRequest =$additionalOptions->getValue();
                    $parentItem->getOptionByCode('info_buyRequest')->setValue($buyRequest);
                    $parentItem->saveItemOptions();
                    $parentItem->save();
            }
        }
        if (!empty($post['getItem'])) {
            $prodId = $post['getItem'];
            $itemNum = count($post['getItem']);
            for ($i = 0; $i < $itemNum; $i++) {
                $quote = $this->cart->getQuote();
                $item = $quote->getItemById($prodId[$i]);
                $item->setBoxItemId($ItemProId);
                $item->save();
            }
        }

        //after add to cart save qty or box name in kit box 
        if (!empty($getBoxItems)) {
            $itemNumco = count($getBoxItems);
            for ($i = 0; $i < $itemNumco; $i++) {
                $quote = $this->cart->getQuote();
                $item = $quote->getItemById($getBoxItems[$i]);
                if (empty($item->getBoxName())) {                    
                    $quote = $this->cart->getQuote();
                    $items = $quote->getItemById($item->getItemId());
                    if(isset($post['totalQty'])){
                        $item->setQty($post['totalQty']);
                    }else{
                        $item->setQty($kitProQty);
                    }   
                    $item->setBoxName($post['input-box-name']);//
                    $item->setBoxProductionDay($post['production']);
                }
                $item->save();                
            }
        }

        // if(isset($post['kitConfChildQtyId'])){
        //     $cartItems = $cart->getQuote()->getAllItems();
        //     $kitItemId = '';
        //     foreach ($cartItems as $item) {
        //          if($item->getBoxId() == 1) {
        //            echo $kitItemId = $item->getItemId();
        //          }
        //          if($item->getKitProductSize() != null) {
        //             echo  $kitItemId ;
        //             $quote = $this->cart->getQuote();
        //             $quoteitem = $quote->getItemById($item->getItemId());
        //             $quoteitem->setBoxItemId("sdfasdfasdfsa");
        //             $quoteitem->save(); 
        //          }
                 
        //     }
        // }

        //add configurable product value after add to cart
        if(isset($post['kitConfChildQtyId'])){            
            $kitConfChildQtyId = json_decode($post['kitConfChildQtyId'],true);
            $arrProSize = unserialize(base64_decode($post['arrProSize']));
            $itemkitId = '';
            $cartItems = $cart->getQuote()->getAllItems();
            foreach ($cartItems as $item) {                    
                if($item->getBoxId() == 1){
                    if($item->getProductId() == $productId){
                        $itemkitId = $item->getItemId();    
                    }
                }
            }
            foreach($kitConfChildQtyId as $kitProId => $qty){
                $cartItems = $cart->getQuote()->getAllItems();
                foreach ($cartItems as $item) {              
                    if($item->getProductId() == $kitProId){
                        $quoteItem = $quote->getItemById($item->getParentItemId());                    
                        $quoteItem->setBoxType("yes");
                        $quoteItem->setBoxProductId($productId);
                        $quoteItem->setBoxItemId($itemkitId);
                        $quote1 = $this->quoteRepository->get($item->getQuoteId());
                        $quote1->setData('esdc_enable', $itemInBox); // Fill data
                        $this->quoteRepository->save($quote1);                        
                        $quoteItem->save();
                    }else{
                        if($item->getBoxType() == 'yes' && $item->getBoxProductId() == $productId){
                            $quote = $this->cart->getQuote();
                            $item = $quote->getItemById($item->getItemId());
                            $item->setBoxItemId($itemkitId);
                            $item->save();
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Get Box Id and check which product is configurable
     * 
     * @param int $itemId
     * 
     * @return bool
     */
    public function CheckProduct($itemId){
       
        $quoteId = $this->cart->getQuote()->getId();
        $itemsArray = $this->cart->getQuote()->getAllItems();
        foreach ($itemsArray as $items) {
            if($items->getBoxType() == 'yes' ){
                if($items->getProductType() == 'configurable')
                {
                    $quotebox = $this->cart->getQuote();
                    $setBoxId = $quotebox->getItemById($itemId);
                    $setBoxId->setBoxType("yes");
                    $setBoxId->save();
                }
            }
        }
        return 'true';
    }
    
    /**
     * Get Box Id and check which product is configurable
     * 
     * @param int $itemId
     * 
     * @return bool
     */
    public function SetKitItemId($ItemProId){
        if(isset($post['kitConfChildQtyId'])){
            $cartItems = $cart->getQuote()->getAllItems();
            $kitItemId = '';

            foreach ($cartItems as $item) {   
                echo $item->getKitProductSize();              
                 if($item->getKitProductSize() != null) {
                    
                    $quote = $this->cart->getQuote();
                    $quoteitem = $quote->getItemById($item->getItemId());
                    $quoteitem->setBoxItemId($ItemProId);
                    $quoteitem->save(); 
                 }
                 
            }
        }
        return 'true';
    }
}
