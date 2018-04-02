<?php
namespace AppBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class BookSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'bookDelete',
        );
    }

    public function bookDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // возможно, вы хотите действовать только на некой сущности "Product"
        /*if ($entity instanceof Product) {
            $entityManager = $args->getEntityManager();
            // ... сделать что-то с Product
        }*/
    }
}
