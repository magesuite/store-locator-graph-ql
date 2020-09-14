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

    public function __construct(
        \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority,
        \Magento\InventoryInStorePickupApi\Model\Mapper $mapper,
        \Magento\InventoryInStorePickup\Model\Source\GetIsPickupLocationActive $getIsPickupLocationActive
    ) {

        $this->getSourcesAssignedToStockOrderedByPriority = $getSourcesAssignedToStockOrderedByPriority;
        $this->mapper = $mapper;
        $this->getIsPickupLocationActive = $getIsPickupLocationActive;
    }

    public function execute($stockId)
    {
        $sources = $this->getSourcesAssignedToStockOrderedByPriority->execute($stockId);

        $result = [];
        foreach ($sources as $source) {
            if ($this->getIsPickupLocationActive->execute($source)) {
                $result[] = $this->mapper->map($source);
            }
        }

        return $result;
    }
}
