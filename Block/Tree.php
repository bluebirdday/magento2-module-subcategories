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
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;

/**
 * Block Category tree
 */
class Tree extends \Magento\Catalog\Block\Category\View
{

    protected CollectionFactory $categoryCollectionFactory;
    protected DataHelper $dataHelper;
    protected Node $menu;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param CollectionFactory $categoryCollectionFactory
     * @param DataHelper $dataHelper
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Category $categoryHelper,
        CollectionFactory $categoryCollectionFactory,
        DataHelper $dataHelper,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->menu = $nodeFactory->create(
            [
                'data' => [],
                'idField' => 'root',
                'tree' => $treeFactory->create()
            ]
        );

        parent::__construct(
            $context,
            $layerResolver,
            $registry,
            $categoryHelper,
            $data
        );
    }

    /**
     * Get menu object.
     *
     * @return Node
     */
    public function getMenu(): Node
    {
        return $this->menu;
    }

    /**
     * @param int $depth
     * @return mixed
     */
    public function getCurrentTree($depth = 3)
    {
        $tree = $this->getCategoryCollectionTree($depth);
        return $tree[$this->getCurrentCategory()->getId()];
    }

    /**
     * @param int $depth
     * @return array
     */
    public function getCategoryCollectionTree($depth = 3)
    {
        $collection = $this->getCategoryCollection($depth);
        $mapping = [$this->getCurrentCategory()->getId() => $this->getMenu()];
//        $collection->load(true);
        foreach ($collection as $category) {
            if (!isset($mapping[$category->getParentId()])) {
                continue;
            }
            /** @var Node $parentCategoryNode */
            $parentCategoryNode = $mapping[$category->getParentId()];

            $categoryNode = new Node(
                $this->getCategoryAsArray($category, $this->getCurrentCategory()),
                'id',
                $parentCategoryNode->getTree(),
                $parentCategoryNode
            );
            $parentCategoryNode->addChild($categoryNode);
            $mapping[$category->getId()] = $categoryNode; //add node in stack
        }
        return $mapping;
    }

    /**
     * Convert category to array
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return array
     */
    public function getCategoryAsArray($category)
    {
        return [
            'name' => $category->getName(),
            'id' => 'category-node-' . $category->getId(),
            'url' => $this->_categoryHelper->getCategoryUrl($category),
            'description' => $category->getDescription(),
            'short_description' => $category->getShortCategoryDescription(),
            'image' => $this->dataHelper->getThumbnailUrl($category->getBluebirddaySubcategoryThumbnail())
        ];
    }

    /**
     * Get the category collection.
     *
     * @return mixed
     */
    public function getCategoryCollection($depth)
    {
        $maxLevel = $this->getCurrentCategory()->getLevel() + $depth;
        $collection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', 1)
            ->setOrder('level', 'ASC')
            ->addFieldToFilter('path', ['like' => $this->getCurrentCategory()->getPath() . '/%'])
            ->addLevelFilter($maxLevel);
        return $collection;
    }

    /**
     * Get depth
     *
     * @return int
     */
    public function getDepth(): int
    {
        $depth = (int) $this->_getData('depth');
        if ($depth <= 0) {
            $depth = 3;
        }

        return $depth;
    }

    /**
     * Get max sub children
     *
     * @return int
     */
    public function getMaxSubChildren(): int
    {
        $max = (int) $this->_getData('max_sub_children');
        if ($max <= 0) {
            $max = 8;
        }

        return $max;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->dataHelper->isModuleEnabled() || !$this->dataHelper->isListingEnabled($this->getCurrentCategory())) {
            return '';
        }
        return parent::_toHtml();
    }
}
