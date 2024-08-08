<?php

declare(strict_types=1);
/**
 * Digit Software Solutions.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category  Dss
 * @package   Dss_GoogleReviews
 * @author    Extension Team
 * @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
 */
namespace Dss\GoogleReviews\Block\Adminhtml\Form\Field;

use Magento\Framework\Escaper;

class Countries extends \Magento\Framework\View\Element\Html\Select
{
    /** @var array */
    private $countries;

    /** @var \Magento\Directory\Model\ResourceModel\Country\Collection */
    private $countryCollection;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        private Escaper $escaper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->countryCollection = $countryCollectionFactory->create();
    }

    /**
     * Get available countries.
     *
     * @return array
     */
    protected function getCountries(): array
    {
        if ($this->countries === null) {
            $countries = $this->countryCollection->toOptionArray(false);
            foreach ($countries as $country) {
                if (!isset($country['is_region_visible']) || $country['is_region_visible']) {
                    $this->countries[$country['value']] = $country['label'];
                }
            }
        }

        return $this->countries;
    }

    /**
     * Generate the HTML for the dropdown.
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            foreach ($this->getCountries() as $value => $title) {
                $this->addOption($value, $this->escaper->escapeHtml($title));
            }
        }
        $this->setExtraParams('multiple="multiple" style="width:240px"');
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value): self
    {
        return $this->setName($value . '[]');
    }
}
