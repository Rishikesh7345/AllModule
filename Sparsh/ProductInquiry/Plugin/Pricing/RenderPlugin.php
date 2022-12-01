<?php
/**
 * Class RenderPlugin
 *
 * PHP version 7 & 8
 *
 * @category Sparsh
 * @package  Sparsh_ProductInquiry
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\ProductInquiry\Plugin\Pricing;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

/**
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class RenderPlugin extends \Magento\Catalog\Pricing\Render
{
    /**
     * RenderPlugin constructor
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param \Sparsh\ProductInquiry\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        \Sparsh\ProductInquiry\Helper\Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Hide price if disclose price is disabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        $product = $this->getProduct();
        $disclosePrice = $this->helperData->getDisclosePrice($product->getId());

        if ($disclosePrice) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
