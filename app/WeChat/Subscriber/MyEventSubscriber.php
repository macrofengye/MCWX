<?php
namespace Blog\subscriber;

use Doctrine\ORM\Events;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Entity\Actor;

class MyEventSubscriber implements EventSubscriber
{
 /* (non-PHPdoc)
     * @see \Symfony\Component\EventDispatcher\Tests\Debug\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        // TODO Auto-generated method stub
        return array(Events::prePersist);
    }
    
    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        if($entity instanceof Actor){
            echo __CLASS__;
        }
    }

    
}

?>