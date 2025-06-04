<?php declare(strict_types=1);

namespace ObstkistenTheme\Twig;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Content\Product\ProductEvents;

class ProductSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProductEvents::PRODUCT_LOADED_EVENT => 'onProductsLoaded',
            'sales_channel.' . ProductEvents::PRODUCT_LOADED_EVENT => 'onSalesChannelProductsLoaded'
        ];
    }

    public function onProductsLoaded(EntityLoadedEvent $event): void
    {
        // Hier können Sie Produkte bearbeiten, wenn sie im Admin-Bereich geladen werden
    }

    public function onSalesChannelProductsLoaded(EntityLoadedEvent $event): void
    {
        /** @var SalesChannelProductEntity $productEntity */
        foreach ($event->getEntities() as $productEntity) {
            // Custom Fields abrufen
            $customFields = $productEntity->getCustomFields() ?? [];
            
            // Wenn bereits Custom Fields vorhanden sind und die Extension noch nicht existiert
            if (!$productEntity->hasExtension('customFields') && !empty($customFields)) {
                // ArrayStruct verwenden, um die Custom Fields zu speichern
                $customFieldsStruct = new ArrayStruct($customFields);
                $productEntity->addExtension('customFields', $customFieldsStruct);
            }
        }
    }
}