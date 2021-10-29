<?php

namespace MichaelRubel\EnhancedContainer\Tests;

use MichaelRubel\EnhancedContainer\Tests\Boilerplate\BoilerplateInterface;
use MichaelRubel\EnhancedContainer\Tests\Boilerplate\BoilerplateService;
use MichaelRubel\EnhancedContainer\Tests\Boilerplate\Services\TestService;

class HelpersTest extends TestCase
{
    /** @test */
    public function testCanSetBindItself()
    {
        bind(BoilerplateService::class)->itself();

        $boilerplateOne = resolve(BoilerplateService::class);
        $boilerplateTwo = resolve(BoilerplateService::class);

        $this->assertNotSame($boilerplateOne, $boilerplateTwo);
    }

    /** @test */
    public function testCanSetSingleton()
    {
        singleton(BoilerplateService::class);

        $boilerplateOne = resolve(BoilerplateService::class);
        $boilerplateTwo = resolve(BoilerplateService::class);

        $this->assertSame($boilerplateOne, $boilerplateTwo);
    }

    /** @test */
    public function testCanSetScopedInstance()
    {
        scoped(BoilerplateService::class);

        $boilerplateOne = resolve(BoilerplateService::class);
        $boilerplateTwo = resolve(BoilerplateService::class);

        $this->assertSame($boilerplateOne, $boilerplateTwo);
    }

    /** @test */
    public function testCanExtendAbstractTypeUsingHelper()
    {
        bind(BoilerplateInterface::class)->to(BoilerplateService::class);

        extend(BoilerplateInterface::class, function ($service) {
            $this->assertTrue($service->testProperty);

            $service->testProperty = false;

            return $service;
        });

        $this->assertFalse(
            call(BoilerplateInterface::class)->testProperty
        );
    }

    /** @test */
    public function testCanCheckIfMethodForwardingEnabled()
    {
        config(['enhanced-container.forwarding_enabled' => true]);

        $this->assertTrue(
            isForwardingEnabled()
        );
    }

    /** @test */
    public function testCanEnableMethodForwarding()
    {
        enableMethodForwarding();

        $this->assertTrue(
            config('enhanced-container.forwarding_enabled')
        );
    }

    /** @test */
    public function testCanDisableMethodForwarding()
    {
        disableMethodForwarding();

        $this->assertFalse(
            config('enhanced-container.forwarding_enabled')
        );
    }

    /** @test */
    public function testCanRunSomethingWithForwardingEnabled()
    {
        $call = runWithForwarding(function () {
            return call(TestService::class)->nonExistingMethod();
        });

        $this->assertTrue($call);
    }

    /** @test */
    public function testCanRunSomethingWithoutForwarding()
    {
        $call = runWithoutForwarding(function () {
            return call(BoilerplateService::class)->test();
        });

        $this->assertTrue($call);
    }
}
