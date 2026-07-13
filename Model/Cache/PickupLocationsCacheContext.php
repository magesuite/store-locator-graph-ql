<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Model\Cache;

class PickupLocationsCacheContext implements \Magento\Framework\DataObject\IdentityInterface
{
    public const CACHE_TAG = 'store_pickup_locations';

    public function __construct(
        protected \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
    }

    public function getIdentities(): array
    {
        return [self::CACHE_TAG];
    }

    public function flush(): void
    {
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
    }
}
