<?php

namespace MageSuite\StoreLocatorGraphQl\Model\Resolver;

class AddressLocation implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    private const LOCATION_TYPE_COUNTRY = 'country';

    protected \MageSuite\GoogleApi\Service\GeoLocationResolver $geoLocationResolver;

    protected \MageSuite\StoreLocatorGraphQl\Service\CountryResolver $countryResolver;

    public function __construct(
        \MageSuite\GoogleApi\Service\GeoLocationResolver $geoLocationResolver,
        \MageSuite\StoreLocatorGraphQl\Service\CountryResolver $countryResolver
    ) {
        $this->geoLocationResolver = $geoLocationResolver;
        $this->countryResolver = $countryResolver;
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
        $address = $args['query'];
        $params = ['address' => $address];

        $countries = $this->countryResolver->resolveCountry($args);
        if (isset($countries)) {
            $params['components'] = $countries;
        }

        $result = $this->geoLocationResolver->execute($params);
        if (is_null($result)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('GeoLocation request failed'));
        }

        if (!in_array($result->status,['OK', 'ZERO_RESULTS'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__($result->error_message));
        }

        foreach ($result->results as $location) {
            if (in_array(self::LOCATION_TYPE_COUNTRY, $location->types)) {
                continue;
            }

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
}
