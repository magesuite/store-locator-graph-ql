<?php

namespace MageSuite\StoreLocatorGraphQl\Test\Integration\Model;

/**
 * @magentoDbIsolation  disabled
 * @magentoAppIsolation enabled
 **/

class GetPickupLocationsByStockIdTest extends \MageSuite\StoreLocatorGraphQl\Test\Integration\AbstractTestCase
{
    /**
     * @var \MageSuite\StoreLocatorGraphQl\Model\GetPickupLocationsByStockId
     */
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
    public function testItReturnsAllEnabledSourcesForEmptyQuery()
    {
        $sources1 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID);
        $sources2 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, '');
        $sources3 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, '  ');

        $this->assertEquals(3, count($sources1));
        $this->assertEquals(3, count($sources2));
        $this->assertEquals(0, count($sources3));
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
    public function testItReturnsSourcesForQueryWhichRelatesToCity()
    {
        $sources1 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'city');
        $sources2 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'CITY');
        $sources3 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'city-2');
        $sources4 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'ci');
        $sources5 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'ty');
        $sources6 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'ty-2');

        $this->assertEquals(2, count($sources1));
        $this->assertEquals(2, count($sources2));
        $this->assertEquals(1, count($sources3));
        $this->assertEquals(2, count($sources4));
        $this->assertEquals(0, count($sources5));
        $this->assertEquals(0, count($sources6));
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
    public function testItReturnsSourcesForQueryWhichRelatesToPostcode()
    {
        $sources1 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'postcode-3');
        $sources2 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'POSTCODE-3');
        $sources3 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'post');
        $sources4 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'code-3');

        $this->assertEquals(1, count($sources1));
        $this->assertEquals(0, count($sources2));
        $this->assertEquals(0, count($sources3));
        $this->assertEquals(0, count($sources4));
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
    public function testItReturnsSourcesForQueryWithAccentsWhichRelatesToCity()
    {
        $sources1 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'Königsbrück');
        $sources2 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'Konigsbruck');
        $sources3 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'König');
        $sources4 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, 'konig');

        $this->assertEquals(1, count($sources1));
        $this->assertEquals(1, count($sources2));
        $this->assertEquals(1, count($sources3));
        $this->assertEquals(1, count($sources4));
    }
}
