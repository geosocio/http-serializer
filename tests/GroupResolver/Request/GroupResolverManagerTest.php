<?php

namespace GeoSocio\HttpSerializer\GroupResolver\Response;

use PHPUnit\Framework\TestCase;

/**
 * Group Resolver Manager.
 */
class GroupResolverManagerTest extends TestCase
{
    public function testAddResolver()
    {
        $manager = new GroupResolverManager();

        $resolver = $this->createMock(GroupResolverInterface::class);
        $this->assertSame($manager, $manager->addResolver($resolver));
    }
}
