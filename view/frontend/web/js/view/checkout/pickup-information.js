define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/form/form',
        'mage/mage',
        'uiRegistry',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'MageWorx_Checkout/js/action/select-shipping-address',
        'mage/validation',
        'jquery/ui'
    ],
    function (
        $,
        _,
        Component,
        mage,
        registry,
        checkoutData,
        addressConverter,
        quote,
        customer,
        selectShippingAddress
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'MageWorx_PickupCheckout/checkout/container/pickup-information',
                shippingFormTemplate: 'MageWorx_PickupCheckout/checkout/address-form',
                shippingFormVisible: true,
                visible: false
            },

            observableProperties: [
                'shippingFormVisible',
                'visible'
            ],

            initObservable: function () {
                this._super();
                this.observe(this.observableProperties);

                return this;
            },

            saveShippingAddress: function () {
                console.log('Saving shipping address for pickup');
                var shippingAddress = quote.shippingAddress(),
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                //Copy form data to quote shipping address object
                for (var field in addressData) {
                    if (addressData.hasOwnProperty(field) &&
                        shippingAddress.hasOwnProperty(field) &&
                        typeof addressData[field] != 'function' &&
                        _.isEqual(shippingAddress[field], addressData[field])
                    ) {
                        shippingAddress[field] = addressData[field];
                    } else if (typeof addressData[field] != 'function' &&
                        !_.isEqual(shippingAddress[field], addressData[field])
                    ) {
                        shippingAddress = addressData;
                        break;
                    }
                }

                if (customer.isLoggedIn()) {
                    shippingAddress.save_in_address_book = 1;
                }

                return selectShippingAddress(shippingAddress);
            },

            /**
             * Validates each element and returns true, if all elements are valid.
             */
            validate: function () {
                var isValid,
                    fieldset = registry.get('checkout.steps.shipping-step.pickupInformation.shipping-address-fieldset');

                isValid = validate([fieldset]);

                function validate(components) {
                    try {
                        _.each(components, function (item) {
                            var result;

                            if (item.index.indexOf('-group') > -1 || item.index.indexOf('-fieldset') > -1) {
                                result = validate(item.elems());
                            } else {
                                result = validateElement(item);
                            }

                            if (!result) {
                                throw new Error('Invalid element');
                            }
                        });
                    } catch (e) {
                        return false;
                    }

                    return true;

                    function validateElement(element) {
                        var valid = true;
                        if (typeof element.validate !== 'function') {
                            return valid;
                        }

                        var result = element.validate();
                        if (result && result.valid == true) {
                            valid = true;
                        } else if (result && result.valid == false) {
                            valid = false;
                        }

                        return valid;
                    }
                }

                if (isValid) {
                    this.saveShippingAddress();
                } else {
                    this.focusInvalid();
                }

                return isValid;
            },
        });
    }
);
