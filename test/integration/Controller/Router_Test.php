<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Test\Controller;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Route\ConfigInterface as RouteConfigInterface;
use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Controller;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Router_IntegrationTest extends \Praxigento\Core\Test\BaseIntegrationTest
{

    public function test_actionControllers_back()
    {
        /** @var \Magento\Backend\App\Router $backendRouter */
        $backendRouter = $this->_manObj->create(\Magento\Backend\App\Router::class);
        /* /admin/catalog/lots/ */
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->_manObj->create(\Magento\Framework\App\RequestInterface::class);
        $request->setPathInfo('/admin/catalog/lots/');
        $actualAction = $backendRouter->match($request);
        $this->assertInstanceOf(Controller\Adminhtml\Lots\Index::class, $actualAction);
        $request = $this->_manObj->create(\Magento\Framework\App\RequestInterface::class);
        /* /admin/catalog/warehouses/ */
        $request->setPathInfo('/admin/catalog/warehouses/');
        $actualAction = $backendRouter->match($request);
        $this->assertInstanceOf(Controller\Adminhtml\Warehouses\Index::class, $actualAction);
    }

    public function test_routeNames()
    {
        /** @var RouteConfigInterface $routeConfig */
        $routeConfig = $this->_manObj->create(RouteConfigInterface::class);
        /* backend (adminhtml) */
        $modules = $routeConfig->getModulesByFrontName(Cfg::ROUTE_NAME_ADMIN_CATALOG);
        $this->assertContains(Cfg::MODULE, $modules);
    }
}