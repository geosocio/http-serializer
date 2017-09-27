<?php

namespace GeoSocio\HttpSerializer\Loader;

use Doctrine\Common\Annotations\Reader;
use GeoSocio\HttpSerializer\Loader\GroupLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ResponseGroupLoaderTest extends GroupLoaderTest
{

    /**
     * Test Get Response Groups.
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
                ResponseGroupLoader::class,
                'resolve'
            ]);

        $reader = $this->createMock(Reader::class);
        $reader->expects($this->once())
            ->method('getMethodAnnotations')
            ->willReturn($annotations);

        $loader = new ResponseGroupLoader($resolver, $reader);

        $groups = $loader->resolve($request, new \stdClass());

        $this->assertInternalType('array', $groups);
        $this->assertCount(2, $groups);
        $this->assertEquals('test', $groups[0]);
        $this->assertEquals('test3', $groups[1]);
    }
}
