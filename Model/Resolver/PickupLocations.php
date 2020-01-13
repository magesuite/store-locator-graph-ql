<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Model\Resolver;

/**
 * In store pickup locations field resolver, used for GraphQL request processing.
 */
class PickupLocations implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \Magento\InventoryInStorePickup\Model\GetPickupLocationsAssignedToSalesChannel
     */
    protected $getPickupLocationsAssignedToSalesChannel;

    public function __construct(
        \Magento\InventoryInStorePickup\Model\GetPickupLocationsAssignedToSalesChannel $getPickupLocationsAssignedToSalesChannel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolver
    ) {
        $this->storeManager = $storeManager;
        $this->stockResolver = $stockResolver;
        $this->getPickupLocationsAssignedToSalesChannel = $getPickupLocationsAssignedToSalesChannel;
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
        $pickupLocations = $this->getPickupLocationsAssignedToSalesChannel->execute('website', $website->getCode());

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
                'countryId' => $pickupLocation->getCountryId(),
                'email' => $pickupLocation->getEmail(),
                'phone' => $pickupLocation->getPhone(),
                'fax' => $pickupLocation->getFax(),
                'url' => $pickupLocation->getExtensionAttributes()->getUrl(),
            ];
        }

        return ['items' => $items];
    }
}
