var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-billing-address': {
                'GaussDev_ExtraCheckoutAddressFields/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'GaussDev_ExtraCheckoutAddressFields/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/create-shipping-address': {
                'GaussDev_ExtraCheckoutAddressFields/js/action/create-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'GaussDev_ExtraCheckoutAddressFields/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'GaussDev_ExtraCheckoutAddressFields/js/action/set-billing-address-mixin': true
            }
        }
    }
};