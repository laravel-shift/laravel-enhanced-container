<?php

namespace MichaelRubel\EnhancedContainer\Tests;

use MichaelRubel\EnhancedContainer\Core\BindingBuilder;
use MichaelRubel\EnhancedContainer\Tests\Boilerplate\BoilerplateInterface;
use MichaelRubel\EnhancedContainer\Tests\Boilerplate\BoilerplateService;

class MethodBindingTest extends TestCase
{
    /** @test */
    public function testCanOverrideMethodAsString()
    {
        bind(BoilerplateService::class)->method()->test(fn () => 'overridden');

        $call = call(BoilerplateService::class)->test('test', 1);

        $this->assertEquals('overridden', $call);
    }

    /** @test */
    public function testCanOverrideMethodAsObject()
    {
        bind(new BoilerplateService())->method()->test(fn () => collect('illuminate'));

        $call = call(BoilerplateService::class)->test('test', 1);

        $this->assertEquals(
            collect('illuminate'),
            $call
        );
    }

    /** @test */
    public function testCanOverrideMethodUsingService()
    {
        bind(new BoilerplateService())->method()->yourMethod(
            fn ($service, $app) => $service->yourMethod(100) + 1
        );

        $call = call(BoilerplateService::class)->yourMethod(100);

        $this->assertEquals(
            101,
            $call
        );
    }

    /** @test */
    public function testCanOverrideMethodUsingInterface()
    {
        bind(BoilerplateInterface::class)->to(BoilerplateService::class);
        bind(BoilerplateInterface::class)->method()->yourMethod(
            fn ($service, $app) => $service->yourMethod(100) + 1
        );

        $call = call(BoilerplateService::class)->yourMethod(100);

        $this->assertEquals(
            101,
            $call
        );
    }

    /** @test */
    public function testCanOverrideMethodWithInterfaceAlternativeSyntax()
    {
        bind(BoilerplateInterface::class)->to(BoilerplateService::class);
        bind(BoilerplateInterface::class)->method(
            'yourMethod',
            fn ($service, $app) => $service->yourMethod(100) + 1
        );

        $call = call(BoilerplateService::class)->yourMethod(100);

        $this->assertEquals(
            101,
            $call
        );
    }

    /** @test */
    public function testBindMethodReturnsItselfIfOnlyMethodPassed()
    {
        bind(BoilerplateService::class)->method();

        $call = call(BoilerplateService::class)->yourMethod(100);

        $this->assertEquals(100, $call);
    }

    /** @test */
    public function testBindMethodReturnsItselfIfOnlyMethodPassedWithString()
    {
        bind(BoilerplateService::class)->method('yourMethod');

        $call = call(BoilerplateService::class)->yourMethod(100);

        $this->assertEquals(100, $call);
    }

    /** @test */
    public function testCanOverrideMethodUsingAnotherSyntax()
    {
        bind(BoilerplateService::class)->method('yourMethod', function ($service, $app, $params) {
            return $service->yourMethod($params['count']) + 1;
        });

        $call = call(BoilerplateService::class)->yourMethod(100);

        $this->assertEquals(
            101,
            $call
        );
    }

    /** @test */
    public function testCanOverrideMethodWithParameters()
    {
        bind(BoilerplateService::class)->method()->yourMethod(
            fn ($service, $app, $params) => $service->yourMethod($params['count']) + 1
        );

        $call = call(BoilerplateService::class)->yourMethod(100);

        $this->assertEquals(
            101,
            $call
        );
    }

    /** @test */
    public function testCanOverrideMethodWithParametersAndAddCondition()
    {
        bind(BoilerplateService::class)->method('yourMethod', function ($service, $app, $params) {
            if ($params['count'] === 100) {
                return $service->yourMethod($params['count']) + 1;
            }

            return false;
        });

        $call = call(BoilerplateService::class)->yourMethod(100);
        $this->assertSame(101, $call);

        $call = call(BoilerplateService::class)->yourMethod(200);
        $this->assertFalse($call);

        bind(BoilerplateService::class)->method()->yourMethod(function ($service, $app, $params) {
            return $params['count'] + 1;
        });
        $this->assertSame(2, call(BoilerplateService::class)->yourMethod(1));
    }

    /** @test */
    public function testCanOverrideMethodWhenReusingCallProxyInstance()
    {
        $callProxy = call(BoilerplateService::class);

        bind(BoilerplateService::class)
            ->method()
            ->yourMethod(
                fn ($service, $app) => $service->yourMethod(100) + 1
            );

        $test = $callProxy->yourMethod(100);

        $this->assertEquals(101, $test);

        bind(BoilerplateService::class)
            ->method()
            ->yourMethod(
                fn ($service, $app) => true
            );

        $test = $callProxy->yourMethod();

        $this->assertTrue($test);
    }

    /** @test */
    public function testCanResolveStringsInMethodBinding()
    {
        bind('test')->to(BoilerplateService::class);
        bind('test')->method('test', fn () => 'works');

        $this->assertEquals('works', call(BoilerplateService::class)->test());
    }

    /** @test */
    public function testTryingToResolveStringsDoesNotThrowExceptionIfNotBound()
    {
        bind('test')->method('test', fn () => 'works');

        $this->assertTrue(
            app()->hasMethodBinding('test@test')
        );
    }

    /** @test */
    public function testReturnsSelfWhenClosureIsNull()
    {
        $instance = bind('test')->method();
        $this->assertInstanceOf(BindingBuilder::class, $instance);

        $instance = bind('test')->method(null, fn () => true);
        $this->assertInstanceOf(BindingBuilder::class, $instance);

        $instance = bind('test')->method('test');
        $this->assertInstanceOf(BindingBuilder::class, $instance);
    }

    /** @test */
    public function testBindingBuilderExtension()
    {
        bind(BoilerplateInterface::class)->to(BoilerplateService::class);

        $builder = new TestBindingBuilder(BoilerplateInterface::class);
        $builder->method('test', fn () => 'test');
        $this->assertFalse(app()->hasMethodBinding(BoilerplateInterface::class . '@test'));
    }
}

class TestBindingBuilder extends BindingBuilder
{
    public function method(string $method = null, \Closure $override = null): self|null
    {
        $this->resolve();

        if (is_null($method) || is_null($override)) {
            return $this;
        }

        return $this->{$method}($override);
    }

    protected function resolve(): mixed
    {
        return parent::resolve();
    }
}
