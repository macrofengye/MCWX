<?php
namespace Blog\listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Entity\Product;
class MyOtherEventListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $em = $args->getObjectManager();
         $firstName = $entity->getFirstName();
         $firstName .= " chen ";
         $entity->setFirstName($firstName);
        // perhaps you only want to act on some "Product" entity
        if ($entity instanceof Product) {
            // do something with the Product
        }
    }
}

?>