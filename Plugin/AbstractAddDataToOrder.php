<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PickupCheckout\Plugin;

use Magento\Quote\Model\Quote as QuoteEntity;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use MageWorx\Locations\Api\LocationRepositoryInterface;

/**
 * AbstractAddDataToOrder class
 */
abstract class AbstractAddDataToOrder
{
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * CheckLocationToOrderPaypal constructor.
     *
     * @param \Magento\Framework\App\State $state
     * @param CookieManagerInterface $cookieManager
     * @param LocationRepositoryInterface $locationRepository
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        CookieManagerInterface $cookieManager,
        LocationRepositoryInterface $locationRepository
    ) {
        $this->state              = $state;
        $this->cookieManager      = $cookieManager;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param string $name
     * @return string|null
     */
    protected function getLocationIdFromCookie($name)
    {
        return $this->cookieManager->getCookie($name);
    }

    /**
     * @param QuoteEntity|\Magento\Sales\Api\Data\OrderInterface $entity
     * @return bool
     * @throws LocalizedException
     */
    protected function out($entity)
    {
        if ($this->state->getAreaCode() == 'adminhtml') {
            return true;
        }

        $methodCode = $entity->getShippingAddress()->getShippingMethod();

        if (!$methodCode) {
            $methodCode = $entity->getShippingMethod();
        }

        if ($methodCode !== 'mageworxpickup_mageworxpickup') {
            return true;
        }

        return false;
    }
}

