<?php declare(strict_types=1);

namespace WebefaceSixCategories\Core\Content\Cms;

use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\CmsSlotEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class WebefaceSixCategoriesCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(
        #[Autowire(service: 'category.repository')]
        private readonly EntityRepository $categoryRepository
    ) {
    }

    public function getType(): string
    {
        return 'webeface-six-categories';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteria = new Criteria($slot->getFieldConfig()['categories']['value'] ?? []);
        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add('categories_' . $slot->getUniqueIdentifier(), $this->categoryRepository->getDefinition(), $criteria);

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, CriteriaCollection $criteriaCollection, Context $context): void
    {
        $categories = $resolverContext->getEntityCollection('categories_' . $slot->getUniqueIdentifier());

        $slot->addData('categories', $categories);
    }
}
