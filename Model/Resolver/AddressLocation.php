<?php

namespace MageSuite\StoreLocatorGraphQl\Model\Resolver;

class AddressLocation implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \MageSuite\GoogleApi\Service\GeoLocationResolver
     */
    protected $geoLocationResolver;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \MageSuite\GoogleApi\Service\GeoLocationResolver $geoLocationResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->geoLocationResolver = $geoLocationResolver;
        $this->scopeConfig = $scopeConfig;
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
        $address = $args['query'];
        $country = $this->resolveCountry($args);

        $params = ['address' => $address];

        if(isset($country)) {
            $params['components'] = sprintf('country:%s', $country);
        }

        $result = $this->geoLocationResolver->execute($params);

        if(is_null($result)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('GeoLocation request failed'));
        }

        if(!in_array($result->status,['OK', 'ZERO_RESULTS'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__($result->error_message));
        }

        if(isset($result->results[0])) {
            $location = $result->results[0];

            return [
                'latitude' => $location->geometry->location->lat,
                'longitude' => $location->geometry->location->lng
            ];
        }

        return [
            'latitude' => null,
            'longitude' => null
        ];
    }

    public function resolveCountry(array $args)
    {
        if(isset($args['country'])) {
            return $args['country'];
        }

        $country = $this->scopeConfig->getValue('store_locator/configuration/country_id');

        return $country;
    }
}