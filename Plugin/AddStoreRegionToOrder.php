<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PickupCheckout\Plugin;

use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;

class AddStoreRegionToOrder extends AbstractAddDataToOrder
{
    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $entity
     * @param OrderInterface $order
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave(
        \Magento\Sales\Api\OrderRepositoryInterface $entity,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $locationId = $this->getLocationIdFromCookie('mageworx_location_id');

        if ($this->out($order) || !$locationId) {
            return [$order];
        }

        $shippingAddress = $order->getShippingAddress();
        $location = $this->locationRepository->getById($locationId);

        $order->setShippingAddress($this->prepareAddress($shippingAddress, $location));

        if ($shippingAddress->getSameAsBilling()) {
            $billingAddress = $order->getBillingAddress();
            $order->setBillingAddress($this->prepareAddress($billingAddress, $location));
        }

        return [$order];
    }

    /**
     * @param $address
     * @param $location
     * @return mixed
     */
    private function prepareAddress($address, $location)
    {
        $address->setRegionId(null);
        $address->setRegionCode(null);
        $address->setRegion($location->getRegion());

        return $address;
    }
}
