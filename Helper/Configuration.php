<?php

namespace MageSuite\StoreLocatorGraphQl\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const STORE_LOCATIONS_SOURCE_PATH = 'store_locator/configuration/stock_id';

    const STORE_LOCATIONS_AVAILABILITY_MODE = 'store_locator/configuration/availability_mode';

    public function getStoreLocationsSource()
    {
        return $this->scopeConfig->getValue(self::STORE_LOCATIONS_SOURCE_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getAvailabilityMode(){
        return $this->scopeConfig->getValue(self::STORE_LOCATIONS_AVAILABILITY_MODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
