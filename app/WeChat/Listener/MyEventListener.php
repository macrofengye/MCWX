<?php
namespace Blog\listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Entity\Product;
class MyEventListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        echo __CLASS__;
        // perhaps you only want to act on some "Product" entity
        if ($entity instanceof Product) {
            // do something with the Product
        }
    }
    
    public function test(){
        echo __CLASS__;
    }
}

?>