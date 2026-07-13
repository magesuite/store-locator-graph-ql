<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Observer;

class FlushPickupLocationsCacheOnConfigChange implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        protected \MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext $cacheContext
    ) {
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $changedPaths = (array) $observer->getEvent()->getChangedPaths();

        $watchedPaths = [
            \MageSuite\StoreLocatorGraphQl\Helper\Configuration::STORE_LOCATIONS_SOURCE_PATH,
            \MageSuite\StoreLocatorGraphQl\Helper\Configuration::STORE_LOCATIONS_AVAILABILITY_MODE,
        ];

        if (empty(array_intersect($watchedPaths, $changedPaths))) {
            return;
        }

        $this->cacheContext->flush();
    }
}
