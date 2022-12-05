<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @category Eighteentech
 * @package  Eighteentech_CustomForm
 *
 */
namespace Eighteentech\CustomForm\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomForm extends AbstractDb
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('customform_table', 'entity_id');
    }
}
