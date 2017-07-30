<?php

namespace GeoSocio\HttpSerializer\EventListener;

use GeoSocio\HttpSerializer\EventListener\KernelExceptionListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use PHPUnit\Framework\TestCase;

class KernelExceptionListenerTest extends TestCase
{
    /**
     * Test Kernel Exception Event
     */
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

        $exception = $this->createMock(HttpExceptionInterface::class);

        $exception->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(403);
        $exception->expects($this->once())
            ->method('getHeaders')
            ->willReturn([]);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->exactly(2))
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

    /**
     * Test Kernel Exception Event
     */
    public function testOnKernelExceptionHasResponse()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $normalizer = $this->createMock(NormalizerInterface::class);
        $normalizer->expects($this->never())
            ->method('supportsNormalization');

        $encoder = $this->createMock(EncoderInterface::class);
        $encoder->expects($this->never())
            ->method('supportsEncoding');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $listener = new KernelExceptionListener($serializer, $normalizer, $encoder, $eventDispatcher);

        $exception = $this->getMockBuilder(\Exception::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->never())
            ->method('getRequestFormat');

        $event = $this->getMockBuilder(GetResponseForExceptionEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->once())
            ->method('hasResponse')
            ->willReturn(true);

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

        $event->expects($this->once())
            ->method('getException')
            ->willReturn($exception);

        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $this->assertSame($response, $listener->onKernelException($event));
    }

    /**
     * Test Kernel Exception Event
     */
    public function testOnKernelExceptionUnsupportedEncoder()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $normalizer = $this->createMock(NormalizerInterface::class);
        $normalizer->expects($this->never())
            ->method('supportsNormalization');

        $encoder = $this->createMock(EncoderInterface::class);
        $encoder->expects($this->once())
            ->method('supportsEncoding')
            ->willReturn(false);

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

        $this->assertNull($response);
    }

    /**
     * Test Kernel Exception Event
     */
    public function testOnKernelExceptionUnsupportedNormalization()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $normalizer = $this->createMock(NormalizerInterface::class);
        $normalizer->expects($this->once())
            ->method('supportsNormalization')
            ->willReturn(false);

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

        $this->assertNull($response);
    }
}
