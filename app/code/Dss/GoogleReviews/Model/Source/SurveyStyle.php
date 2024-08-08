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
namespace Dss\GoogleReviews\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SurveyStyle implements OptionSourceInterface
{
    /**
     * Retrieve an array of options for the dropdown.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            'CENTER_DIALOG' => __('Center Dialog'),
            'BOTTOM_RIGHT_DIALOG' => __('Bottom Right Dialog'),
            'BOTTOM_LEFT_DIALOG' => __('Bottom Left Dialog'),
            'TOP_RIGHT_DIALOG' => __('Top Right Dialog'),
            'TOP_LEFT_DIALOG' => __('Top Left Dialog'),
            'BOTTOM_TRAY' => __('Bottom Tray')
        ];
    }
}
