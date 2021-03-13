<?php
namespace MageSuite\StoreLocatorGraphQl\Model\Config\Source;

class AvailabilityMode implements \Magento\Framework\Option\ArrayInterface
{

    public const STORES_AVAILABILITY_MODE_SHOW_ALL = 'show_all';

    public const STORES_AVAILABILITY_MODE_SHOW_ENABLED_ONLY = 'show_enabled_only';

    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::STORES_AVAILABILITY_MODE_SHOW_ALL,
                'label' => __('Show all stores'),
                'image'=>'MageSuite_StoreLocatorGraphQl::images/show_all.png',
                'description'=>'In this mode, all stores will be shown on map, regardless of their status as in store pickup dropdown.'
            ],
            [
                'value' => self::STORES_AVAILABILITY_MODE_SHOW_ENABLED_ONLY,
                'label' => __('Show enabled only'),
                'image'=>'MageSuite_StoreLocatorGraphQl::images/show_enabled.png',
                'description' => 'In this mode, only stores available to pickup will be shown on map.']
        ];

        return $options;
    }
}
