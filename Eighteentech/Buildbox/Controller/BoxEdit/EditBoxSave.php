<?php
/**
 * @author 18th DigiTech Team
 * @copyright Copyright (c) 2022 18th DigiTech (https://www.18thdigitech.com)
 * @package Eighteentech_Buildbox
 */
namespace Eighteentech\Buildbox\Controller\BoxEdit;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\QuoteRepository;

/**
 * Index Controller
 */
class EditBoxSave extends Action
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
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_catalogProductTypeConfigurable;
    
    /**
     * @var \Magento\Catalog\Model\Product\Option $customOptions
     */
    protected $customOptions;

    /**
     * @var\Magento\CatalogInventory\Api\StockStateInterface $stockState
     */
    protected $stockState;
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
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable
     * @param \Magento\Catalog\Model\Product\Option $customOptions
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
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
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Catalog\Model\Product\Option $customOptions,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState
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
        $this->_catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->_customOptions = $customOptions;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->stockState           = $stockState;
        parent::__construct($context);
    }

    /**
     * Execute build box functionality
     * 
     * @return html
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();


         /**
         * check Product qty
         */
        if(isset($post['getItem'])){            
            $cart = $this->cart;
            $quote = $cart->getQuote();

            $cartItems = $cart->getQuote()->getAllItems();
            foreach($post['getItem'] as $selectedId){            
                foreach ($cartItems as $item) {
                    if($item->getItemId() == $selectedId){
                        if($item->getProductType() != 'configurable'){
                            
                            $productQty = $this->stockState->getStockQty($item->getProductId()); 
      
                            if($post['totalQty'] <= $productQty){
                                $item->getProductId();
                            }else{
                                $data = ['success' => 'true','qtyvalid' => 'false', 'msg' => 'Product qty is not available!'];
                                $result = $this->jsonResultFactory->create();
                                $result->setData($data);
                                return $result;
                            }
                            
                        }else{
                            if(isset($post['kitConfChildQtyId'])){

                                $kitConfChildQtyId = json_decode($post['kitConfChildQtyId'],true);
                    
                                foreach($kitConfChildQtyId as $kitProId => $qty){
                                    $productQty = $this->stockState->getStockQty($kitProId);
                                    // echo $qty .'<='. $productQty;
                                    if($qty <= $productQty){
                                        "true";
                                    }else{
                                        $data = ['success' => 'true','qtyvalid' => 'false', 'msg' => 'Product qty is not available!'];
                                        $result = $this->jsonResultFactory->create();
                                        $result->setData($data);
                                        return $result;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $storekitBoxId = '';
        $kitProQty = 1;
        
        if(isset($post['kitProQty'])){
            $kitProQty = $post['kitProQty'];
        }
        $storeAddonsPrice = 0;
        if(isset($post['storeAddonsPrice'])){
            $storeAddonsPrice = (int)$post['storeAddonsPrice'];
        }
        $proIdKit=[];
        if($post['proIdKit']){
            $proIdKit = json_decode($post['proIdKit'],true);
        }
        
        if(isset($post['kitConfChildQtyId'])){
            $selectKitProId = unserialize(base64_decode($post['selectKitProId'][0]));
            
           $kitConfChildQtyId = json_decode($post['kitConfChildQtyId'],true);
           $selectkitParentId = $post['selectkitParentId'];
           $getItem=[];
           $getItem = $post['getItem'];

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
            
            $params1 = [];
            $options1 = [];
            
            $removePro = 1; 
            $cartItems = $cart->getQuote()->getAllItems();
            foreach($kitConfChildQtyId as $kitProId => $qty){
                if($removePro == 1){                    
                    foreach ($cartItems as $item) { 
                        if($item->getProductId() == $post['selectkitParentId']){
                    
                            $qouteItem = $quote->getItemById($item->getItemId());
                            $qouteItem->delete();
                            $qouteItem->save();
                            continue;
                               
                        }
                    }
                    $removePro=0;
                }

                $parent1 = $this->productFactory->create()->load($selectkitParentId);
                $child1 = $this->productFactory->create()->load($kitProId);
                
                $params1['product'] = $parent1->getId();
                $params1['qty'] = $qty;
        
                $productAttributeOptions = $parent1->getTypeInstance(true)->getConfigurableAttributesAsArray($parent1);
        
                foreach ($productAttributeOptions as $option) {
                    $options1[$option['attribute_id']] = $child1->getData($option['attribute_code']);
                }
                
                $params1['super_attribute'] = $options1;
         
                /*Add product to cart */
                $cart->addProduct($parent1, $params1);
                $cart->save();
            }
            
        }

        $cart=$this->cart;
        $itemInBox = 0;
        if (!empty($post['itemInBox'])) {
            $itemInBox = 1;
        } else {
            $itemInBox = 0;
        }

        /**
         * @var $sum
         * Store sum of proudcts dimension
         */
        $sum = 0;

        /**
         * @var $proDim
         * Store proudcts dimension value
         */
        $proDim=[];
        foreach ($post['prodDim'] as $key => $value) {
            $proDim[] = $value;
            $sum += $value;
        }

        /**
         * @var $childWCId
         * Get child product id and options id
         */
        $childWCId = '' ;

        if (!isset($post["optionProId"])) {
            $childWCId = $post['choose-buildbox'];
        } else {
            $childWCId = $post["optionProId"];
        }

        $productId = '';
        $parentByChild = $this->_catalogProductTypeConfigurable->getParentIdsByChild($childWCId);
        
        if (isset($parentByChild[0])) {
            //set id as parent product id...
            $productId = $parentByChild[0];
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
        $params['product'] = $parent->getId();
        
        if(isset($post['totalQty'])){
            $params['qty'] =$post['totalQty'];
        }else{
            $params['qty'] = $kitProQty;
        }

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
        /**
         * @var $getItemsArry
         * get All cart items
         */
        $productAttributeOptions = $parent->getTypeInstance(true)->getConfigurableAttributesAsArray($parent);
        $customOptions = $this->_customOptions->getProductOptionCollection($parent);
        $optionId = '';
        $option = [];
        $itemkitId = '';
        foreach ($customOptions as $option) {
            $optionId = $option->getId();
        }
        foreach ($productAttributeOptions as $option) {
            $options[$option['attribute_id']] = $childProduct->getData($option['attribute_code']);
        }

        $quote = $this->cart->getQuote();
        $getItemsArry = $this->cart->getQuote()->getAllItems();
        foreach ($getItemsArry as $items) {
            for ($i = 0; $i < count($post['getItem']); $i++) {

                $quoteItemCollection = $this->quoteItemCollectionFactory->create();
                $quoteItemCollection->addFieldToSelect('*')
                    ->addFieldToFilter('item_id', $items->getItemId())
                    // ->addFieldToFilter('box_product_id', $post['parentProductId'])
                    // ->addFieldToFilter('box_item_id', $post['productItemId'])
                    ->getFirstItem();

                foreach ($quoteItemCollection as $item) {
                   
                   if($item->getBoxItemId() == $post['editItemId']){
                        $itemkitId = $item->getItemId();
                        $remItem = $quote->getItemById($itemkitId);
                        $remItem->setBoxProductId(null);
                        $remItem->setBoxType(null);
                        $remItem->setBoxItemId(null);
                        $remItem->setProductDim(null);
                        
                        $remItem->setProductQtyEachBox(null);
                        $remItem->setKitProductSize(null);                        
                        $remItem->save();
                    }
                }
            }

            if($items->getProductType() == 'configurable'){
                $itemkitId = $item->getItemId();
                   
                   if($item->getBoxItemId() == $post['editItemId']){
                    $quoteItem = $quote->getItemById($items->getItemId());                    
                    $quoteItem->setBoxType(null);
                    $quoteItem->setBoxProductId(null);
                    $quoteItem->setProductQtyEachBox(null);
                    $quoteItem->setKitProductSize(null);

                    $quoteItem->save();
                }
            }

            if ($items->getItemId() == $post['editItemId']) {
                $items->delete();
                $items->save();
                continue;
            }
         
        }

        $itemInBox = 0;
        if (!empty($post['itemInBox'])) {
            $itemInBox = 1;
        } else {
            $itemInBox = 0;
        }
        $idForOption = '';
        /**
         * add custom column value in cart product with box
         */
       
        if (!empty($post['getItem'])) {
            $prodId = $post['getItem'];
            
            $quote = $this->cart->getQuote();
            $getItemsArry = $this->cart->getQuote()->getAllItems();
            foreach ($getItemsArry as $_item) {
               
                if(in_array($_item->getItemId(),$prodId)){                
                    $quote = $this->cart->getQuote();
                    $item = $quote->getItemById($_item->getItemId());                       
                    $item->setBoxType("yes"); //don't change
                    $item->setBoxProductId($productId);
                    
                    if(array_key_exists($item->getProductId(),$proIdKit)){
                        $idForOption = $item->getItemId();
                        $item->setProductQtyEachBox($proIdKit[$item->getProductId()]);
                        if(isset($post['totalQty'])){
                            $item->setQty($post['totalQty'] * $proIdKit[$item->getProductId()]);
                        }else{
                            $item->setQty($_item->getQty() * $proIdKit[$item->getProductId()]);
                        }
                    }
                    if ($parent->getId()==$item->getBoxProductId()) {
                        $item->setProductDim(3.7);
                    }
                    $option = [
                        $optionId  => $parent->getName().'_'. $item->getItemId()
                    ];

                    $item->save();
                }

                $option = [
                    $optionId  => $parent->getName().'_'. $idForOption
                ];
            }
        }
        
        $params['super_attribute'] = $options;        

        $params['options'] = $option;
        $params['options_'.$childWCId[0].'_file_action'] = 'save_new';

        $cart->addProduct($parent, $params);
        $cart->save();
        
        /**
         * @var $data
         * json message for response
         */
        $data = ['success' => 'true', 'msg' => 'Cart product Edit successfully!'];
        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        
       

        /**
         * @var $itemsArray
         * Get all Cart item
         */
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
                $parentId = $items->getItemId();
                $quotebox = $this->cart->getQuote();
                $setBoxId = $quotebox->getItemById($itemId);
                $getBoxItems[] = $setBoxId->getItemId();
                $boxPrice= $setBoxId->getPrice();
                $setBoxId->setBoxId(1);
                $setBoxId->setBoxItemId($setBoxId->getItemId());
                $storekitBoxId = $setBoxId->getItemId();
                
               
                $setBoxId->setProductQtyEachBox(1);
                
                if(isset($post['totalQty'])){
                    $item->setQty($post['totalQty']);
                }else{
                    $item->setQty($kitProQty);
                }

                if ( $setBoxId->getBoxId() == '1' ) {
                    $ItemProId = $setBoxId->getItemId();
                }
               
                $boxTot = $boxPrice + $storeAddonsPrice;
                $setBoxId->setPrice($boxTot);
                $setBoxId->setKitAddonPrice($storeAddonsPrice);
              
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

         //add configurable product value after add to cart
         if(isset($post['kitConfChildQtyId'])){            
            $kitConfChildQtyId = json_decode($post['kitConfChildQtyId'],true);
            $arrProSize = unserialize(base64_decode($post['arrProSize']));
            $itemkitId = '';
            $cartItems = $cart->getQuote()->getAllItems();
            $prodId = $post['getItem'];
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
                        $quoteItem->setBoxItemId($storekitBoxId);
                        if($quoteItem->getKitProductSize() == '' ){
                            $this->CheckProduct($itemkitId);
                        }
                        
                        $quote1 = $this->quoteRepository->get($quoteItem->getQuoteId());
                        $quote1->setData('esdc_enable', 1); // Fill data
                        $this->quoteRepository->save($quote1);

                        if(array_key_exists($quoteItem->getProductId(),$proIdKit)){
                            $quoteItem->setProductQtyEachBox($proIdKit[$quoteItem->getProductId()]);
                        }

                        foreach($arrProSize as $arrproId => $arrProVal){
                            if($item->getProductId() == $arrproId){
                                $quoteItem->setKitProductSize($arrProVal);
                                
                            }
                        }
                        $quoteItem->save();
                    }else{
                        if($item->getBoxType() == 'yes' && in_array($item->getItemId(),$prodId)){
                            $quote = $this->cart->getQuote();
                            $item = $quote->getItemById($item->getItemId());
                            $item->setBoxItemId($itemkitId);

                            $quote1 = $this->quoteRepository->get($item->getQuoteId());
                            $quote1->setData('esdc_enable', 1);
                            $this->quoteRepository->save($quote1);

                            $item->save();
                        }
                    }
                }
            }
        }else{
            // set kit item id in simple product 
            $itemkitId = '';
            $cartItems = $cart->getQuote()->getAllItems();
            foreach ($cartItems as $item) {                    
                if($item->getBoxId() == 1){
                    if($item->getItemId() == $storekitBoxId){
                        $itemkitId = $item->getItemId();    
                    }
                }            
            }
            foreach ($cartItems as $item) {

                $prodId = $post['getItem'];

                if($item->getBoxType() == 'yes' && in_array($item->getItemId(),$prodId)){
                   
                    $quote = $this->cart->getQuote();
                    $item = $quote->getItemById($item->getItemId());
                    $item->setBoxItemId(null);
                    $item->setBoxItemId($itemkitId);
                    $item->save();
                }
            }
        }
        
        // set box qty or box name after add to cart product 
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
                    $items->setBoxName($post['existBoxName']);
                }
                $item->save();
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
}
