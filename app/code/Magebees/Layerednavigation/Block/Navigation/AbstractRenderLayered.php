<?php
namespace Magebees\Layerednavigation\Block\Navigation;
use Magento\Framework\View\Element\Template;

abstract class AbstractRenderLayered extends Template
{
    /**
     * The filter of the RenderLayered.
     *
     * @var \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    protected $filter;

    /**
     * Sets the filter on this RenderLayered object.
     *
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setMagebeesNavFilter(\Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Returns the request value of the filter.
     *
     * @return string
     */
    public function getFilterRequestVar()
    {
        return $this->filter->getRequestVar();
    }
}
