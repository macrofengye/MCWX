<?php
namespace Blog\listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Entity\Actor;
use event\TestEvent;

class PrePersisterExampleListener
{

    public function prePersist(Actor $actor, LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $em = $args->getObjectManager();
    }

    public function preFlush(Actor $actor, PreFlushEventArgs $args)
    {
        $maps = $args->getEntityManager()->getUnitOfWork();
        echo "<br/>aaaaaa";
    }

    public function preFoo(Actor $actor , TestEvent $args){
        echo "My hahahhaha";
    }
}

?>