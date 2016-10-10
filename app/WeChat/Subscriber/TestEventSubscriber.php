<?php
namespace Blog\subscriber;

use Doctrine\Common\EventSubscriber;
use Blog\event\TestEvent;
use Boot\Bootstrap;
use Respect\Validation\Validator;

class TestEventSubscriber implements EventSubscriber
{

    public $preFooInvoked = false;

    public function getSubscribedEvents()
    {
        return array(
            TestEvent::preFoo
        );
    }

    public function preFoo($args)
    {
        $filter = new \Zend\I18n\Filter\Alpha();
        $number = '12222sss333';
       /* echo Validator::string()->validate($number);
        echo "<br/>this is : " . Bootstrap::getApp()->container->get('v')
            ->string()
            ->validate($number) . "<br/>";*/
       // $args->obj->setFirstName($filter->filter("kkkkk 123456"));
        print_r($args->obj);
        $this->preFooInvoked = true;
        echo "adadsdad";
    }
}

?>