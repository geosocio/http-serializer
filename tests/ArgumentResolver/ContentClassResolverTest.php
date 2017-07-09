<?php

namespace GeoSocio\Tests\HttpSerializer\ArgumentResolver;

use GeoSocio\HttpSerializer\ArgumentResolver\ContentClassResolver;
use GeoSocio\HttpSerializer\Loader\GroupLoaderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ContentClassResolverTest extends TestCase
{
    public function testSupports()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $serializer = $this->createMock(SerializerInterface::class);
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects($this->once())
            ->method('supportsDenormalization')
            ->with($content, 'test')
            ->willReturn(true);

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->once())
            ->method('supportsDecoding')
            ->with('test')
            ->willReturn(true);

        $loader = $this->createMock(GroupLoaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $resolver = new ContentClassResolver(
            $serializer,
            $denormalizer,
            $decoder,
            $loader,
            $eventDispatcher
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

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->exactly(2))
            ->method('getType')
            ->willReturn(\stdClass::class);

        $result = $resolver->supports($request, $metadata);

        $this->assertTrue($result);
    }
}
