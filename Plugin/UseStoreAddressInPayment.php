<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PickupCheckout\Plugin;

use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use MageWorx\Locations\Api\Data\LocationInterface;
use MageWorx\Locations\Api\LocationRepositoryInterface;

/**
 * AddStoreAddressToOrder class
 */
class UseStoreAddressInPayment extends AbstractAddDataToOrder
{
    private CartRepositoryInterface $quoteRepository;

    public function __construct(
        CartRepositoryInterface     $quoteRepository,
        State                       $state,
        CookieManagerInterface      $cookieManager,
        LocationRepositoryInterface $locationRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        parent::__construct($state, $cookieManager, $locationRepository);
    }

    /**
     * @param PaymentInformationManagementInterface $paymentInformationManagement
     * @param $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @return array
     * @throws LocalizedException
     */
    public function beforeSavePaymentInformation(
        PaymentInformationManagementInterface $paymentInformationManagement,
                                              $cartId,
        PaymentInterface                      $paymentMethod,
        ?AddressInterface                     $billingAddress = null
    ) {
        $locationId = $this->getLocationIdFromCookie('mageworx_location_id');
        $quote      = $this->quoteRepository->get($cartId);
        if ($this->out($quote) || !$locationId || $billingAddress === null) {
            return [$cartId, $paymentMethod, $billingAddress];
        }

        /** @var LocationInterface $location */
        $location = $this->locationRepository->getById($locationId);

        $billingAddress = $this->prepareAddress($billingAddress, $location);

        return [$cartId, $paymentMethod, $billingAddress];
    }

    /**
     * @param AddressInterface $address
     * @param LocationInterface $location
     * @return AddressInterface
     */
    private function prepareAddress(
        AddressInterface  $address,
        LocationInterface $location
    ): AddressInterface {
        $address->setFax('');
        $address->setStreetFull('');
        $address->setPostcode($location->getPostcode());
        $address->setStreet($location->getAddress());
        $address->setCity($location->getCity());

        return $address;
    }
}

