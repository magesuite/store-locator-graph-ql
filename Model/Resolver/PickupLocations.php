<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Model\Resolver;

/**
 * In store pickup locations field resolver, used for GraphQL request processing.
 */
class PickupLocations implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \Magento\InventorySalesApi\Api\StockResolverInterface
     */
    private $stockResolver;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\InventoryInStorePickup\Model\GetPickupLocationsAssignedToStockOrderedByPriority
     */
    private $getPickupLocationsAssignedToStockOrderedByPriority;

    /**
     * @param \Magento\InventoryInStorePickup\Model\GetPickupLocationsAssignedToStockOrderedByPriority $getPickupLocationsAssignedToStockOrderedByPriority
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolver
     */
    public function __construct(
        \Magento\InventoryInStorePickup\Model\GetPickupLocationsAssignedToStockOrderedByPriority $getPickupLocationsAssignedToStockOrderedByPriority,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolver
    ) {
        $this->getPickupLocationsAssignedToStockOrderedByPriority = $getPickupLocationsAssignedToStockOrderedByPriority;
        $this->storeManager = $storeManager;
        $this->stockResolver = $stockResolver;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $website = $this->storeManager->getWebsite();
        $stock = $this->stockResolver->execute(\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE, $website->getCode());

        $pickupLocations = $this->getPickupLocationsAssignedToStockOrderedByPriority->execute($stock->getStockId());

        $items = [];

        if(empty($pickupLocations)) {
            return ['items' => $items];
        }

        /** @var \Magento\InventoryInStorePickup\Model\PickupLocation $pickupLocation */
        foreach($pickupLocations as $pickupLocation) {
            $items[] = [
                'name' => $pickupLocation->getName(),
                'contactName' => $pickupLocation->getContactName(),
                'latitude' => $pickupLocation->getLatitude(),
                'longitude' => $pickupLocation->getLongitude(),
                'sourceCode' => $pickupLocation->getSourceCode(),
                'city' => $pickupLocation->getCity(),
                'street' => $pickupLocation->getStreet(),
                'description' => $pickupLocation->getDescription(),
                'postCode' => $pickupLocation->getPostcode(),
                'countryId' => $pickupLocation->getCountryId()
            ];
        }

        return ['items' => $items];
    }
}
