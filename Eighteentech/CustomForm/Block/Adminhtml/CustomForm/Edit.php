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
namespace Eighteentech\CustomForm\Block\Adminhtml\CustomForm;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Department edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Eighteentech_CustomForm';
        $this->_controller = 'adminhtml_customform';

        parent::_construct();
        
        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');

        if ($this->_isAllowedAction('Eighteentech_CustomForm::customform_manage')) {
           
            $this->addButton(
                'delete',
                [
                'label' => __('Delete'),
                'onclick' => 'deleteConfirm(' . json_encode(__('Are you sure you want to do this?'))
                    . ','
                    . json_encode($this->getDeleteUrl())
                    . ')',
                'class' => 'scalable delete',
                'level' => -1
                ]
            );
        }
    }

    /**
     *  Delete record.
     *
     * @return void
     */
    public function getDeleteUrl()
    {
            return $this->getUrl('eighteentech_customform/customform/delete', ['_current' => true]);
    }

    /**
     * Get header
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('eighteentech_customform')->getId()) {
            return __(
                "View Custom Form '%1'",
                $this->escapeHtml($this->_coreRegistry->registry('eighteentech_customform')
                ->getName())
            );
        } else {
            return __('New Custom Form');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
