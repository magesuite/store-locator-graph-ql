<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Plugin\MageSuite\StoreLocator\Import\Source;

class FlushPickupLocationsCacheAfterImport
{
    public function __construct(
        protected \MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext $cacheContext
    ) {
    }

    public function afterImportData(\MageSuite\StoreLocator\Model\Import\Source $subject, bool $result): bool 
    {
        $this->cacheContext->flush();

        return $result;
    }
}
