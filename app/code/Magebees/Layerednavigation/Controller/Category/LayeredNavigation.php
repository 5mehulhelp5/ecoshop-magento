<?php

namespace Magebees\LayeredNavigation\Controller\Category;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class LayeredNavigation extends Action
{
    private $pageFactory;
    private $jsonFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();

        try {
            // Create a Page instance and load the layout
            $page = $this->pageFactory->create();
            $layout = $page->getLayout();

            // Ensure the layout handle for category view is loaded
            $layout->getUpdate()->addHandle('catalog_category_view');
            $layout->generateXml();
            $layout->generateElements();

            // Manually create the catalog.leftnav block
            $layeredNavBlock = $layout->createBlock(
                \Magento\LayeredNavigation\Block\Navigation::class,
                'catalog.leftnav'
            );

            if ($layeredNavBlock) {
                // Generate the block's HTML
                $html = $layeredNavBlock->toHtml();

                return $resultJson->setData([
                    'success' => true,
                    'layered_navigation' => $html,
                ]);
            }

            return $resultJson->setData([
                'success' => false,
                'message' => 'Layered navigation block not found.',
            ]);
        } catch (\Exception $e) {
            return $resultJson->setData([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
