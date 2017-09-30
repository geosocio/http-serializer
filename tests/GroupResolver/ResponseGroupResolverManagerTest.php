<?php

namespace GeoSocio\HttpSerializer\GroupResolver;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Group Resolver Manager.
 */
class ResponseGroupResolverManagerTest extends TestCase
{
    /**
     * Test resolve.
     */
    public function testResolve()
    {
        $manager = new ResponseGroupResolverManager();

        $resolver = $this->createMock(ResponseGroupResolverInterface::class);
        $resolver->expects($this->once())
           ->method('supports')
           ->willReturn(true);

        $this->assertSame($manager, $manager->addResolver($resolver));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertEmpty($manager->resolve($request, new \stdClass()));
    }

    /**
     * Test resolve.
     */
    public function testResolveFalse()
    {
        $manager = new ResponseGroupResolverManager();

        $resolver = $this->createMock(ResponseGroupResolverInterface::class);
        $this->assertSame($manager, $manager->addResolver($resolver));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertEmpty($manager->resolve($request, new \stdClass()));
    }

    /**
     * Test resolve.
     */
    public function testSupports()
    {
        $manager = new ResponseGroupResolverManager();

        $resolver = $this->createMock(ResponseGroupResolverInterface::class);
        $resolver->expects($this->once())
           ->method('supports')
           ->willReturn(true);

        $this->assertSame($manager, $manager->addResolver($resolver));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertTrue($manager->supports($request, new \stdClass()));
    }

    /**
     * Test resolve.
     */
    public function testSupportsFalse()
    {
        $manager = new ResponseGroupResolverManager();

        $resolver = $this->createMock(ResponseGroupResolverInterface::class);

        $this->assertSame($manager, $manager->addResolver($resolver));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertFalse($manager->supports($request, new \stdClass()));
    }
}
