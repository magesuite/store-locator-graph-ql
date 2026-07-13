<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Plugin\Magento\InventoryApi\StockRepository;

class FlushPickupLocationsCacheOnDelete
{
    public function __construct(
        protected \MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext $cacheContext
    ) {
    }

    public function afterDeleteById(): void
    {
        $this->cacheContext->flush();
    }
}
