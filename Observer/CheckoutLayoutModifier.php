<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PickupCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CheckoutLayoutModifier
 *
 * Move pickup elements on the MageWorx_Checkout page;
 */
class CheckoutLayoutModifier implements ObserverInterface
{

    /**
     * @var \Magento\Checkout\Model\Session\Proxy
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session\Proxy $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \MageWorx\Checkout\Api\LayoutModifierAccess $subject */
        $subject = $observer->getSubject();
        /** @var array $jsLayout */
        $jsLayout = &$subject->getJsLayout();

        // Copy element
        $originalElement = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['pickupInformation']['children']['shippingAdditional'];

        if ($this->checkoutSession->getQuote() && $this->checkoutSession->getQuote()->getIsVirtual()) {
            // Remove pickup for virtual quote and return
            unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                  ['children']['pickupInformation']);

            return;
        }

        $originalElement['component'] = 'MageWorx_PickupCheckout/js/view/checkout/select-store-container';
        $originalElement['config']['template'] = 'MageWorx_PickupCheckout/checkout/select-store-container';

        // Remove original element from layout
        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['pickupInformation']['children']['shippingAdditional']
        );

        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['pickup_container'] = $originalElement;

        //copy delivery date to pickup tab
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingMethods']['children']['shipping_method_additional_data']['children']['delivery_date'])) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['pickup_container']['children']['mageworxpickup']['children']['shipping_method_additional_data']['children']
            ['delivery_date'] = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingMethods']['children']['shipping_method_additional_data']['children']['delivery_date'];

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['pickup_container']['children']['mageworxpickup']['children']['shipping_method_additional_data']['children']
            ['delivery_date']['config']['isVisible'] = true;
        }
    }
}
