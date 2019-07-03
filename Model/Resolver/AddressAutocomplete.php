<?php

namespace MageSuite\StoreLocatorGraphQl\Model\Resolver;

class AddressAutocomplete implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \MageSuite\GoogleApi\Service\PlaceAutocompleteResolver
     */
    protected $placeAutocompleteResolver;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \MageSuite\GoogleApi\Service\PlaceAutocompleteResolver $placeAutocompleteResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->placeAutocompleteResolver = $placeAutocompleteResolver;
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

        $params = ['input' => $address];

        if(isset($country)) {
            $params['components'] = sprintf('country:%s', $country);
        }

        $result = $this->placeAutocompleteResolver->execute($params);

        if(is_null($result)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('PlaceAutocomplete request failed'));
        }

        if(!in_array($result->status,['OK', 'ZERO_RESULTS'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__($result->error_message));
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

    public function resolveCountry(array $args)
    {
        if(isset($args['country'])) {
            return $args['country'];
        }

        $country = $this->scopeConfig->getValue('store_locator/configuration/country_id');

        return $country;
    }
}