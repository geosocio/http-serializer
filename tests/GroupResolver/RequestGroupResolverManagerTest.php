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
}
