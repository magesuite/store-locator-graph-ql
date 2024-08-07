<?php

namespace MageSuite\StoreLocatorGraphQl\Model\Resolver;

class AddressAutocomplete implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected \MageSuite\GoogleApi\Service\PlaceAutocompleteResolver $placeAutocompleteResolver;

    protected \MageSuite\StoreLocatorGraphQl\Service\CountryResolver $countryResolver;

    public function __construct(
        \MageSuite\GoogleApi\Service\PlaceAutocompleteResolver $placeAutocompleteResolver,
        \MageSuite\StoreLocatorGraphQl\Service\CountryResolver $countryResolver
    ) {
        $this->placeAutocompleteResolver = $placeAutocompleteResolver;
        $this->countryResolver = $countryResolver;
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
        $countries = $this->countryResolver->resolveCountry($args);

        $params = ['input' => $address];

        if(isset($countries)) {
            $params['components'] = $countries;
        }

        $result = $this->placeAutocompleteResolver->execute($params);

        if(is_null($result)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('PlaceAutocomplete request failed'));
        }

        if(!in_array($result->status,['OK', 'ZERO_RESULTS'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__($result->error_message ?? 'PlaceAutocomplete request failed'));
        }

        if(isset($result->predictions) and !empty($result->predictions)) {
            $results = [];

            foreach($result->predictions as $prediction) {
                $results[] = ['description' => $prediction->description];
            }

            return ['items' => $results];
        }

        return ['items' => []];
    }
}
