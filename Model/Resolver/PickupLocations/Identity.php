<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Model\Resolver\PickupLocations;

class Identity implements \Magento\Framework\GraphQl\Query\Resolver\IdentityInterface, \Magento\GraphQlResolverCache\Model\Resolver\Result\Cache\IdentityInterface
{
    public function __construct(
        protected \MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext $cacheContext
    ) {
    }

    public function getIdentities($resolvedData, ?array $parentResolvedData = null): array //phpcs:ignore
    {
        return $this->cacheContext->getIdentities();
    }
}
