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

class Checkproductqty extends Action
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
     * @var\Magento\CatalogInventory\Api\StockStateInterface $stockState
     */
    protected $stockState;
    /**
     * Constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultRawFactory
     * @param Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
     */

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->resultRawFactory     = $resultRawFactory;
        $this->jsonResultFactory    = $jsonResultFactory; 
        $this->stockState           = $stockState;
        parent::__construct($context);
    }

    /**
     * Execute the function for get Item color
     *
     * @return array
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $html = "";
        
        $result = $this->resultRawFactory->create();
        
        $productQty = $this->stockState->getStockQty($post['kitconfproid']);
        
        if($post['qtyVal'] <= $productQty){
            $data = ['success' => 'true', 'msg' => 'Product Qty is valid!'];
            $result = $this->jsonResultFactory->create();
            $result->setData($data);
            return $result;
        }else{
            $qtyVal = $post['qtyVal'];
            $html = "
            <input type='hidden' name='invalidQty' id='invalidQty' class='invalidQty' value='".$qtyVal."' productQty='".$productQty."'/>
            <span class='productQty' style='color: red;'>Product qty is not available!</span>";
        }

        $result->setContents($html);
        return $result;
    }
}
