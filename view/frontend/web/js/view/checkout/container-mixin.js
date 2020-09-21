/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'uiRegistry',
        'ko'
    ],
    function (registry, ko) {
        'use strict';

        return function (origComponent) {

            if (window.isMageWorxCheckout) {
                return origComponent.extend({
                    defaults: {
                        isVisible: true,
                        template: 'MageWorx_PickupCheckout/checkout/container',
                    },
                });
            }

            return origComponent;
        };
    }
);
