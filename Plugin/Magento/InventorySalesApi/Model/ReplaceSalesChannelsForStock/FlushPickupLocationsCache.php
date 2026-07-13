<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Plugin\Magento\InventorySalesApi\Model\ReplaceSalesChannelsForStock;

class FlushPickupLocationsCache
{
    public function __construct(
        protected \MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext $cacheContext
    ) {
    }

    public function afterExecute(): void
    {
        $this->cacheContext->flush();
    }
}
