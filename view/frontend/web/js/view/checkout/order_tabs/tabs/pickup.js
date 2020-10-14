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
            previousVisibility: false
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
            this.updateInitialVisibilityCount();

            return this;
        },

        initSubscribers: function () {
            var tabs = registry.get('order_tabs');

            this.visible.subscribe(function (oldValue) {
                this.previousVisibility(oldValue);
            }, this, 'beforeChange');

            this.visible.subscribe(function (visibility) {
                var oldVisibility = this.previousVisibility();
                if (oldVisibility == false && visibility == true) {
                    tabs.visibleTabsCount(tabs.visibleTabsCount() + 1);
                } else if (oldVisibility == true && visibility == false) {
                    tabs.visibleTabsCount(tabs.visibleTabsCount() - 1);
                }
            }, this, 'change');
        },

        process: function (flag) {
            console.log('Processing pickup tab');
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

                self.visible(visible);
            });
        },

        updateInitialVisibilityCount: function () {
            var visibility = this.visible();
            registry.async('order_tabs')(function (tabs) {
                if (visibility) {
                    tabs.visibleTabsCount(tabs.visibleTabsCount() + 1);
                }
            });
        }
    });
});
