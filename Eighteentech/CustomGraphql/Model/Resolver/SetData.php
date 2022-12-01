<?php
/**
 * @author Mohit Patel
 * @copyright Copyright (c) 2021
 * @package Mag_ContactUs
 */

namespace Eighteentech\CustomGraphql\Model\Resolver;


use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;


class SetData implements ResolverInterface
{
    private $inquiryFactory;

    /**
     * @param
     */
    public function __construct(
        \Sparsh\ProductInquiry\Model\ProductInquiryFactory $inquiryFactory
    ) {
        $this->_inquiryFactory = $inquiryFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        // print_r($args);die;       
        $name = $args['input']['name']??null;
        $phone = $args['input']['phone']??null;
        $email = $args['input']['email']??null;
        $description = $args['input']['description']??null;
        $sku = $args['input']['sku']?? null;
        $inquiry = $this->_inquiryFactory->create();
        $inquiry->setName($name);
        $inquiry->setPhone($phone);
        $inquiry->setEmail($email);
        $inquiry->setDescription($description);
        $inquiry->setSku($sku);
        $inquiry->save();
        $success_message = ["success_message"=>"Data add successfully"];
       
        return $success_message;
    }
}