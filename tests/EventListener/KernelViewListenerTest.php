<?php

namespace GeoSocio\Tests\HttpSerializer\EventListener;

use GeoSocio\Core\EventListener\ReturnListener;
use GeoSocio\HttpSerializer\EventListener\KernelViewListener;
use GeoSocio\HttpSerializer\Loader\GroupLoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use PHPUnit\Framework\TestCase;

class KernelViewListenerTest extends TestCase
{

    public function testOnKernelView()
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

        $loader = $this->createMock(GroupLoaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $listener = new KernelViewListener($serializer, $normalizer, $encoder, $loader, $eventDispatcher);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->exactly(3))
            ->method('getRequestFormat')
            ->willReturn('test');

        $result = new \stdClass();

        $event = $this->createMock(GetResponseForControllerResultEvent::class);
        $event->expects($this->once())
            ->method('hasResponse')
            ->willReturn(false);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);
        $event->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($result);

        $response = $listener->onKernelView($event);

        $this->assertInstanceOf(Response::class, $response);
    }
}
