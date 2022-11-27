<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PickupCheckout\Plugin;

use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote as QuoteEntity;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\Locations\Api\Data\LocationInterface;
use MageWorx\Locations\Api\LocationRepositoryInterface;

/**
 * AddStoreAddressToOrder class
 */
class UseStoreAddressInPayment extends AbstractAddDataToOrder
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\App\State $state
     * @param CookieManagerInterface $cookieManager
     * @param LocationRepositoryInterface $locationRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\App\State $state,
        CookieManagerInterface $cookieManager,
        LocationRepositoryInterface $locationRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        parent::__construct($state, $cookieManager, $locationRepository);
    }

    /**
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement
     * @param $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @return array
     * @throws LocalizedException
     */
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null

    ) {
        $locationId = $this->getLocationIdFromCookie('mageworx_location_id');
        $quote = $this->quoteRepository->get($cartId);
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
        AddressInterface $address,
        LocationInterface $location
    ): AddressInterface {
        //$address->setId('');
        $address->setFax('');
        $address->setStreetFull('');
        $address->setPostcode($location->getPostcode());
        $address->setStreet($location->getAddress());
        $address->setCity($location->getCity());


        return $address;
    }
}

