<?php

namespace Magebees\CacheWarmer\Block\Adminhtml\Form\Field\PageType;

use Magebees\CacheWarmer\Model\Serializer;
use Magebees\CacheWarmer\Model\Config\Source\PageType;
use Magento\Backend\Block\Template;
use Magento\Framework\App\ObjectManager;

/**
 * @method array getValue()
 * @method Element setValue(array $value)
 * @method string getName()
 * @method Element setName(string $value)
 * */
class Element extends Template
{
    /**
     * @var string
     */
    protected $_template = 'form/field/page_type.phtml';

    /**
     * @var PageType
     */
    private $pageTypeSource;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        Template\Context $context,
        PageType $pageTypeSource,
        ?Serializer $serializer = null, //todo: move to not optional
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageTypeSource = $pageTypeSource;
        $this->serializer = $serializer ?? ObjectManager::getInstance()->get(Serializer::class);
    }

    public function getOptions()
    {
        return $this->pageTypeSource->toOptionArray();
    }

    public function getTypes()
    {
        $options = $this->getValue();

        if (!is_array($options)) {
            $options = $this->serializer->unserialize($options);
        }

        uasort($options, function ($a, $b) {
            return $a['priority'] < $b['priority'] ? -1 : 1;
        });

        $labels = $this->pageTypeSource->toArray();

        foreach ($options as $key => &$option) {
            $option['label'] = $labels[$key];
        }

        return $options;
    }
}
