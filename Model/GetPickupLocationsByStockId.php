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

    /**
     * @var \Magento\Framework\Filter\RemoveAccents
     */
    protected $removeAccents;

    public function __construct(
        \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority,
        \Magento\InventoryInStorePickupApi\Model\Mapper $mapper,
        \Magento\InventoryInStorePickup\Model\Source\GetIsPickupLocationActive $getIsPickupLocationActive,
        \MageSuite\StoreLocatorGraphQl\Helper\Configuration $configuration,
        \Magento\Framework\Filter\RemoveAccents $removeAccents
    ) {

        $this->getSourcesAssignedToStockOrderedByPriority = $getSourcesAssignedToStockOrderedByPriority;
        $this->mapper = $mapper;
        $this->getIsPickupLocationActive = $getIsPickupLocationActive;
        $this->configuration = $configuration;
        $this->removeAccents = $removeAccents;
    }

    public function execute(int $stockId, string $query = ''): array
    {
        $query = $this->removeAccents->filter($query);

        $sources = $this->getSourcesAssignedToStockOrderedByPriority->execute($stockId);

        $result = [];
        foreach ($sources as $source) {
            if (!$this->shouldSourceBeDisplayed($source)) {
                continue;
            }

            if (empty($query)) {
                $result[] = $this->mapper->map($source);
                continue;
            }

            if ($this->sourceMatchQuery($source, $query)) {
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

    private function sourceMatchQuery(\Magento\InventoryApi\Api\Data\SourceInterface $source, string $query): bool
    {
        if ($this->sourceMatchCity($source->getCity(), $query) || ($query === $source->getPostcode())) {
            return true;
        }

        return false;
    }

    private function sourceMatchCity(string $cityString, string $query): bool
    {
        $cityString = $this->removeAccents->filter($cityString);

        if (mb_stripos($cityString, $query) === 0) {
            return true;
        }

        return false;
    }
}
