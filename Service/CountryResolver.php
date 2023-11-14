<?php
namespace MageSuite\StoreLocatorGraphQl\Service;

class CountryResolver
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function resolveCountry(array $args)
    {
        if(isset($args['country'])) {
            return sprintf('country:%s', $args['country']);
        }

        $countries = $this->scopeConfig->getValue('store_locator/configuration/country_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $countries = explode(',', $countries);

        $countrySearchCondition = '';

        foreach ($countries as $country){
            $countrySearchCondition .= sprintf('country:%s', $country) . '|';
        }
        return rtrim($countrySearchCondition, '|');
    }
}
