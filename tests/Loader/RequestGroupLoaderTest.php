<?php

namespace GeoSocio\HttpSerializer\Loader;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class RequestGroupLoaderTest extends GroupLoaderTest
{
    /**
     * Test Get Request Groups.
     */
    public function testResolve()
    {
        $annotations = $this->getAnnotations();

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolver = $this->createMock(ControllerResolverInterface::class);
        $resolver->expects($this->once())
            ->method('getController')
            ->willReturn([
                RequestGroupLoader::class,
                'resolve'
            ]);

        $reader = $this->createMock(Reader::class);
        $reader->expects($this->once())
            ->method('getMethodAnnotations')
            ->willReturn($annotations);

        $loader = new RequestGroupLoader($resolver, $reader);

        $groups = $loader->resolve($request, \stdClass::class);

        $this->assertInternalType('array', $groups);
        $this->assertCount(2, $groups);
        $this->assertEquals('test', $groups[0]);
        $this->assertEquals('test2', $groups[1]);
    }

    /**
     * Test Get Response Groups with no annotations.
     */
    public function testResolveNoAnnotations()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolver = $this->createMock(ControllerResolverInterface::class);
        $resolver->expects($this->once())
            ->method('getController')
            ->willReturn([
                RequestGroupLoader::class,
                'resolve'
            ]);

        $reader = $this->createMock(Reader::class);
        $reader->expects($this->once())
            ->method('getMethodAnnotations')
            ->willReturn([]);

        $loader = new RequestGroupLoader($resolver, $reader);

        $groups = $loader->resolve($request, \stdClass::class);

        $this->assertEmpty($groups);
    }

    /**
     * Test Get Request Groups with no controller.
     */
    public function testResolveNoController()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolver = $this->createMock(ControllerResolverInterface::class);
        $resolver->expects($this->once())
            ->method('getController')
            ->willReturn(null);

        $reader = $this->createMock(Reader::class);
        $reader->expects($this->never())
            ->method('getMethodAnnotations');

        $loader = new RequestGroupLoader($resolver, $reader);

        $groups = $loader->resolve($request, \stdClass::class);

        $this->assertEmpty($groups);
    }

    /**
     * Test Get Response Groups Suports.
     */
    public function testSupoorts()
    {
        $resolver = $this->createMock(ControllerResolverInterface::class);
        $reader = $this->createMock(Reader::class);
        $loader = new RequestGroupLoader($resolver, $reader);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertTrue($loader->supports($request, \stdClass::class));
    }
}
