<?php
/**
 * Class Collection
 *
 * PHP version 7 & 8
 *
 * @category Sparsh
 * @package  Sparsh_ProductInquiry
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\ProductInquiry\Model\ResourceModel\ProductInquiry;

/**
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * @var string
     */
    protected $_eventPrefix = 'sparsh_product_inquiry_post_collection';
    /**
     * @var string
     */
    protected $_eventObject = 'product_inquiry_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Sparsh\ProductInquiry\Model\ProductInquiry::class,
            \Sparsh\ProductInquiry\Model\ResourceModel\ProductInquiry::class
        );
    }
}
