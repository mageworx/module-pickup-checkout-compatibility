/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'ko',
        'Magento_Catalog/js/price-utils',
        'mage/translate'
    ],
    function (
        registry,
        quote,
        ko,
        priceUtils,
        $t
    ) {
        'use strict';

        return function (origComponent) {

            if (window.isMageWorxCheckout) {
                return origComponent.extend({
                    defaults: {
                        isVisible: true,
                        template: 'MageWorx_PickupCheckout/checkout/container',
                    },

                    amount: function() {
                        if (quote.shippingMethod()) {
                            return this.getFormattedPrice(quote.shippingMethod().amount);
                        }

                        return this.getFormattedPrice(0);
                    },

                    carrierTitle: function() {
                        if (quote.shippingMethod()) {
                            return quote.shippingMethod().carrier_title;
                        }

                        return '';
                    },

                    methodTitle: function() {
                        if (quote.shippingMethod()) {
                            return quote.shippingMethod().method_title;
                        }

                        return '';
                    },

                    /**
                     * @param {*} price
                     * @return {*|String}
                     */
                    getFormattedPrice: function (price) {
                        if (price < 0.001) {
                            return $t('Free');
                        }

                        return priceUtils.formatPrice(price, quote.getPriceFormat());
                    }
                });
            }

            return origComponent;
        };
    }
);
