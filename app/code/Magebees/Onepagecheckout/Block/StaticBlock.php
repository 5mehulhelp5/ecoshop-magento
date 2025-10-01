<?php
namespace Magebees\Onepagecheckout\Block;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magebees\Onepagecheckout\Helper\Configurations as configHelper;
use Magebees\Onepagecheckout\Model\System\Config\Source\StaticBlockPosition;

class StaticBlock extends Template
{
    protected $configHelper;
    private $checkoutSession;
    protected $blockRepository;

    public function __construct(
        Context $context,
        configHelper $configHelper,
        CheckoutSession $checkoutSession,
        BlockRepositoryInterface $blockRepository,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->configHelper      = $configHelper;
        $this->checkoutSession = $checkoutSession;
        $this->blockRepository = $blockRepository;
    }
    public function getStaticBlock()
    {
        $result = [];
        $config = $this->configHelper->isEnableStaticBlock() ? $this->configHelper->getStaticBlockList() : [];
        foreach ($config as $key => $row) {
            $block = $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($row['block'])->toHtml();
            if (($row['position'] == StaticBlockPosition::SHOW_AT_TOP_CHECKOUT_PAGE && $this->getNameInLayout() == 'mbopc.static-block.top')
                || ($row['position'] == StaticBlockPosition::SHOW_AT_BOTTOM_CHECKOUT_PAGE && $this->getNameInLayout() == 'mbopc.static-block.bottom')) {
                $result[] = [
                    'content'   => $block,
                    'sortOrder' => $row['sort_order']
                ];
            }
        }
        usort($result, function ($a, $b) {
            return ($a['sortOrder'] <= $b['sortOrder']) ? -1 : 1;
        });
        return $result;
    }
}
