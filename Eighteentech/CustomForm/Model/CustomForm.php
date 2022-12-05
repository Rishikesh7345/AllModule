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
namespace Eighteentech\CustomForm\Model;

use Magento\Framework\Model\AbstractModel;

class CustomForm extends AbstractModel
{
     /**
      * CMS page cache tag.
      */
    public const CACHE_TAG = 'customform_table';

    /**
     * @var string
     */
    protected $_cacheTag = 'customform_table';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'customform_table';
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(\Eighteentech\CustomForm\Model\ResourceModel\CustomForm::class);
    }
}
