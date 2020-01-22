<?php
namespace MageSuite\StoreLocatorGraphQl\Model\Config\Source;

class Stock implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\InventoryApi\Api\StockRepositoryInterface
     */
    protected $stockRepository;

    public function __construct(\Magento\InventoryApi\Api\StockRepositoryInterface $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    public function toOptionArray()
    {
        $options = [];

        $options[] = ['value' => '', 'label' => __('Stock assigned to website')];

        foreach ($this->stockRepository->getList()->getItems() as $stock) {
            $options[] = ['value' => $stock->getStockId(), 'label' => $stock->getName()];
        }

        return $options;
    }
}
