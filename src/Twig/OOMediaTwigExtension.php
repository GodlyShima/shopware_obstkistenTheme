<?php declare(strict_types=1);

namespace ObstkistenTheme\Twig;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OOMediaTwigExtension extends AbstractExtension
{
    private EntityRepository $mediaRepository;

    public function __construct(EntityRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getMediaById', [$this, 'getMediaById']),
        ];
    }

    public function getMediaById(string $mediaId, ?Context $context = null): ?MediaEntity
    {
        $context = $context ?? Context::createDefaultContext();

        $criteria = new Criteria([$mediaId]);
        // KEIN addFields hier
        return $this->mediaRepository->search($criteria, $context)->get($mediaId);
    }

}
