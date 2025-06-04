<?php declare(strict_types=1);

namespace ObstkistenTheme;

use Shopware\Core\Framework\Plugin;
use Shopware\Storefront\Framework\ThemeInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class ObstkistenTheme extends Plugin implements ThemeInterface
{
    public function getThemeConfigPath(): string
    {
        return 'theme.json';
    }

    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('shopware.cms.data_resolver');

        $definition->addMethodCall('add', [
            new Reference('WebefaceSixCategories\Core\Content\Cms\WebefaceSixCategoriesCmsElementResolver')
        ]);
    }

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $this->createCustomFields($installContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);
        
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->removeCustomFields($uninstallContext->getContext());
    }

    private function createCustomFields(Context $context): void
    {
        /** @var EntityRepositoryInterface $customFieldSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        
        // Prüfen, ob das Custom Field Set bereits existiert
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', 'obstkisten_product_fields'));
        $customFieldSets = $customFieldSetRepository->searchIds($criteria, $context);
        
        if ($customFieldSets->getTotal() > 0) {
            return; // Custom Field Set existiert bereits
        }
        
        // Custom Field Set erstellen
        $customFieldSetRepository->create([
            [
                'name' => 'obstkisten_product_fields',
                'config' => [
                    'label' => [
                        'de-DE' => 'Obstkisten Produktfelder',
                        'en-GB' => 'Obstkisten Product Fields'
                    ]
                ],
                'relations' => [
                    [
                        'entityName' => 'product'
                    ]
                ],
                'customFields' => [
                    [
                        'name' => 'product_alias',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => [
                                'de-DE' => 'Produkt Alias',
                                'en-GB' => 'Product Alias'
                            ],
                            'customFieldPosition' => 1,
                            'helpText' => [
                                'de-DE' => 'Alternative Bezeichnung für das Produkt',
                                'en-GB' => 'Alternative name for the product'
                            ]
                        ]
                    ]
                ]
            ]
        ], $context);
    }

    private function removeCustomFields(Context $context): void
    {
        /** @var EntityRepositoryInterface $customFieldSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', 'obstkisten_product_fields'));
        $customFieldSets = $customFieldSetRepository->searchIds($criteria, $context);
        
        if ($customFieldSets->getTotal() === 0) {
            return;
        }
        
        $ids = array_map(static function ($id) {
            return ['id' => $id];
        }, $customFieldSets->getIds());
        
        $customFieldSetRepository->delete($ids, $context);
    }
}