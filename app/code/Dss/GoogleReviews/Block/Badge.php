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
namespace Dss\GoogleReviews\Block;

use Magento\Framework\View\Element\Template;
use Dss\GoogleReviews\Helper\Config as ConfigHelper;
use Magento\Framework\Escaper;

class Badge extends Template
{
    /**
     * @param Template\Context $context
     * @param ConfigHelper $configHelper
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        private ConfigHelper $configHelper,
        private Escaper $escaper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Render the HTML for the Google Reviews badge.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        return $this->configHelper->isBadgeEnabled() ? parent::_toHtml() : '';
    }

    /**
     * Get the merchant ID.
     *
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->configHelper->getMerchantId();
    }

    /**
     * Get the badge position.
     *
     * @return string
     */
    public function getPosition(): string
    {
        $pos = $this->configHelper->getBadgePosition();
        return $this->escaper->escapeHtml($pos);
    }

    /**
     * Get the badge language.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        $lang = $this->configHelper->getBadgeLanguage();
        return $this->escaper->escapeHtml($lang);
    }
}
