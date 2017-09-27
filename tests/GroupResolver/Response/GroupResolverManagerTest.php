<?php

namespace GeoSocio\HttpSerializer\GroupResolver\Response;

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

        $this->assertEmpty($manager->resolve(new \stdClass()));
    }

    /**
     * Test resolve.
     */
    public function testSupports()
    {
        $manager = new GroupResolverManager();

        $resolver = $this->createMock(GroupResolverInterface::class);
        $resolver->expects($this->once())
           ->method('supports')
           ->willReturn(true);

        $this->assertSame($manager, $manager->addResolver($resolver));

        $this->assertTrue($manager->supports(new \stdClass()));
    }

    /**
     * Test resolve.
     */
    public function testSupportsFalse()
    {
        $manager = new GroupResolverManager();

        $resolver = $this->createMock(GroupResolverInterface::class);

        $this->assertSame($manager, $manager->addResolver($resolver));

        $this->assertFalse($manager->supports(new \stdClass()));
    }
}
