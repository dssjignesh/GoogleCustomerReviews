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

use Dss\GoogleReviews\Helper\CustomDeliveryTime as Helper;
use Magento\Shipping\Model\Config;
use Magento\Framework\Escaper;

class Carriers extends \Magento\Framework\View\Element\Html\Select
{
    /** @var array */
    private $carriers;

    /**
     * Carriers constructor.
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param Config $shippingMethodConfig
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        private Config $shippingMethodConfig,
        private Escaper $escaper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get available carriers.
     *
     * @return array
     */
    protected function getCarriers(): array
    {
        if ($this->carriers === null) {
            $this->carriers = [Helper::ANY_METHOD => __('Any Method')];

            $carriers = $this->shippingMethodConfig->getActiveCarriers();
            foreach ($carriers as $carrierCode => $carrier) {
                if ($methods = $carrier->getAllowedMethods()) {
                    if (!$carrierTitle = $this->_scopeConfig->getValue("carriers/$carrierCode/title", 'store')) {
                        $carrierTitle = __($carrierCode);
                    }
                    foreach ($methods as $methodCode => $methodTitle) {
                        $value = $carrierCode . '_' . $methodCode;
                        $this->carriers[$value] = $carrierTitle . ' — ' . $methodTitle;
                    }
                }
            }
        }

        return $this->carriers;
    }

    /**
     * Set the input name for the dropdown.
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value): self
    {
        return $this->setName($value);
    }

    /**
     * Generate the HTML for the dropdown.
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            foreach ($this->getCarriers() as $value => $title) {
                $this->addOption($value, $this->escaper->escapeHtml($title));
            }
        }

        $this->setExtraParams('style="width:220px"');
        return parent::_toHtml();
    }
}
