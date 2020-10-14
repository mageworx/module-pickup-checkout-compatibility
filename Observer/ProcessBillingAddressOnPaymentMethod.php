<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PickupCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProcessBillingAddressOnPaymentMethod implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \MageWorx\Checkout\Block\Checkout\Onepage\LayoutProcessor $subject */
        $subject = $observer->getEvent()->getData('subject');
        $jsLayout = &$subject->getJsLayout();

        if (!isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children'])) {
            return;
        }

        $methods = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children']['payments-list']['children'];

        foreach ($methods as $index => &$method) {
            if (substr($index, -5) !== '-form') {
                continue;
            }

            $method['component'] = 'MageWorx_PickupCheckout/js/view/checkout/billing-address';
        }
    }
}
