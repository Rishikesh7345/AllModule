<?php
/**
 * @author 18th DigiTech Team
 * @copyright Copyright (c) 2022 18th DigiTech (https://www.18thdigitech.com)
 * @package Eighteentech_Buildbox
 */
namespace Eighteentech\Buildbox\Controller\BoxEdit;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class EditProid extends Action
{
    /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\UrlFactory
     */
    protected $urlFactory;

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
     * @var Session
     */
    protected $session;

    /**
     * @var productCollectionFactory
     */
     protected $productCollectionFactory;

    /**
     * @var Option $_imageBuilder
     */
     protected $_imageBuilder;
    
    /**
     * @var Option $_customOptions
     */
    protected $_customOptions;
    
    /**
     * @var Option $option
     */
    protected $option;

    /**
     * @var AppEmulation $appEmulation
     */
    protected $appEmulation;

    /**
     * @var BlockFactory $blockFactory
     */
    protected $blockFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultRawFactory
     * @param ProductFactory $_productloader
     * @param UrlFactory $urlFactory
     * @param Session $session
     * @param StoreManagerInterface $storeManager
     * @param Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param CollectionFactory $productCollectionFactory
     * @param ImageBuilder $_imageBuilder
     * @param Option $customOptions
     * @param Option $option
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param PriceCurrencyInterface $priceCurrency
     **/

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        ProductFactory $_productloader,
        UrlFactory $urlFactory,
        Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Block\Product\ImageBuilder $_imageBuilder,
        \Magento\Catalog\Model\Product\Option $customOptions,
        \Magento\Catalog\Model\Product\Option $option,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        PriceCurrencyInterface $priceCurrency
    ) {
            $this->resultPageFactory    = $resultPageFactory;
            $this->resultRawFactory     = $resultRawFactory;
            $this->_productloader       = $_productloader;
            $this->urlModel             = $urlFactory->create();
            $this->_session = $session;
            $this->storeManager = $storeManager;
            $this->productRepository   = $productRepository;
            $this->productCollectionFactory = $productCollectionFactory;
            $this->_imageBuilder = $_imageBuilder;
            $this->_customOptions = $customOptions;
            $this->option = $option;
            $this->blockFactory = $blockFactory;
            $this->appEmulation = $appEmulation;
            $this->priceCurrency      = $priceCurrency;
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
        $post = $this->getRequest()->getPostValue();
      
        $editProId = $post['editProId'];
        $proditemid = $post['proditemid'];
        $boxId = $post['boxId'];
        $boxDimentions = $post['boxDimentions'];

        $itemsCollection = $this->_session->getQuote()->getItemsCollection();
        $itemsVisible =$this->_session->getQuote()->getAllVisibleItems();
        $items = $this->_session->getQuote()->getAllItems();

        $dimension = '';
        $html = '';
        $editItemId=$storeKitItemQty = 0;
        $storeKitProId=[];
        $childProductQty = [];
        $storeProdQty = '';
        $checkConfigProd = true; 
        $html = '<div style="display:flex;" >';
        foreach ($items as $item) {

            $product = $this->_productloader->create()->load($item->getProductId());
            $productType= $item->getProductType();
            $editItemId = $item->getItemId();
            $height = $product->getKitHeight();
            $width = $product->getKitWidth();
            $lenght = $product->getKitLength();
            $dimension = ($height * $width * $lenght)/1000;

            if ($product->getProdinbox() == true) {
                if($productType == 'configurable'){
                    if($proditemid == $item->getBoxItemId() || $item->getBoxItemId() == null) {
                        
                        $product = $this->_productloader->create()->load($item->getProductId());
                                       
                        if($product->getId() == $item->getProductId() && $checkConfigProd == true){
                            $items = $this->_session->getQuote()->getAllItems();
                            foreach($items as $childItem){
                                if($childItem->getProductId() == $product->getId()){
                                  $storeKitProId[] = $childItem->getItemId();
                                  $storeKitItemQty += $childItem->getQty();
                                  $storeProdQty =$childItem->getQty();                                               
                                }
                                if($childItem->getParentItemId() != ''){
                                  $childProductQty[$childItem->getProductId()] = $storeProdQty;
                                }
                            }

                            $storeKitProId = base64_encode(serialize($storeKitProId));  
                            $childProQtyJson = base64_encode(serialize($childProductQty));
                            $imageUrl = $this->getImageUrl($product, 'product_page_image_small');
                            $html .= '<div class="product-list">';
                            if ($proditemid == $item->getBoxItemId()) {
                                // if ($item->getBoxItemId() == '') {
                                $html .='<input type="checkbox" name="getItem[]" value="'.$editItemId.'" 
                                    prod-qty="'.$item->getQty().'" data-dim="'.$dimension .'" 
                                    data-pro-id="'.$item->getProductId().'" class="proDimVal" checked> ';
                            } else {
                                $html .='<input type="checkbox" name="getItem[]" value="'.$editItemId.'" 
                                prod-qty="'.$item->getQty().'" data-dim="'.$dimension .'" 
                                data-pro-id="'.$item->getProductId().'" class="proDimVal"> ';
                            }

                            $html .= '
                            <input type="hidden" name="selectKitProId[]" value="'.$storeKitProId .'" id="storeKitProId">
                            <input type="hidden" name="storeKitItemQty" value="'. $storeKitItemQty .'" id="storeKitItemQty">
                            <input type="hidden"  name="childProductQty" value="'.$childProQtyJson.'" id="childProQtyJson" />
                            <input type="hidden" name="prodDim[]" value="'.$dimension.'">
                            <input type="hidden" name="productItemId" value="'.$proditemid.'">                               
                            <input type="hidden" name="product-qty[]" class="product-qty" 
                            id="prod-qty" value="'.$item->getQty().'">
                            
                            <div class="product-cart-box">
                                <div class="pro-image">
                                    <img 
                                        src="'.$imageUrl. '"
                                        width="100"
                                        height="100"
                                    />
                                </div>                                  
                                <div class="name"> 
                                        <h2>'.$item->getName().'</h2>
                                    </div>
                                </div>
                                <div class="prodQtysec">
                                    <span>Quantity in each box?</span>
                                    <input 
                                        type="text" 
                                        name="prodQtyForBox[]" 
                                        id="prodQtyForBox"
                                        class="qty-input"
                                        width="45px"
                                        value="0"
                                        prodIdForBox="'.$item->getProductId().'"
                                        prodTypeForBox="'.$item->getProductType().'"
                                        disabled
                                    />
                                  
                                    <p style="color: red;display:none" class="eachQty">
                                       Please add minimum 1 qty
                                    </p>
                                </div>
                            </div>';
                        }
                        $checkConfigProd = false; 
                    }
                }else {
                   if($proditemid == $item->getBoxItemId() || $item->getBoxItemId() == null) {
                    if($item->getPrice() != 0){
                    $imageUrl = $this->getImageUrl($product, 'product_page_image_small');
                    $html .= '<div class="product-list">';
                            if ($proditemid == $item->getBoxItemId()) {
                                $html .='<input type="checkbox" name="getItem[]" value="'.$editItemId.'" 
                                    prod-qty="'.$item->getQty().'" data-dim="'.$dimension .'" 
                                    data-pro-id="'.$item->getProductId().'" class="proDimVal" checked> ';
                            } else {
                                $html .='<input type="checkbox" name="getItem[]" value="'.$editItemId.'" 
                                prod-qty="'.$item->getQty().'" data-dim="'.$dimension .'" 
                                data-pro-id="'.$item->getProductId().'" class="proDimVal"> ';
                            }

                            $html .= '                           
                            <input type="hidden" name="prodDim[]" value="'.$dimension.'">
                            <input type="hidden" name="productItemId" value="'.$proditemid.'">                               
                            <input type="hidden" name="product-qty[]" class="product-qty" 
                            id="prod-qty" value="'.$item->getQty().'">
                            
                            <div class="product-cart-box">
                                <div class="pro-image">
                                    <img 
                                        src="'.$imageUrl. '"
                                        width="100"
                                        height="100"
                                    />
                                </div>                                  
                                <div class="name"> 
                                        <h2>'.$item->getName().'</h2>
                                    </div>
                                </div>
                                <div class="prodQtysec">
                                    <span>Quantity in each box?</span>
                                    <input 
                                        type="text" 
                                        name="prodQtyForBox[]" 
                                        id="prodQtyForBox"
                                        class="qty-input"
                                        width="45px"
                                        value="0"
                                        prodIdForBox="'.$item->getProductId().'"
                                        prodTypeForBox="'.$item->getProductType().'"
                                    />
                                    <p style="color: red;display:none" class="eachQty">
                                       Please add minimum 1 qty
                                    </p>
                                </div>
                            </div>';
                        }
                    }
                }
            }
        }
        $html .= '</div>';
        $result->setContents($html);
        return $result;
    }
    
    protected function getImageUrl($product, string $imageType = '')
    {
        $storeId = $this->storeManager->getStore()->getId();

        $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);

        $imageBlock =  $this->blockFactory->createBlock('Magento\Catalog\Block\Product\ListProduct');
        $productImage = $imageBlock->getImage($product, $imageType);
        $imageUrl = $productImage->getImageUrl();

        $this->appEmulation->stopEnvironmentEmulation();

        return $imageUrl;
    }
}
