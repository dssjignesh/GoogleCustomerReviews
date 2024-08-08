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
use Magento\Framework\Locale\TranslatedLists;

class Language implements OptionSourceInterface
{
    /**
     * @param TranslatedLists $translatedLists
     */
    public function __construct(
        private TranslatedLists $translatedLists
    ) {
    }

    /**
     * Retrieve an array of options for the dropdown.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $allowedLanguages = [
            "cs",
            "da",
            "de",
            "en_AU",
            "en_GB",
            "en_US",
            "es",
            "fr",
            "it",
            "ja",
            "nl",
            "no",
            "pl",
            "pt_BR",
            "ru",
            "sv",
            "tr"
        ];

        $result = [__('User Environment Defined')];
        $locales = $this->translatedLists->getOptionLocales();

        foreach ($locales as $language) {
            foreach ($allowedLanguages as $index => $allowed) {
                if (strpos($language['value'], $allowed) === 0) {
                    unset($allowedLanguages[$index]);
                    $label = $language['label'];

                    if (strlen($allowed) == 2) {
                        $label = preg_replace('/(.*?)\(.*?\)/', '$1', $label);
                    }

                    $result[$allowed] = $label;
                }
            }
        }

        return $result;
    }
}
