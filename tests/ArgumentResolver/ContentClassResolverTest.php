<?php

namespace GeoSocio\HttpSerializer\ArgumentResolver;

use GeoSocio\HttpSerializer\ArgumentResolver\ContentClassResolver;
use GeoSocio\HttpSerializer\Loader\GroupLoaderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContentClassResolverTest extends TestCase
{
    /**
     * Test Supports
     */
    public function testSupports()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $serializer = $this->createMock(SerializerInterface::class);
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects($this->once())
            ->method('supportsDenormalization')
            ->with($content, 'stdClass', 'test')
            ->willReturn(true);

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->once())
            ->method('supportsDecoding')
            ->with('test')
            ->willReturn(true);

        $loader = $this->createMock(GroupLoaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $loader,
            $eventDispatcher,
            $validator
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->exactly(2))
            ->method('getContent')
            ->willReturn($content);
        $request->expects($this->exactly(2))
            ->method('getRequestFormat')
            ->willReturn('test');
        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn(Request::METHOD_POST);

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->exactly(3))
            ->method('getType')
            ->willReturn(\stdClass::class);

        $result = $resolver->supports($request, $metadata);

        $this->assertTrue($result);
    }

    /**
     * Test Supports
     */
    public function testSupportsNotContent()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects($this->never())
            ->method('supportsDenormalization');

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->never())
            ->method('supportsDecoding');

        $loader = $this->createMock(GroupLoaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $loader,
            $eventDispatcher,
            $validator
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn(null);
        $request->expects($this->never())
            ->method('getRequestFormat');
        $request->expects($this->never())
            ->method('getMethod');

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
    public function testSupportsUnsupportedMethod()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $serializer = $this->createMock(SerializerInterface::class);
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects($this->never())
            ->method('supportsDenormalization');

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->never())
            ->method('supportsDecoding');

        $loader = $this->createMock(GroupLoaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $loader,
            $eventDispatcher,
            $validator
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($content);
        $request->expects($this->never())
            ->method('getRequestFormat');
        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn(Request::METHOD_PATCH);

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
    public function testSupportsUnsupportedDeecoderFormat()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $serializer = $this->createMock(SerializerInterface::class);
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects($this->never())
            ->method('supportsDenormalization');

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->once())
            ->method('supportsDecoding')
            ->with('test')
            ->willReturn(false);

        $loader = $this->createMock(GroupLoaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $loader,
            $eventDispatcher,
            $validator
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($content);
        $request->expects($this->once())
            ->method('getRequestFormat')
            ->willReturn('test');
        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn(Request::METHOD_POST);

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
    public function testSupportsInternalType()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $serializer = $this->createMock(SerializerInterface::class);
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects($this->never())
            ->method('supportsDenormalization');

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->once())
            ->method('supportsDecoding')
            ->with('test')
            ->willReturn(true);

        $loader = $this->createMock(GroupLoaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $loader,
            $eventDispatcher,
            $validator
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($content);
        $request->expects($this->once())
            ->method('getRequestFormat')
            ->willReturn('test');
        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn(Request::METHOD_POST);

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
     * Test Supports
     */
    public function testSupportsNonExistantClass()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $serializer = $this->createMock(SerializerInterface::class);
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects($this->never())
            ->method('supportsDenormalization');

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->once())
            ->method('supportsDecoding')
            ->with('test')
            ->willReturn(true);

        $loader = $this->createMock(GroupLoaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $loader,
            $eventDispatcher,
            $validator
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($content);
        $request->expects($this->once())
            ->method('getRequestFormat')
            ->willReturn('test');
        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn(Request::METHOD_POST);

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->exactly(2))
            ->method('getType')
            ->willReturn('\NonExistant');

        $result = $resolver->supports($request, $metadata);

        $this->assertFalse($result);
    }

    /**
     * Test Supports
     */
    public function testSupportsUnsupportedDenormalization()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $serializer = $this->createMock(SerializerInterface::class);
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects($this->once())
            ->method('supportsDenormalization')
            ->with($content, 'stdClass', 'test')
            ->willReturn(false);

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->once())
            ->method('supportsDecoding')
            ->with('test')
            ->willReturn(true);

        $loader = $this->createMock(GroupLoaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $loader,
            $eventDispatcher,
            $validator
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->exactly(2))
            ->method('getContent')
            ->willReturn($content);
        $request->expects($this->exactly(2))
            ->method('getRequestFormat')
            ->willReturn('test');
        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn(Request::METHOD_POST);

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->exactly(3))
            ->method('getType')
            ->willReturn(\stdClass::class);

        $result = $resolver->supports($request, $metadata);

        $this->assertFalse($result);
    }

    /**
     * Test Resolve
     */
    public function testResolve()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $decoder = $this->createMock(DecoderInterface::class);
        $loader = $this->createMock(GroupLoaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $loader,
            $eventDispatcher,
            $validator
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getRequestFormat')
            ->willReturn('test');

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->once())
            ->method('getType')
            ->willReturn('test');

        $result = $resolver->resolve($request, $metadata)->next();

        $this->assertNull($result);
    }
}
