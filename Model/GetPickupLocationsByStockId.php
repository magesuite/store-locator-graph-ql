<?php
namespace MageSuite\StoreLocatorGraphQl\Model;

class GetPickupLocationsByStockId
{
    /**
     * @var \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface
     */
    protected $getSourcesAssignedToStockOrderedByPriority;

    /**
     * @var \Magento\InventoryInStorePickupApi\Model\Mapper
     */
    protected $mapper;

    /**
     * @var \Magento\InventoryInStorePickup\Model\Source\GetIsPickupLocationActive
     */
    protected $getIsPickupLocationActive;

    /**
     * @var \MageSuite\StoreLocatorGraphQl\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority,
        \Magento\InventoryInStorePickupApi\Model\Mapper $mapper,
        \Magento\InventoryInStorePickup\Model\Source\GetIsPickupLocationActive $getIsPickupLocationActive,
        \MageSuite\StoreLocatorGraphQl\Helper\Configuration $configuration
    ) {

        $this->getSourcesAssignedToStockOrderedByPriority = $getSourcesAssignedToStockOrderedByPriority;
        $this->mapper = $mapper;
        $this->getIsPickupLocationActive = $getIsPickupLocationActive;
        $this->configuration = $configuration;
    }

    public function execute($stockId)
    {
        $sources = $this->getSourcesAssignedToStockOrderedByPriority->execute($stockId);

        $result = [];
        foreach ($sources as $source) {
            if ($this->shouldSourceBeDisplayed($source)) {
                $result[] = $this->mapper->map($source);
            }
        }

        return $result;
    }

    /**
     * @param $source
     * @return bool
     */
    private function shouldSourceBeDisplayed($source)
    {
        if ($this->configuration->getAvailabilityMode() == \MageSuite\StoreLocatorGraphQl\Model\Config\Source\AvailabilityMode::STORES_AVAILABILITY_MODE_SHOW_ENABLED_ONLY) {
            return $this->getIsPickupLocationActive->execute($source);
        }

        return true;
    }
}
