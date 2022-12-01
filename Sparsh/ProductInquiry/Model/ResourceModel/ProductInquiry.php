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
namespace Sparsh\ProductInquiry\Model\ResourceModel;

/**
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class ProductInquiry extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define Maintable and primarykey
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sparsh_product_inquiry', 'entity_id');
    }
}
