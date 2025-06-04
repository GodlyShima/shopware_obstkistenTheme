<?php declare(strict_types=1);

namespace ObstkistenTheme\Twig;

use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OOGetCategoryById extends AbstractExtension
{
    private EntityRepository $categoryRepository;

    public function __construct(EntityRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getCategoryWithMedia', [$this, 'getCategoryWithMedia']),
        ];
    }

    public function getCategoryWithMedia(string $categoryId, ?Context $context = null): ?CategoryEntity
    {
        if (!$context) {
            $context = Context::createDefaultContext();
        }

        $criteria = new Criteria([$categoryId]);
        $criteria->addAssociation('media');

        return $this->categoryRepository->search($criteria, $context)->get($categoryId);
    }
}
