<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Model\Resolver;

/**
 * In store pickup locations field resolver, used for GraphQL request processing.
 */
class PickupLocations implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\InventorySalesApi\Api\StockResolverInterface
     */
    protected $stockResolver;

    /**
     * @var \MageSuite\StoreLocatorGraphQl\Model\GetPickupLocationsByStockId
     */
    protected $getPickupLocationsByStockId;

    /**
     * @var \MageSuite\StoreLocatorGraphQl\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolver,
        \MageSuite\StoreLocatorGraphQl\Model\GetPickupLocationsByStockId $getPickupLocationsByStockId,
        \MageSuite\StoreLocatorGraphQl\Helper\Configuration $configuration,
        \Psr\Log\LoggerInterface $logger
    ) {

        $this->storeManager = $storeManager;
        $this->stockResolver = $stockResolver;
        $this->getPickupLocationsByStockId = $getPickupLocationsByStockId;
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $query = $args['query'] ?? '';

        $website = $this->storeManager->getWebsite();

        $stockIdFromConfiguration = $this->configuration->getStoreLocationsSource();

        $stockId = $stockIdFromConfiguration ?
            $stockIdFromConfiguration :
            $this->stockResolver->execute('website', $website->getCode())->getStockId();

        $pickupLocations = $this->getPickupLocationsByStockId->execute((int)$stockId, $query);

        $items = [];

        if (empty($pickupLocations)) {
            return ['items' => $items];
        }

        /** @var \Magento\InventoryInStorePickup\Model\PickupLocation $pickupLocation */
        foreach ($pickupLocations as $pickupLocation) {
            if ($pickupLocation->getLatitude() === null || $pickupLocation->getLongitude() === null) {
                $this->logger->warning(
                    sprintf(
                        'Pickup location "%s" skipped: missing latitude or longitude.',
                        $pickupLocation->getPickupLocationCode()
                    )
                );
                continue;
            }

            $items[] = $this->mapPickupLocation($pickupLocation);
        }

        return ['items' => $items];
    }

    public function mapPickupLocation(\Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface $pickupLocation)
    {
        return [
            'name' => $pickupLocation->getName(),
            'contactName' => $pickupLocation->getContactName(),
            'latitude' => $pickupLocation->getLatitude(),
            'longitude' => $pickupLocation->getLongitude(),
            'sourceCode' => $pickupLocation->getPickupLocationCode(),
            'pickupLocationCode' => $pickupLocation->getPickupLocationCode(),
            'city' => $pickupLocation->getCity(),
            'street' => $pickupLocation->getStreet(),
            'region' => $pickupLocation->getRegion(),
            'description' => $pickupLocation->getDescription(),
            'postCode' => $pickupLocation->getPostcode(),
            'countryId' => $pickupLocation->getCountryId(),
            'email' => $pickupLocation->getEmail(),
            'phone' => $pickupLocation->getPhone(),
            'fax' => $pickupLocation->getFax(),
            'url' => $pickupLocation->getExtensionAttributes()->getUrl(),
        ];
    }
}
