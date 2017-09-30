<?php

namespace GeoSocio\HttpSerializer\GroupResolver;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Group Resolver Manager.
 */
class RequestGroupResolverManagerTest extends TestCase
{
    /**
     * Test resolve.
     */
    public function testResolve()
    {
        $manager = new RequestGroupResolverManager();

        $resolver = $this->createMock(RequestGroupResolverInterface::class);
        $this->assertSame($manager, $manager->addResolver($resolver));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertEmpty($manager->resolve($request, \stdClass::class));
    }

    /**
     * Test resolve.
     */
    public function testResolveFalse()
    {
        $manager = new RequestGroupResolverManager();

        $resolver = $this->createMock(RequestGroupResolverInterface::class);
        $this->assertSame($manager, $manager->addResolver($resolver));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertEmpty($manager->resolve($request, \stdClass::class));
    }

    /**
     * Test resolve.
     */
    public function testSupports()
    {
        $manager = new RequestGroupResolverManager();

        $resolver = $this->createMock(RequestGroupResolverInterface::class);
        $resolver->expects($this->once())
           ->method('supports')
           ->willReturn(true);

        $this->assertSame($manager, $manager->addResolver($resolver));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertTrue($manager->supports($request, \stdClass::class));
    }

    /**
     * Test resolve.
     */
    public function testSupportsFalse()
    {
        $manager = new RequestGroupResolverManager();

        $resolver = $this->createMock(RequestGroupResolverInterface::class);

        $this->assertSame($manager, $manager->addResolver($resolver));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertFalse($manager->supports($request, \stdClass::class));
    }
}
