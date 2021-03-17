<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\Api\DataObjectHelper;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\InventoryInStorePickup\Model\Source\GetIsPickupLocationActive;
/** @var SourceInterfaceFactory $sourceFactory */
$sourceFactory = Bootstrap::getObjectManager()->get(SourceInterfaceFactory::class);
/** @var DataObjectHelper $dataObjectHelper */
$dataObjectHelper = Bootstrap::getObjectManager()->get(DataObjectHelper::class);
/** @var SourceRepositoryInterface $sourceRepository */
$sourceRepository = Bootstrap::getObjectManager()->get(SourceRepositoryInterface::class);

$sourcesData = [
    [
        // define only required and needed for tests fields
        SourceInterface::SOURCE_CODE => 'eu-1',
        SourceInterface::NAME => 'EU-source-1',
        SourceInterface::ENABLED => true,
        SourceInterface::POSTCODE => 'postcode-1',
        SourceInterface::CITY => 'city-1',
        SourceInterface::STREET => 'street-1',
        SourceInterface::COUNTRY_ID => 'DE',
        SourceInterface::PHONE => '111111111',
        \Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface::IS_PICKUP_LOCATION_ACTIVE => 1
    ],
    [
        SourceInterface::SOURCE_CODE => 'eu-2',
        SourceInterface::NAME => 'EU-source-2',
        SourceInterface::ENABLED => true,
        SourceInterface::POSTCODE => 'postcode-2',
        SourceInterface::CITY => 'city-2',
        SourceInterface::STREET => 'street-2',
        SourceInterface::COUNTRY_ID => 'DE',
        SourceInterface::PHONE => '222222222',
        \Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface::IS_PICKUP_LOCATION_ACTIVE => 1
    ],
    [
        SourceInterface::SOURCE_CODE => 'eu-3',
        SourceInterface::NAME => 'EU-source-3',
        SourceInterface::ENABLED => true,
        SourceInterface::POSTCODE => 'postcode-3',
        SourceInterface::CITY => 'city-3',
        SourceInterface::STREET => 'street-3',
        SourceInterface::COUNTRY_ID => 'DE',
        SourceInterface::PHONE => '333333333',
        \Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface::IS_PICKUP_LOCATION_ACTIVE => 1
    ],
    [
        SourceInterface::SOURCE_CODE => 'eu-disabled',
        SourceInterface::NAME => 'EU-source-disabled',
        SourceInterface::ENABLED => false,
        SourceInterface::POSTCODE => 'postcode-4',
        SourceInterface::CITY => 'city-4',
        SourceInterface::STREET => 'street-4',
        SourceInterface::COUNTRY_ID => 'DE',
        SourceInterface::PHONE => '444444444',
        \Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface::IS_PICKUP_LOCATION_ACTIVE => 0
    ],
    [
        SourceInterface::SOURCE_CODE => 'us-1',
        SourceInterface::NAME => 'US-source-1',
        SourceInterface::ENABLED => true,
        SourceInterface::POSTCODE => 'postcode-5',
        SourceInterface::CITY => 'city-5',
        SourceInterface::STREET => 'street-5',
        SourceInterface::COUNTRY_ID => 'US',
        SourceInterface::PHONE => '555555555',
        \Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface::IS_PICKUP_LOCATION_ACTIVE => 0
    ],
];
foreach ($sourcesData as $sourceData) {
    /** @var SourceInterface $source */
    $source = $sourceFactory->create();
    $dataObjectHelper->populateWithArray($source, $sourceData, SourceInterface::class);
    $extension = $source->getExtensionAttributes();
    $extension->setIsPickupLocationActive($sourceData[\Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface::IS_PICKUP_LOCATION_ACTIVE]);
    $sourceRepository->save($source);
}
