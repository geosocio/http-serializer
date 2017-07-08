<?php

namespace GeoSocio\Tests\HttpSerializer\EventListener;

use GeoSocio\HttpSerializer\EventListener\KernelViewListener;
use GeoSocio\HttpSerializer\EventListener\KernelExceptionListener;
use GeoSocio\HttpSerializer\Loader\GroupLoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use PHPUnit\Framework\TestCase;

class KernelExceptionListenerTest extends TestCase
{

    public function testOnKernelException()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $normalizer = $this->createMock(NormalizerInterface::class);
        $normalizer->expects($this->once())
            ->method('supportsNormalization')
            ->willReturn(true);

        $encoder = $this->createMock(EncoderInterface::class);
        $encoder->expects($this->once())
            ->method('supportsEncoding')
            ->willReturn(true);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $listener = new KernelExceptionListener($serializer, $normalizer, $encoder, $eventDispatcher);

        $exception = $this->getMockBuilder(\Exception::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getRequestFormat')
            ->willReturn('test');

        $event = $this->getMockBuilder(GetResponseForExceptionEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->once())
            ->method('getException')
            ->willReturn($exception);

        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $response = $listener->onKernelException($event);

        $this->assertInstanceOf(Response::class, $response);
    }
}
