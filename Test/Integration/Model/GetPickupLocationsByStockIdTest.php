<?php

namespace MageSuite\StoreLocatorGraphQl\Test\Integration\Model;

/**
 * @magentoDbIsolation  disabled
 * @magentoAppIsolation enabled
 **/

class GetPickupLocationsByStockIdTest extends \MageSuite\StoreLocatorGraphQl\Test\Integration\AbstractTestCase
{

    protected $getPickupLocationsByStockId;

    private const STOCK_ID = 30;

    private const ALL_STORES_COUNT = 5;

    private const ENABLED_PICKUP_STORES = 3;

    public function setUp(): void
    {
        parent::setUp();
        $this->getPickupLocationsByStockId = $this->objectManager->get(\MageSuite\StoreLocatorGraphQl\Model\GetPickupLocationsByStockId::class);
    }

    /**
     * @magentoDataFixture loadSourcesFixture
     * @magentoDataFixture loadStocksFixture
     * @magentoDataFixture loadStockSourceLinksFixture
     * @magentoDataFixture loadSourceItemsFixture
     * @magentoDataFixture loadWebsiteWithStoresFixture
     * @magentoDataFixture loadStockWebsiteSalesChannelsFixture
     * @magentoDataFixture loadReindexInventoryFixture
     * @magentoConfigFixture current_store store_locator/configuration/availability_mode show_enabled_only
     */
    public function testItReturnsEnabledPickupLocationsOnly()
    {
        $sources = $this->getPickupLocationsByStockId->execute(self::STOCK_ID);
        $this->assertEquals(self::ENABLED_PICKUP_STORES, count($sources), 'Failed to assert that only sources set as enabled pickup locations are returned.');
    }


    /**
     * @magentoDataFixture loadSourcesFixture
     * @magentoDataFixture loadStocksFixture
     * @magentoDataFixture loadStockSourceLinksFixture
     * @magentoDataFixture loadSourceItemsFixture
     * @magentoDataFixture loadWebsiteWithStoresFixture
     * @magentoDataFixture loadStockWebsiteSalesChannelsFixture
     * @magentoDataFixture loadReindexInventoryFixture
     * @magentoConfigFixture current_store store_locator/configuration/availability_mode show_all
     */
    public function testItReturnsAllSources()
    {
        $sources = $this->getPickupLocationsByStockId->execute(self::STOCK_ID);
        $this->assertEquals(self::ALL_STORES_COUNT, count($sources), 'Failed to assert that all sources are returned (regardless of their pickup location status).');
    }
}
