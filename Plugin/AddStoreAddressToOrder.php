<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PickupCheckout\Plugin;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote as QuoteEntity;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\Locations\Api\Data\LocationInterface;

/**
 * AddStoreAddressToOrder class
 */
class AddStoreAddressToOrder extends AbstractAddDataToOrder
{
    /**
     * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
     * @param QuoteEntity $quote
     * @param array $orderData
     * @return array
     * @throws LocalizedException
     */
    public function beforeSubmit(
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        QuoteEntity $quote,
        $orderData = []
    ) {
        $locationId = $this->getLocationIdFromCookie('mageworx_location_id');

        if ($this->out($quote) || !$locationId) {
            return [$quote, $orderData];
        }

        $shippingAddress = $quote->getShippingAddress();
        /** @var LocationInterface $location */
        $location = $this->locationRepository->getById($locationId);

        $quote->setShippingAddress($this->prepareAddress($shippingAddress, $location));
        $quote->getShippingAddress()->setShouldIgnoreValidation(true);

        $billingAddress = $quote->getBillingAddress();
        $billingAddress->setShouldIgnoreValidation(true);
        if ($shippingAddress->getSameAsBilling()) {
            $quote->setBillingAddress($this->prepareAddress($billingAddress, $location));
        } else {
            $quote->setBillingAddress($billingAddress);
        }

        return [$quote, $orderData];
    }

    /**
     * @param AddressInterface $address
     * @param LocationInterface $location
     * @return AddressInterface
     */
    private function prepareAddress(
        AddressInterface $address,
        LocationInterface $location
    ): AddressInterface {
        //$address->setId('');
        $address->setFax('');
        $address->setStreetFull('');
        $address->setCountryId($location->getCountryId());
        $address->setPostcode($location->getPostcode());
        $address->setStreet($location->getAddress());
        $address->setCity($location->getCity());


        return $address;
    }
}

