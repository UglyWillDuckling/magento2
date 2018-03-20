<?php

namespace GaussDev\ExtraCheckoutAddressFields\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper{

    protected $fieldsetConfig;

    protected $logger;

    public function __construct(
        \Magento\Framework\DataObject\Copy\Config $fieldsetConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->fieldsetConfig = $fieldsetConfig;
        $this->logger = $logger;
    }

    public function getExtraCheckoutAddressFields($fieldset='extra_checkout_billing_address_fields',$root='global'){

        $fields = $this->fieldsetConfig->getFieldset($fieldset, $root);

        $extraCheckoutFields = [];

//        foreach($fields as $field => $targetField){
//            $extraCheckoutFields[$field] = array_values($targetField)[0];
//        }
//        return $extraCheckoutFields;
        return $fields;
    }

    public function transportFieldsFromExtensionAttributesToObject(
        $fromObject,
        $toObject,
        $fieldset='extra_checkout_billing_address_fields'
    )
    {
        foreach($this->getExtraCheckoutAddressFields($fieldset) as $key => $extraField) {

            $set = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $get = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

            $value = $fromObject->$get();
            try {
                $toObject->$set($value);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        return $toObject;
    }
}