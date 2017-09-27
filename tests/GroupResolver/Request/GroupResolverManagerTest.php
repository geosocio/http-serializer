<?php

namespace GeoSocio\HttpSerializer\GroupResolver\Request;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Group Resolver Manager.
 */
class GroupResolverManagerTest extends TestCase
{
    /**
     * Test resolve.
     */
    public function testResolve()
    {
        $manager = new GroupResolverManager();

        $resolver = $this->createMock(GroupResolverInterface::class);
        $this->assertSame($manager, $manager->addResolver($resolver));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertEmpty($manager->resolve($request, \stdClass::class));
    }
}
