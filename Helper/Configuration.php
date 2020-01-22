<?php

namespace MageSuite\StoreLocatorGraphQl\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const STORE_LOCATIONS_SOURCE_PATH = 'store_locator/configuration/stock_id';

    public function getStoreLocationsSource()
    {
        return $this->scopeConfig->getValue(self::STORE_LOCATIONS_SOURCE_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
