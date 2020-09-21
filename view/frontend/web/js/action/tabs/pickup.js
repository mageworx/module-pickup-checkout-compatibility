/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data',
    'uiRegistry',
    'mage/translate'
], function (
    $,
    _,
    Component,
    ko,
    quote,
    shippingService,
    selectShippingMethodAction,
    checkoutData,
    customerData,
    registry,
    $t
) {
    'use strict';

    return function (flag) {
        var elementsToHide = {
                'shippingAddressList': 'name = checkout.steps.shipping-step.shippingAddress.address-list',
                'shippingAddressListAdditionalAddresses': 'name = checkout.steps.shipping-step.shippingAddress.address-list-additional-addresses',
                'billingAddress': 'name = checkout.steps.shipping-step.billingAddress'
            },
            shippingMethods = registry.get('ns = checkout, name = checkout.steps.shipping-step.shippingMethods'),
            shippingAddress = registry.get('ns = checkout, name = checkout.steps.shipping-step.shippingAddress'),
            billingAddress = registry.get('ns = checkout, name = checkout.steps.shipping-step.billingAddress'),
            pickupInfo = registry.get('ns = checkout, name = checkout.steps.shipping-step.pickupInformation');

        if (flag === true) {
            enableTab();
        } else {
            disableTab();
        }

        function enableTab() {
            _.each(elementsToHide, function (value, key) {
                registry.async(value)(function (component) {
                    typeof component.visible === 'function' ? component.visible(false) : component.visible = false;
                });
            });

            shippingMethods.visible(false);
            shippingAddress.visible(false);
            billingAddress.visible(false);
            billingAddress.isAddressDifferent(true);
            billingAddress.isAddressSameAsShipping(false);
            pickupInfo.visible(true);
            selectPickupShippingMethod();
        }

        function disableTab() {
            _.each(elementsToHide, function (value, key) {
                registry.async(value)(function (component) {
                    typeof component.visible === 'function' ? component.visible(true) : component.visible = true;
                });
            });

            shippingMethods.visible(true);
            shippingAddress.visible(true);
            billingAddress.visible(true);
            pickupInfo.visible(false);
            deselectShippingMethod();
        }

        function selectPickupShippingMethod () {
            var rates = shippingService.getShippingRates()();
            for (var rate of rates) {
                var carrierCode = rate.carrier_code;
                if (carrierCode === 'mageworxpickup') {
                    break;
                }
            }

            if (rate) {
                registry.get('index = shippingMethods, ns = checkout').selectShippingMethod(rate);
            } else {
                console.log('Pickup shipping method not found');
            }
        }

        function deselectShippingMethod() {
            // registry.get('index = shippingMethods, ns = checkout').selectShippingMethod(null);
            selectShippingMethodAction(null);
            checkoutData.setSelectedShippingRate(null);
        }
    };
});
