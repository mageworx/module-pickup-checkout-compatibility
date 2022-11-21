define([
    'jquery',
    'underscore',
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Customer/js/customer-data',
    'uiRegistry',
    'MageWorx_PickupCheckout/js/action/tabs/pickup',
    'mage/translate'
], function (
    $,
    _,
    Component,
    ko,
    quote,
    shippingService,
    customerData,
    registry,
    pickupTabProcess,
    $t
) {
    'use strict';

    return Component.extend({
        defaults: {
            visible: false,
            previousVisibility: false,
            deps: [
                'order_tabs'
            ]
        },

        observableProperties: [
            'visible',
            'previousVisibility'
        ],

        initialize: function () {
            this._super();

            console.log('Pickup Tab initialized');

            return this;
        },

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);
            this.initSubscribers();
            this.setInitialVisibility();

            return this;
        },

        initSubscribers: function () {
            registry.async('order_tabs')(function (tabs) {
                this.visible.subscribe(function (oldValue) {
                    this.previousVisibility(oldValue);
                }, this, 'beforeChange');
            }.bind(this));
        },

        process: function (flag) {
            pickupTabProcess(flag);
        },

        setInitialVisibility: function () {
            var self = this,
                ratesObservable = shippingService.getShippingRates();

            ratesObservable.subscribe(function (rates) {
                var visible = false;
                for (var rate of rates) {
                    var carrierCode = rate.carrier_code;
                    if (carrierCode === 'mageworxpickup') {
                        visible = true;
                        break;
                    }
                }

                if (!visible && this.previousVisibility()) {
                    let parent = registry.get(this.parentName),
                        deliveryTab = parent && parent.getChild('delivery');

                    if (deliveryTab) {
                        parent.selectTab(deliveryTab);
                    } else {
                        console.log('ERROR! Unable to locate delivery tab.');
                    }
                }

                self.visible(visible);
            }, this);
        }
    });
});
