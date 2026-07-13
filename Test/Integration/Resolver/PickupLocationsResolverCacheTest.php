<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Test\Integration\Resolver;

class PickupLocationsResolverCacheTest extends \PHPUnit\Framework\TestCase
{
    protected \Magento\GraphQl\Service\GraphQlRequest $graphQlRequest;
    protected \Magento\GraphQlResolverCache\Model\Resolver\Result\Type $resolverCache;
    protected \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository;
    protected \Magento\Framework\Serialize\SerializerInterface $serializer;
    protected \Magento\Inventory\Model\Source\Command\GetSourcesAssignedToStockOrderedByPriorityCache $getSourcesAssignedToStockOrderedByPriorityCache;
    protected \Magento\Framework\Event\ManagerInterface $eventManager;
    protected \Magento\InventorySalesApi\Model\ReplaceSalesChannelsForStockInterface $replaceSalesChannelsForStock;
    protected \Magento\InventoryApi\Api\StockRepositoryInterface $stockRepository;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->getSourcesAssignedToStockOrderedByPriorityCache = $objectManager->get(\Magento\Inventory\Model\Source\Command\GetSourcesAssignedToStockOrderedByPriorityCache::class);
        $this->graphQlRequest = $objectManager->get(\Magento\GraphQl\Service\GraphQlRequest::class);
        $this->resolverCache = $objectManager->get(\Magento\GraphQlResolverCache\Model\Resolver\Result\Type::class);
        $this->sourceRepository = $objectManager->get(\Magento\InventoryApi\Api\SourceRepositoryInterface::class);
        $this->serializer = $objectManager->get(\Magento\Framework\Serialize\SerializerInterface::class);
        $this->eventManager = $objectManager->get(\Magento\Framework\Event\ManagerInterface::class);
        $this->replaceSalesChannelsForStock = $objectManager->get(\Magento\InventorySalesApi\Model\ReplaceSalesChannelsForStockInterface::class);
        $this->stockRepository = $objectManager->get(\Magento\InventoryApi\Api\StockRepositoryInterface::class);
    }

    #[\Magento\TestFramework\Fixture\DbIsolation(false)]
    #[\Magento\TestFramework\Fixture\AppIsolation(true)]
    #[\Magento\TestFramework\Fixture\DataFixture(\Magento\InventoryApi\Test\Fixture\Stock::class, [
        \Magento\InventoryApi\Api\Data\StockInterface::STOCK_ID => 99
    ])]
    #[\Magento\TestFramework\Fixture\DataFixture(\Magento\InventoryApi\Test\Fixture\Source::class,[
        \Magento\InventoryApi\Api\Data\SourceInterface::SOURCE_CODE => 'eu-1',
        \Magento\InventoryApi\Api\Data\SourceInterface::NAME => 'EU-source-1',
        \Magento\InventoryApi\Api\Data\SourceInterface::ENABLED => true,
        \Magento\InventoryApi\Api\Data\SourceInterface::COUNTRY_ID => 'DE',
        \Magento\InventoryApi\Api\Data\SourceInterface::POSTCODE => '10115',
        \Magento\InventoryApi\Api\Data\SourceInterface::CITY => 'Berlin',
        \Magento\InventoryApi\Api\Data\SourceInterface::STREET => 'Invalidenstraße 117',
        \Magento\InventoryApi\Api\Data\SourceInterface::LATITUDE => 52.5312,
        \Magento\InventoryApi\Api\Data\SourceInterface::LONGITUDE => 13.3847,
        \Magento\InventoryApi\Api\Data\SourceInterface::PHONE => '+49 30 123456789',
        \Magento\InventoryApi\Api\Data\SourceInterface::EXTENSION_ATTRIBUTES_KEY => [
            \Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface::IS_PICKUP_LOCATION_ACTIVE => 1
        ]
    ])]
    #[\Magento\TestFramework\Fixture\DataFixture(\Magento\InventoryApi\Test\Fixture\StockSourceLinks::class, [
           [
               \Magento\InventoryApi\Api\Data\StockSourceLinkInterface::STOCK_ID => 99,
               \Magento\InventoryApi\Api\Data\StockSourceLinkInterface::SOURCE_CODE => 'eu-1'
           ]
    ])]
    #[\Magento\TestFramework\Fixture\AppArea(\Magento\Framework\App\Area::AREA_GRAPHQL)]
    #[\Magento\TestFramework\Fixture\Cache('graphql_query_resolver_result', true)]
    #[\Magento\TestFramework\Fixture\Config(\MageSuite\StoreLocatorGraphQl\Helper\Configuration::STORE_LOCATIONS_SOURCE_PATH, 99, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 'default')]
    #[\Magento\TestFramework\Fixture\Config(\MageSuite\StoreLocatorGraphQl\Helper\Configuration::STORE_LOCATIONS_AVAILABILITY_MODE, \MageSuite\StoreLocatorGraphQl\Model\Config\Source\AvailabilityMode::STORES_AVAILABILITY_MODE_SHOW_ALL)]
    public function testResolverResultIsCachedAndInvalidatedOnSourceSave(): void
    {
        $this->resolverCache->clean();

        $this->resolverCache->save(
            $this->serializer->serialize(['marker' => true]),
            'pickup_locations_resolver_marker',
            [\MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext::CACHE_TAG]
        );

        $response = $this->graphQlRequest->send($this->getQuery());
        $body = $this->serializer->unserialize((string) $response->getBody());

        $this->assertResponse($body, [
            [
                'name' => 'EU-source-1',
                'city' => 'Berlin',
                'sourceCode' => 'eu-1'
            ]
        ]);

        $this->assertNotFalse(
            $this->resolverCache->load('pickup_locations_resolver_marker'),
            'Resolver cache marker must be present before source save — GraphQL query must not flush the cache'
        );

        $source = $this->sourceRepository->get('eu-1');
        $source->getExtensionAttributes()->setFrontendName('EU-source-1-updated');
        $this->sourceRepository->save($source);

        $this->assertFalse(
            $this->resolverCache->load('pickup_locations_resolver_marker'),
            'Resolver cache must be invalidated after source save'
        );

        $this->getSourcesAssignedToStockOrderedByPriorityCache->_resetState();
        $updatedResponse = $this->graphQlRequest->send($this->getQuery());
        $updatedBody = $this->serializer->unserialize((string) $updatedResponse->getBody());

        $this->assertResponse($updatedBody, [
            [
                'name' => 'EU-source-1-updated',
                'city' => 'Berlin',
                'sourceCode' => 'eu-1'
            ]
        ]);
    }

    #[\Magento\TestFramework\Fixture\AppArea(\Magento\Framework\App\Area::AREA_GRAPHQL)]
    #[\Magento\TestFramework\Fixture\Cache('graphql_query_resolver_result', true)]
    public function testResolverResultIsInvalidatedOnAvailabilityModeConfigChange(): void
    {
        $this->assertMarkerFlushedOnConfigChange([
            \MageSuite\StoreLocatorGraphQl\Helper\Configuration::STORE_LOCATIONS_AVAILABILITY_MODE
        ]);
    }

    #[\Magento\TestFramework\Fixture\AppArea(\Magento\Framework\App\Area::AREA_GRAPHQL)]
    #[\Magento\TestFramework\Fixture\Cache('graphql_query_resolver_result', true)]
    public function testResolverResultIsInvalidatedOnStockIdConfigChange(): void
    {
        $this->assertMarkerFlushedOnConfigChange([
            \MageSuite\StoreLocatorGraphQl\Helper\Configuration::STORE_LOCATIONS_SOURCE_PATH
        ]);
    }

    #[\Magento\TestFramework\Fixture\AppArea(\Magento\Framework\App\Area::AREA_GRAPHQL)]
    #[\Magento\TestFramework\Fixture\Cache('graphql_query_resolver_result', true)]
    public function testResolverResultIsNotInvalidatedOnUnrelatedConfigChange(): void
    {
        $this->resolverCache->clean();

        $this->resolverCache->save(
            $this->serializer->serialize(['marker' => true]),
            'pickup_locations_resolver_marker',
            [\MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext::CACHE_TAG]
        );

        $this->eventManager->dispatch('admin_system_config_changed_section_store_locator', [
            'changed_paths' => [\MageSuite\StoreLocator\Helper\Configuration::META_DESCRIPTION_CONFIG_PATH]
        ]);

        $this->assertNotFalse(
            $this->resolverCache->load('pickup_locations_resolver_marker'),
            'Resolver cache must not be invalidated by unrelated config changes'
        );
    }

    #[\Magento\TestFramework\Fixture\DataFixture(\Magento\InventoryApi\Test\Fixture\Stock::class, [
        \Magento\InventoryApi\Api\Data\StockInterface::STOCK_ID => 98
    ])]
    #[\Magento\TestFramework\Fixture\AppArea(\Magento\Framework\App\Area::AREA_GRAPHQL)]
    #[\Magento\TestFramework\Fixture\Cache('graphql_query_resolver_result', true)]
    public function testResolverResultIsInvalidatedOnStockSalesChannelsChange(): void
    {
        $this->resolverCache->clean();

        $this->resolverCache->save(
            $this->serializer->serialize(['marker' => true]),
            'pickup_locations_resolver_marker',
            [\MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext::CACHE_TAG]
        );

        $this->assertNotFalse(
            $this->resolverCache->load('pickup_locations_resolver_marker'),
            'Resolver cache marker must be present before stock sales channels change'
        );

        $this->replaceSalesChannelsForStock->execute([], 98);

        $this->assertFalse(
            $this->resolverCache->load('pickup_locations_resolver_marker'),
            'Resolver cache must be invalidated after stock sales channels (website assignment) change'
        );
    }

    #[\Magento\TestFramework\Fixture\DataFixture(\Magento\InventoryApi\Test\Fixture\Stock::class, [
        \Magento\InventoryApi\Api\Data\StockInterface::STOCK_ID => 97
    ])]
    #[\Magento\TestFramework\Fixture\AppArea(\Magento\Framework\App\Area::AREA_GRAPHQL)]
    #[\Magento\TestFramework\Fixture\Cache('graphql_query_resolver_result', true)]
    public function testResolverResultIsInvalidatedOnStockDelete(): void
    {
        $this->resolverCache->clean();

        $this->resolverCache->save(
            $this->serializer->serialize(['marker' => true]),
            'pickup_locations_resolver_marker',
            [\MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext::CACHE_TAG]
        );

        $this->assertNotFalse(
            $this->resolverCache->load('pickup_locations_resolver_marker'),
            'Resolver cache marker must be present before stock delete'
        );

        $this->stockRepository->deleteById(97);

        $this->assertFalse(
            $this->resolverCache->load('pickup_locations_resolver_marker'),
            'Resolver cache must be invalidated after stock delete'
        );
    }

    protected function assertMarkerFlushedOnConfigChange(array $changedPaths): void
    {
        $this->resolverCache->clean();

        $this->resolverCache->save(
            $this->serializer->serialize(['marker' => true]),
            'pickup_locations_resolver_marker',
            [\MageSuite\StoreLocatorGraphQl\Model\Cache\PickupLocationsCacheContext::CACHE_TAG]
        );

        $this->assertNotFalse(
            $this->resolverCache->load('pickup_locations_resolver_marker'),
            'Resolver cache marker must be present before config change'
        );

        $this->eventManager->dispatch('admin_system_config_changed_section_store_locator', [
            'changed_paths' => $changedPaths
        ]);

        $this->assertFalse(
            $this->resolverCache->load('pickup_locations_resolver_marker'),
            'Resolver cache must be invalidated after store-locator config change'
        );
    }

    protected function assertResponse(array $response, array $itemsData): void
    {
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('storePickupLocations', $response['data']);
        $this->assertArrayHasKey('items', $response['data']['storePickupLocations']);
        $this->assertCount(count($itemsData), $response['data']['storePickupLocations']['items']);
        $items = $response['data']['storePickupLocations']['items'];
        foreach ($items as $index => $item) {
            $dataToCheck = array_filter($item, fn(string $key) => in_array($key, array_keys($itemsData[$index])), ARRAY_FILTER_USE_KEY);
            $this->assertEquals($itemsData[$index], $dataToCheck);
        }
    }

    protected function getQuery(): string
    {
        return '{
            storePickupLocations {
                items {
                    name
                    city
                    sourceCode
                }
            }
        }';
    }
}
