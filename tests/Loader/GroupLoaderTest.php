<?php

namespace GeoSocio\HttpSerializer\Loader;

use Doctrine\Common\Annotations\Reader;
use GeoSocio\HttpSerializer\Annotation\RequestGroups;
use GeoSocio\HttpSerializer\Annotation\ResponseGroups;
use GeoSocio\HttpSerializer\Loader\GroupLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

class GroupLoaderTest extends TestCase
{
    /**
     * Test Get Request Groups.
     */
    public function testGetRequestGroups()
    {
        $annotations = $this->getAnnotations();

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolver = $this->createMock(ControllerResolverInterface::class);
        $resolver->expects($this->once())
            ->method('getController')
            ->willReturn([
                GroupLoader::class,
                'getRequestGroups'
            ]);

        $reader = $this->createMock(Reader::class);
        $reader->expects($this->once())
            ->method('getMethodAnnotations')
            ->willReturn($annotations);

        $loader = new GroupLoader($resolver, $reader);

        $groups = $loader->getRequestGroups($request);

        $this->assertInternalType('array', $groups);
        $this->assertCount(2, $groups);
        $this->assertEquals('test', $groups[0]);
        $this->assertEquals('test2', $groups[1]);
    }

    /**
     * Test Get Response Groups with no annotations.
     */
    public function testGetRequestGroupsNoAnnotations()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolver = $this->createMock(ControllerResolverInterface::class);
        $resolver->expects($this->once())
            ->method('getController')
            ->willReturn([
                GroupLoader::class,
                'getRequestGroups'
            ]);

        $reader = $this->createMock(Reader::class);
        $reader->expects($this->once())
            ->method('getMethodAnnotations')
            ->willReturn([]);

        $loader = new GroupLoader($resolver, $reader);

        $groups = $loader->getRequestGroups($request);

        $this->assertNull($groups);
    }

    /**
     * Test Get Request Groups with no controller.
     */
    public function testGetRequestGroupsNoController()
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

        $loader = new GroupLoader($resolver, $reader);

        $groups = $loader->getRequestGroups($request);

        $this->assertNull($groups);
    }

    /**
     * Test Get Response Groups.
     */
    public function testGetResponseGroups()
    {
        $annotations = $this->getAnnotations();

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolver = $this->createMock(ControllerResolverInterface::class);
        $resolver->expects($this->once())
            ->method('getController')
            ->willReturn([
                GroupLoader::class,
                'getRequestGroups'
            ]);

        $reader = $this->createMock(Reader::class);
        $reader->expects($this->once())
            ->method('getMethodAnnotations')
            ->willReturn($annotations);

        $loader = new GroupLoader($resolver, $reader);

        $groups = $loader->getResponseGroups($request);

        $this->assertInternalType('array', $groups);
        $this->assertCount(2, $groups);
        $this->assertEquals('test', $groups[0]);
        $this->assertEquals('test3', $groups[1]);
    }

    public function getAnnotations()
    {
        return [
            new Groups([
                'value' => [
                    'test',
                ]
            ]),
            new RequestGroups([
                'value' => [
                    'test'
                ]
            ]),
            new RequestGroups([
                'value' => [
                    'test2'
                ]
            ]),
            new ResponseGroups([
                'value' => [
                    'test3'
                ]
            ]),
            new MaxDepth([
                'value' => 1,
            ]),
        ];
    }
}
