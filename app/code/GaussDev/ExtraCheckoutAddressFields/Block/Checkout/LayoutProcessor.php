<?php

namespace GaussDev\ExtraCheckoutAddressFields\Block\Checkout;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    protected $helper;

    public function __construct(
        \GaussDev\ExtraCheckoutAddressFields\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }

    public function process($result)
    {
        $result = $this->getShippingFormFields($result);
        $result = $this->getBillingFormFields($result);
        return $result;
    }

    public function getShippingFormFields($result)
    {
        if (isset($result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset'])
        ) {

            $shippingPostcodeFields = $this->getFields('shippingAddress.custom_attributes', 'shipping');

            $shippingFields = $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'];

            if (isset($shippingFields['street'])) {
                unset($shippingFields['street']['children'][1]['validation']);
                unset($shippingFields['street']['children'][2]['validation']);
            }

            $shippingFields = array_replace_recursive($shippingFields, $shippingPostcodeFields);

            $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'] = $shippingFields;

        }

        return $result;
    }

    public function getBillingFormFields($result)
    {
        if (isset($result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list'])) {

            $paymentForms = $result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list']['children'];

            foreach ($paymentForms as $paymentMethodForm => $paymentMethodValue) {

                $paymentMethodCode = str_replace('-form', '', $paymentMethodForm);

                if (!isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form'])) {
                    continue;
                }

                $billingFields = $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'];

                $billingPostcodeFields = $this->getFields('billingAddress' . $paymentMethodCode . '.custom_attributes', 'billing');

                $billingFields = array_replace_recursive($billingFields, $billingPostcodeFields);

                $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'] = $billingFields;
            }
        }

        return $result;
    }

    public function getAdditionalFields($addressType = 'shipping')
    {
        if ($addressType == 'shipping') {
            return $this->helper->getExtraCheckoutAddressFields('extra_checkout_shipping_address_fields') ?: [];
        }
        return $this->helper->getExtraCheckoutAddressFields('extra_checkout_billing_address_fields') ?: [];
    }

    public function getFields($scope, $addressType)
    {
        $fields = [];
        foreach ($this->getAdditionalFields($addressType) as $code => $field) {
            $fields[$code] = $this->getField($code, $field, $scope);
        }
        return $fields;
    }

    public function getField($attributeCode, $fieldData, $scope)
    {
        $field = array_merge([
            'dataScope' => $scope . '.' . $attributeCode,
            'label' => $attributeCode,
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 100,
            'id' => $attributeCode
        ], $fieldData);

        $config = array_merge($this->getDefaultConfig(), $fieldData);
        $config['id'] = $attributeCode;
        $config['customScope'] = $scope;

        $field['config'] = $config;

        return $field;
    }

    protected function generateFieldConfig($code, $field, $scope)
    {
        $defaultConfig = [
            'customScope' => $scope,
            'template' => 'ui/form/field',
            'elementTmpl' => 'ui/form/element/input',
            'options' => [],
            'id' => $code
        ];
        return array_merge($defaultConfig, $field);
    }
    protected function getSortOrder($code)
    {
        $sortOrders = [
            'digi_code' => 20
        ];
        return isset($sortOrders[$code]) ? $sortOrders[$code] : 10;
    }
    private function getDefaultConfig()
    {
        return  [
            'template' => 'ui/form/field',
            'elementTmpl' => 'ui/form/element/input',
            'options' => [],
        ];
    }
}