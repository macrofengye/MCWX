<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-9-28
 * Time: 下午3:04
 */

namespace Core\Providers;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Validator\Validation;

class ValidatorService implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['validator'] = function (Container $container) {
            //AnnotationRegistry::registerFile(ROOT_PATH . "/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
            //AnnotationRegistry::registerAutoloadNamespace("Symfony\Component\Validator\Constraint", ROOT_PATH."/entity/Models");
            //AnnotationRegistry::registerAutoloadNamespace("Entity\\Models\\", ROOT_PATH . "/entity/Models");
            AnnotationRegistry::registerLoader('class_exists');
            $reader = new AnnotationReader();
            AnnotationReader::addGlobalIgnoredName('dummy');
            return Validation::createValidatorBuilder()->enableAnnotationMapping($reader)->getValidator();
        };
    }
}