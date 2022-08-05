<?php
/**
 * @file
 * Magento 2 module - Sequence by environment
 * Copyright (C) 2022  Bluebirdday
 *
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Bluebirdday\Subcategories\Helper;

use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface as iScope;
use Magento\Theme\Block\Html\Header\Logo;

/**
 * Common module helper.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private const CONF_ENABLED_PATH = 'bluebirdday_subcategories/general/enabled';

    protected StoreManagerInterface $storeManager;
    protected Logo $logo;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param StoreManagerInterface $storeManager
     * @param Logo $logo
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager,
        Logo $logo
    ) {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->logo = $logo;
    }

    /**
     * Module active.
     *
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONF_ENABLED_PATH, iScope::SCOPE_STORE);
    }

    /**
     * Is listing enabled.
     *
     * @param Category $category
     *
     * @return bool
     */
    public function isListingEnabled(Category $category): bool
    {
        return '1' === $category->getData('bluebirdday_subcategory_is_active');
    }

    /**
     * Get list usp.
     *
     * @param Category $category
     *
     * @return string
     */
    public function getListUsp(Category $category): string
    {
        return (string) $category->getData('bluebirdday_subcategory_list_usp');
    }

    /**
     * Get raw thumbnail.
     *
     * @param Category $category
     *
     * @return string
     *   Empty when no thumbnail is set.
     */
    public function getThumbnail(Category $category): string
    {
        return (string) $category->getData('bluebirdday_subcategory_thumbnail');
    }

    /**
     * Get category thumbnail url.
     *
     * On empty returns placeholder.
     *
     * @param Category $category
     *
     * @return string
     *
     * @throws LocalizedException
     */
    public function getThumbnailUrl(Category $category): string
    {
        $thumbnail = $category->getData('bluebirdday_subcategory_thumbnail');

        if ($thumbnail) {
            return $category->getImageUrl('bluebirdday_subcategory_thumbnail');
        }

        return $this->getLogoSrc();
    }

    /**
     * Get the website's logo source URL.
     *
     * @return string
     */
    protected function getLogoSrc(): string
    {
        return (string) $this->logo->getLogoSrc();
    }
}
