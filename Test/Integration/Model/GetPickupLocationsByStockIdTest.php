<?php

declare(strict_types=1);

namespace MageSuite\StoreLocatorGraphQl\Test\Integration\Model;

/**
 * @magentoDbIsolation  disabled
 * @magentoAppIsolation enabled
 **/

class GetPickupLocationsByStockIdTest extends \PHPUnit\Framework\TestCase
{
    protected \MageSuite\StoreLocatorGraphQl\Model\GetPickupLocationsByStockId $getPickupLocationsByStockId;
    private const STOCK_ID = 30;
    private const ALL_STORES_COUNT = 5;
    private const ENABLED_PICKUP_STORES = 3;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->getPickupLocationsByStockId = $objectManager->get(\MageSuite\StoreLocatorGraphQl\Model\GetPickupLocationsByStockId::class);
    }

    /**
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/sources.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stocks.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stock_source_links.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/source_items.php
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/websites_with_stores.php
     * @magentoDataFixture Magento_InventorySalesApi::Test/_files/stock_website_sales_channels.php
     * @magentoDataFixture Magento_InventoryIndexer::Test/_files/reindex_inventory.php
     * @magentoConfigFixture current_store store_locator/configuration/availability_mode show_enabled_only
     */
    public function testItReturnsEnabledPickupLocationsOnly(): void
    {
        $sources = $this->getPickupLocationsByStockId->execute(self::STOCK_ID);
        $this->assertEquals(self::ENABLED_PICKUP_STORES, count($sources), 'Failed to assert that only sources set as enabled pickup locations are returned.');
    }

    /**
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/sources.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stocks.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stock_source_links.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/source_items.php
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/websites_with_stores.php
     * @magentoDataFixture Magento_InventorySalesApi::Test/_files/stock_website_sales_channels.php
     * @magentoDataFixture Magento_InventoryIndexer::Test/_files/reindex_inventory.php
     * @magentoConfigFixture current_store store_locator/configuration/availability_mode show_all
     */
    public function testItReturnsAllSources(): void
    {
        $sources = $this->getPickupLocationsByStockId->execute(self::STOCK_ID);
        $this->assertEquals(self::ALL_STORES_COUNT, count($sources), 'Failed to assert that all sources are returned (regardless of their pickup location status).');
    }

    /**
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/sources.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stocks.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stock_source_links.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/source_items.php
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/websites_with_stores.php
     * @magentoDataFixture Magento_InventorySalesApi::Test/_files/stock_website_sales_channels.php
     * @magentoDataFixture Magento_InventoryIndexer::Test/_files/reindex_inventory.php
     * @magentoConfigFixture current_store store_locator/configuration/availability_mode show_enabled_only
     */
    public function testItReturnsAllEnabledSourcesForEmptyQuery(): void
    {
        $sources1 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID);
        $sources2 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, '');
        $sources3 = $this->getPickupLocationsByStockId->execute(self::STOCK_ID, '  ');

        $this->assertEquals(3, count($sources1));
        $this->assertEquals(3, count($sources2));
        $this->assertEquals(0, count($sources3));
    }

    /**
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/sources.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stocks.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stock_source_links.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/source_items.php
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/websites_with_stores.php
     * @magentoDataFixture Magento_InventorySalesApi::Test/_files/stock_website_sales_channels.php
     * @magentoDataFixture Magento_InventoryIndexer::Test/_files/reindex_inventory.php
     * @magentoConfigFixture current_store store_locator/configuration/availability_mode show_enabled_only
     */
    public function testItReturnsSourcesForQueryWhichRelatesToCity(): void
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
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/sources.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stocks.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stock_source_links.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/source_items.php
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/websites_with_stores.php
     * @magentoDataFixture Magento_InventorySalesApi::Test/_files/stock_website_sales_channels.php
     * @magentoDataFixture Magento_InventoryIndexer::Test/_files/reindex_inventory.php
     * @magentoConfigFixture current_store store_locator/configuration/availability_mode show_enabled_only
     */
    public function testItReturnsSourcesForQueryWhichRelatesToPostcode(): void
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
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/sources.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stocks.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/stock_source_links.php
     * @magentoDataFixture Magento_InventoryApi::Test/_files/source_items.php
     * @magentoDataFixture MageSuite_StoreLocatorGraphQl::Test/Integration/_files/websites_with_stores.php
     * @magentoDataFixture Magento_InventorySalesApi::Test/_files/stock_website_sales_channels.php
     * @magentoDataFixture Magento_InventoryIndexer::Test/_files/reindex_inventory.php
     * @magentoConfigFixture current_store store_locator/configuration/availability_mode show_enabled_only
     */
    public function testItReturnsSourcesForQueryWithAccentsWhichRelatesToCity(): void
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
