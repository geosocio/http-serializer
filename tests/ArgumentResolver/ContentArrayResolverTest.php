<?php

namespace GeoSocio\HttpSerializer\ArgumentResolver;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class ContentArrayResolverTest extends TestCase
{

    /**
     * Test Supports
     */
    public function testSupports()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->once())
            ->method('supportsDecoding')
            ->with('test')
            ->willReturn(true);

        $resolver = new ContentArrayResolver($decoder);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($content);
        $request->expects($this->once())
            ->method('getRequestFormat')
            ->willReturn('test');

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->once())
            ->method('getType')
            ->willReturn('array');

        $result = $resolver->supports($request, $metadata);

        $this->assertTrue($result);
    }

    /**
     * Test Supports
     */
    public function testSupportsNoContent()
    {
        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->never())
            ->method('supportsDecoding');

        $resolver = new ContentArrayResolver($decoder);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn(null);
        $request->expects($this->never())
            ->method('getRequestFormat');

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->never())
            ->method('getType');

        $result = $resolver->supports($request, $metadata);

        $this->assertFalse($result);
    }

    /**
     * Test Supports
     */
    public function testSupportsNotArray()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->never())
            ->method('supportsDecoding');

        $resolver = new ContentArrayResolver($decoder);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($content);
        $request->expects($this->never())
            ->method('getRequestFormat');

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->once())
            ->method('getType')
            ->willReturn('string');

        $result = $resolver->supports($request, $metadata);

        $this->assertFalse($result);
    }

    /**
     * Test Supports
     */
    public function testSupportsUnsupportedFormat()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->once())
            ->method('supportsDecoding')
            ->with('test')
            ->willReturn(false);

        $resolver = new ContentArrayResolver($decoder);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($content);
        $request->expects($this->once())
            ->method('getRequestFormat')
            ->willReturn('test');

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->once())
            ->method('getType')
            ->willReturn('array');

        $result = $resolver->supports($request, $metadata);

        $this->assertFalse($result);
    }

    /**
     * Test Resolve
     */
    public function testResolve()
    {
        $decoder = $this->createMock(DecoderInterface::class);

        $resolver = new ContentArrayResolver($decoder);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();

        $result = $resolver->resolve($request, $metadata)->next();

        $this->assertNull($result);
    }
}
