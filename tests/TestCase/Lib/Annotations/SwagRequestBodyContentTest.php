<?php


namespace SwaggerBake\Test\TestCase\Lib\Annotations;

use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;
use Cake\TestSuite\TestCase;
use SwaggerBake\Lib\AnnotationLoader;
use SwaggerBake\Lib\EntityScanner;
use SwaggerBake\Lib\RouteScanner;
use SwaggerBake\Lib\Configuration;
use SwaggerBake\Lib\Swagger;

class SwagRequestBodyContentTest extends TestCase
{
    public $fixtures = [
        'plugin.SwaggerBake.Employees',
    ];

    private $router;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $router = new Router();
        $router::scope('/api', function (RouteBuilder $builder) {
            $builder->setExtensions(['json']);
            $builder->resources('SwagRequestBodyContent', [
                'map' => [
                    'textPlain' => [
                        'action' => 'textPlain',
                        'method' => 'POST',
                        'path' => 'text-plain'
                    ],
                    'multipleMimeTypes' => [
                        'action' => 'multipleMimeTypes',
                        'method' => 'POST',
                        'path' => 'multiple-mime-types'
                    ],
                    'useConfigDefaults' => [
                        'action' => 'useConfigDefaults',
                        'method' => 'POST',
                        'path' => 'use-config-defaults'
                    ],
                ]
            ]);
        });
        $this->router = $router;

        $this->config = new Configuration([
            'prefix' => '/api',
            'yml' => '/config/swagger-bare-bones.yml',
            'json' => '/webroot/swagger.json',
            'webPath' => '/swagger.json',
            'hotReload' => false,
            'exceptionSchema' => 'Exception',
            'requestAccepts' => ['application/x-www-form-urlencoded','application/xml','application/json'],
            'responseContentTypes' => ['application/json'],
            'namespaces' => [
                'controllers' => ['\SwaggerBakeTest\App\\'],
                'entities' => ['\SwaggerBakeTest\App\\'],
                'tables' => ['\SwaggerBakeTest\App\\'],
            ]
        ], SWAGGER_BAKE_TEST_APP);

        AnnotationLoader::load();
    }

    public function testSwagRequestBodyContent()
    {
        $cakeRoute = new RouteScanner($this->router, $this->config);

        $swagger = new Swagger(new EntityScanner($cakeRoute, $this->config));
        $arr = json_decode($swagger->toString(), true);

        $operation = $arr['paths']['/swag-request-body-content/text-plain']['post'];

        $this->assertArrayHasKey('schema', $operation['requestBody']['content']['text/plain']);
    }

    public function testMultipleMimeTypes()
    {
        $cakeRoute = new RouteScanner($this->router, $this->config);

        $swagger = new Swagger(new EntityScanner($cakeRoute, $this->config));
        $arr = json_decode($swagger->toString(), true);

        $operation = $arr['paths']['/swag-request-body-content/multiple-mime-types']['post'];

        $this->assertCount(2, $operation['requestBody']['content']);
    }

    public function testUseMimeTypesFromConfig()
    {
        $cakeRoute = new RouteScanner($this->router, $this->config);

        $swagger = new Swagger(new EntityScanner($cakeRoute, $this->config));
        $arr = json_decode($swagger->toString(), true);

        $operation = $arr['paths']['/swag-request-body-content/use-config-defaults']['post'];

        $this->assertCount(3, $operation['requestBody']['content']);
    }
}