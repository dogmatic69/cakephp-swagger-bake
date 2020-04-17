<?php


namespace SwaggerBake\Test\TestCase\Lib;


use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;
use Cake\TestSuite\TestCase;
use SwaggerBake\Lib\AnnotationLoader;
use SwaggerBake\Lib\CakeModel;
use SwaggerBake\Lib\CakeRoute;
use SwaggerBake\Lib\Configuration;
use SwaggerBake\Lib\Swagger;

class SwaggerPathTest extends TestCase
{
    public $fixtures = [
        'plugin.SwaggerBake.Employees',
        'plugin.SwaggerBake.EmployeeTitles',
    ];

    private $router;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $router = new Router();
        $router::scope('/api', function (RouteBuilder $builder) {
            $builder->setExtensions(['json']);
            $builder->resources('Employees', function (RouteBuilder $routes) {
                $routes->resources('EmployeeTitles');
            });
        });
        $this->router = $router;

        AnnotationLoader::load();
    }

    public function testPathInvisible()
    {
        $config = new Configuration([
            'prefix' => '/api',
            'yml' => '/config/swagger-bare-bones.yml',
            'json' => '/webroot/swagger.json',
            'webPath' => '/swagger.json',
            'hotReload' => false,
            'namespaces' => [
                'controllers' => ['\SwaggerBakeTest\App\\'],
                'entities' => ['\SwaggerBakeTest\App\\']
            ]
        ], SWAGGER_BAKE_TEST_APP);

        $cakeRoute = new CakeRoute($this->router, $config);

        $swagger = new Swagger(new CakeModel($cakeRoute, $config));

        $arr = json_decode($swagger->toString(), true);

        $this->assertArrayNotHasKey('/employee-titles', $arr['paths']);
    }
}