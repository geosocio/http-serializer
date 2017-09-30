<?php

namespace GeoSocio\HttpSerializer\ArgumentResolver;

use GeoSocio\HttpSerializer\ArgumentResolver\ContentClassResolver;
use GeoSocio\HttpSerializer\Exception\ConstraintViolationException;
use GeoSocio\HttpSerializer\GroupResolver\RequestGroupResolverInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
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

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $groupResolver = $this->createMock(RequestGroupResolverInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $eventDispatcher,
            $validator,
            $groupResolver
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

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $groupResolver = $this->createMock(RequestGroupResolverInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $eventDispatcher,
            $validator,
            $groupResolver
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

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $groupResolver = $this->createMock(RequestGroupResolverInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $eventDispatcher,
            $validator,
            $groupResolver
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

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $groupResolver = $this->createMock(RequestGroupResolverInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $eventDispatcher,
            $validator,
            $groupResolver
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

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $groupResolver = $this->createMock(RequestGroupResolverInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $eventDispatcher,
            $validator,
            $groupResolver
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

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $groupResolver = $this->createMock(RequestGroupResolverInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $eventDispatcher,
            $validator,
            $groupResolver
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

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $groupResolver = $this->createMock(RequestGroupResolverInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $eventDispatcher,
            $validator,
            $groupResolver
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

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $groupResolver = $this->createMock(RequestGroupResolverInterface::class);
        $groupResolver->expects($this->once())
            ->method('resolve')
            ->willReturn([]);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $eventDispatcher,
            $validator,
            $groupResolver
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
        $metadata->expects($this->exactly(2))
            ->method('getType')
            ->willReturn('test');

        $result = $resolver->resolve($request, $metadata)->next();

        $this->assertNull($result);
    }

    /**
     * Test Resolve
     */
    public function testResolveErrors()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $decoder = $this->createMock(DecoderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $errorList = $this->createMock(ConstraintViolationListInterface::class);
        $errorList->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn($errorList);
        $groupResolver = $this->createMock(RequestGroupResolverInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $eventDispatcher,
            $validator,
            $groupResolver
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
        $metadata->expects($this->exactly(2))
            ->method('getType')
            ->willReturn('test');

        $this->expectException(ConstraintViolationException::class);
        $result = $resolver->resolve($request, $metadata)->next();
    }
}
