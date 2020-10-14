define(
    [
        'ko',
        'underscore',
        'Magento_Checkout/js/view/billing-address'
    ],
    function (
        ko,
        _,
        Component
    ) {
        return Component.extend(
            {
                defaults: {
                    template: 'MageWorx_PickupCheckout/checkout/billing-address',
                    actionsTemplate: 'Magento_Checkout/billing-address/actions',
                    formTemplate: 'Magento_Checkout/billing-address/form',
                    detailsTemplate: 'Magento_Checkout/billing-address/details',
                    links: {
                        isAddressFormVisible: '${$.billingAddressListProvider}:isNewAddressSelected'
                    }
                },

                canUseShippingAddress: ko.pureComputed({
                    read: function () {
                        return false;
                    },
                    write: function (value) {

                    },
                    owner: this
                }),

                isAddressSameAsShipping: ko.pureComputed({
                    read: function () {
                        return false;
                    },
                    write: function (value) {

                    },
                    owner: this
                }),
            }
        );
    }
);
