define(
    [
        'ko',
        'underscore',
        'uiComponent'
    ],
    function (
        ko,
        _,
        Component
    ) {
        return Component.extend(
            {
                defaults: {
                    template: 'MageWorx_PickupCheckout/checkout/select-store-container',
                    visible: false
                },

                observableProperties: [
                    'visible'
                ],

                initObservable: function () {
                    this._super();
                    this.observe(this.observableProperties);

                    return this;
                }
            }
        );
    }
);
