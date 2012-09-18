<?php

if ( ! is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    throw new \LogicException('Could not find vendor/autoload.php. Did you run "composer install --dev"?');
}

require_once $autoloadFile;

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

// http://kriswallsmith.net/post/1338263070/how-to-test-a-symfony2-bundle
//require_once $_SERVER['SYMFONY'].'/Symfony/Component/HttpFoundation/UniversalClassLoader.php';
//
//use Symfony\Component\HttpFoundation\UniversalClassLoader;
//
//$loader = new UniversalClassLoader();
//$loader->registerNamespace('Symfony', $_SERVER['SYMFONY']);
//$loader->register();
//
//spl_autoload_register(function($class)
//{
//    if (0 === strpos($class, 'DigitalPioneers\\PheanstalkBundle\\')) {
//        $path = implode('/', array_slice(explode('\\', $class), 3)).'.php';
//        require_once __DIR__.'/../'.$path;
//        return true;
//    }
//});