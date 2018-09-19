<?php
/**
 * Include composer autoloader.
 * Path is relative to "./work/vendor/<vendor>/<module>/test/manual/" folder.
 */
include_once(__DIR__ . '/../../../../autoload.php');
/* Start M2 app */
/* BP is defined in Magento's ./app/autoload.php */
if (!defined('BP')) {
    include_once(__DIR__ . '/../../../../../app/bootstrap.php');
    /**
     * Create test application that initializes DB connection and ends w/o exiting
     *  ($response->terminateOnSend = false).
     */
    $params = $_SERVER;
    /** @var  $bootstrap \Magento\Framework\App\Bootstrap */
    $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
    /** @var  $app \Praxigento\Core\Test\App */
    $app = $bootstrap->createApplication(\Praxigento\Core\Test\App::class);
    $bootstrap->run($app);
}