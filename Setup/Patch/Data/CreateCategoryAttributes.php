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

namespace Bluebirdday\Subcategories\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Data create category attributes
 */
class CreateCategoryAttributes implements DataPatchInterface, PatchVersionInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory $eavSetupFactory;

    /**
     * CreateUrlAttributes constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $categoryTypeId = \Magento\Catalog\Model\Category::ENTITY;
        $scopeStore = \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE;

        $eavSetup->addAttribute(
            $categoryTypeId,
            'bluebirdday_subcategory_is_active',
            [
                'group' => 'General Information',
                'type' => 'int',
                'label' => 'Enable Subcategory Thumbnails',
                'input' => 'boolean',
                'required' => false,
                'default' => '0',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'visible_on_front' => false,
                'global' => $scopeStore,
                'sort_order' => 4,
            ]
        );

        $eavSetup->addAttribute(
            $categoryTypeId,
            'bluebirdday_subcategory_list_usp',
            [
                'group' => 'General Information',
                'type' => 'text',
                'label' => 'Subcategory Usp List',
                'input' => 'textarea',
                'required' => false,
                'wysiwyg_enabled' => true,
                'is_html_allowed_on_front' => true,
                'global' => $scopeStore,
                'sort_order' => 5,
            ]
        );

        $eavSetup->addAttribute(
            $categoryTypeId,
            'bluebirdday_subcategory_thumbnail',
            [
                'group' => 'Content',
                'type' => 'varchar',
                'label' => 'Subcategory Thumbnail',
                'input' => 'image',
                'required' => false,
                'default' => '0',
                'backend'    => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class,
                'visible_on_front' => true,
                'global' => $scopeStore,
                'sort_order' => 4,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
