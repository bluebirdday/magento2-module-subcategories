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

namespace Bluebirdday\Subcategories\Block;

use Bluebirdday\Subcategories\Helper\Data as DataHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

/**
 * Block Subcategories
 */
class Subcategories extends \Magento\Catalog\Block\Category\View
{
    protected CollectionFactory $categoryCollectionFactory;
    protected \Magento\Catalog\Helper\Category $categoryHelper;
    protected DataHelper $dataHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param CollectionFactory $categoryCollectionFactory
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Category $categoryHelper,
        CollectionFactory $categoryCollectionFactory,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryHelper = $categoryHelper;
        $this->dataHelper = $dataHelper;

        parent::__construct(
            $context,
            $layerResolver,
            $registry,
            $categoryHelper,
            $data
        );
    }

    /**
     * Helper
     *
     * @return DataHelper
     */
    public function helper(): DataHelper
    {
        return $this->dataHelper;
    }

    /**
     * Get the category collection.
     *
     * @return Collection
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryCollection(): Collection
    {
        $ids = $this->getCurrentCategory()->getChildren();
        if ($ids) {
            $ids = explode(',', $ids);
        }

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addIsActiveFilter()
            ->setOrder('position', 'ASC')
            ->addIdFilter($ids);

        return $collection;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if (!$this->dataHelper->isModuleEnabled() || !$this->dataHelper->isListingEnabled($this->getCurrentCategory())) {
            return '';
        }
        return parent::_toHtml();
    }
}
