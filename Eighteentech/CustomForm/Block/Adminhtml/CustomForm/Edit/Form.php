<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @category Eighteentech
 * @package  Eighteentech_CustomForm
 */
namespace Eighteentech\CustomForm\Block\Adminhtml\CustomForm\Edit;

/**
 * Adminhtml attachment edit form block
 *
 */
use Magento\Backend\Block\Widget\Form\Container;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Framework\Translate\AdapterInterface
     */
    protected $adapterInterface;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \\Magento\Framework\Translate\AdapterInterface $adapterInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Translate\AdapterInterface $adapterInterface,
        array $data = []
    ) {
        $this->adapterInterface = $adapterInterface;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('eighteentech_customform');
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setHtmlIdPrefix('customform_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );
        if ($model->getEntityId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }
        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => $this->adapterInterface->translate('Name'),
             'title' => $this->adapterInterface->translate('Name'), 'disabled' => true]
        );
        $fieldset->addField(
            'email',
            'text',
            ['name' => 'email', 'label' => $this->adapterInterface->translate('Email'),
            'title' => $this->adapterInterface->translate('Email'), 'disabled' => true]
        );
        $fieldset->addField(
            'telephone',
            'text',
            ['name' => 'telephone', 'label' => $this->adapterInterface->translate('Telephone'),
             'title' => $this->adapterInterface->translate('Telephone'), 'disabled' => true]
        );
        $fieldset->addField(
            'ip',
            'text',
            ['name' => 'ip', 'label' => $this->adapterInterface->translate('IP'),
             'title' => $this->adapterInterface->translate('IP'),  'disabled' => true]
        );

        $fieldset->addField(
            'browser',
            'text',
            ['name' => 'browser', 'label' => $this->adapterInterface->translate('Browser'),
             'title' => $this->adapterInterface->translate('Browser'),  'disabled' => true]
        );
        
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
