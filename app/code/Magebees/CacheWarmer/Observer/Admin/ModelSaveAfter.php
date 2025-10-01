<?php
namespace Magebees\CacheWarmer\Observer\Admin;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Refresher;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ModelSaveAfter implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Refresher
     */
    private $refresher;

    public function __construct(
        Config $config,
        Refresher $refresher
    ) {
        $this->config = $config;
        $this->refresher = $refresher;
    }

    public function execute(Observer $observer)
    {
        if (!$this->config->isAutoUpdate()) {
            return;
        }

        $object = $observer->getData('object');

        if ($object instanceof PageInterface) {
            if ($this->refresher->isIndexPage($object->getIdentifier())) {
                $this->refresher->queueIndexPage();
            } else {
                $this->refresher->queueCmsPage($object->getIdentifier());
            }
        } elseif ($object instanceof ProductInterface) {
            $this->refresher->queueProductPage($object->getId(), (int)$object->getStoreId());
        } elseif ($object instanceof CategoryInterface) {
            $this->refresher->queueCategoryPage($object->getId(), (int)$object->getStoreId());
        }
    }
}
