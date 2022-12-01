<?php
/**
 * Class ProductInquiry
 *
 * PHP version 7 & 8
 *
 * @category Sparsh
 * @package  Sparsh_ProductInquiry
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\ProductInquiry\Model;

/**
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class ProductInquiry extends \Magento\Framework\Model\AbstractModel
{
    public const CACHE_TAG = 'sparsh_product_inquiry_post';

    /**
     * @var string
     */
    protected $_cacheTag = 'sparsh_product_inquiry_post';
    /**
     * @var string
     */
    protected $_eventPrefix = 'sparsh_product_inquiry_post';

    /**
     * ProductInquiry Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Sparsh\ProductInquiry\Model\ResourceModel\ProductInquiry::class);
    }
}
