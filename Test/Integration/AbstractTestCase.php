<?php

namespace MageSuite\StoreLocatorGraphQl\Test\Integration;

class AbstractTestCase extends \PHPUnit\Framework\TestCase {

    public function setUp(): void
    {
        parent::setUp();
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
    }

    public static function loadProductsFixture()
    {
        include __DIR__ . "/../../../../magento/module-inventory-api/Test/_files/products.php";
    }

    public static function loadSourcesFixture()
    {
        include __DIR__ . "/_files/sources.php";
    }

    public static function loadStocksFixture()
    {
        include __DIR__ . "/../../../../magento/module-inventory-api/Test/_files/stocks.php";
    }

    public static function loadStockSourceLinksFixture()
    {
        include __DIR__ . "/../../../../magento/module-inventory-api/Test/_files/stock_source_links.php";
    }

    public static function loadSourceItemsFixture()
    {
        include __DIR__ . "/../../../../magento/module-inventory-api/Test/_files/source_items.php";
    }

    public static function loadWebsiteWithStoresFixture()
    {
        include __DIR__ . "/_files/websites_with_stores.php";
    }

    public static function loadStockWebsiteSalesChannelsFixture()
    {
        include __DIR__ . "/../../../../magento/module-inventory-sales-api/Test/_files/stock_website_sales_channels.php";
    }

    public static function loadQuoteFixture()
    {
        include __DIR__ . "/../../../../magento/module-inventory-sales-api/Test/_files/quote.php";
    }

    public static function loadReindexInventoryFixture()
    {
        include __DIR__ . "/../../../../magento/module-inventory-indexer/Test/_files/reindex_inventory.php";
    }

    /**
     * Rollbacks
     */

    public static function loadProductsFixtureRollback()
    {
        include __DIR__ . "/../../../../magento/module-inventory-api/Test/_files/products_rollback.php";
    }

    public static function loadSourcesFixtureRollback()
    {
        include __DIR__ . "/../../../../magento/module-inventory-api/Test/_files/sources_rollback.php";
    }

    public static function loadStocksFixtureRollback()
    {
        include __DIR__ . "/../../../../magento/module-inventory-api/Test/_files/stocks_rollback.php";
    }

    public static function loadStockSourceLinksFixtureRollback()
    {
        include __DIR__ . "/../../../../magento/module-inventory-api/Test/_files/stock_source_links_rollback.php";
    }

    public static function loadSourceItemsFixtureRollback()
    {
        include __DIR__ . "/../../../../magento/module-inventory-api/Test/_files/source_items_rollback.php";
    }

    public static function loadStockWebsiteSalesChannelsFixtureRollback()
    {
        include __DIR__ . "/../../../../magento/module-inventory-sales-api/Test/_files/stock_website_sales_channels_rollback.php";
    }

    public static function loadWebsiteWithStoresFixtureRollback()
    {
        include __DIR__ . "/_files/websites_with_stores_rollback.php";
    }

    public static function loadQuoteFixtureRollback()
    {
        include __DIR__ . "/../../../../magento/module-inventory-sales-api/Test/_files/quote_rollback.php";
    }

    public static function loadReindexInventoryFixtureRollback()
    {
        include __DIR__ . "/../../../../magento/module-inventory-indexer/Test/_files/reindex_inventory.php";
    }
}
