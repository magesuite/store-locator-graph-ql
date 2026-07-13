<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Plugin\Magento\InventoryApi\SourceRepository;

class FlushPickupLocationsCache
{
    public function __construct(
        protected \MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext $cacheContext
    ) {
    }

    public function afterSave(): void {
        $this->cacheContext->flush();
    }
}
