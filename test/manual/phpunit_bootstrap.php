<?php
/**
 * Check Magento version by key file "./app/Mage.php"
 */
if(is_file(__DIR__ . '/../../../../../htdocs/app/Mage.php')) {
    /* Start M1 app */
    include_once(__DIR__ . '/../../../../../htdocs/app/Mage.php');
    // Start Magento application
    Mage::app('default');
    // Avoid issues "Headers already send"
    session_start();
} else {
    /* Start M2 app */
    /* BP is defined in Magento's ./app/autoload.php */
    if(!defined('BP')) {
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
}
