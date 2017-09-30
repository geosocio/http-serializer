<?php

namespace GeoSocio\HttpSerializer\EventListener;

use GeoSocio\HttpSerializer\EventListener\KernelViewListener;
use GeoSocio\HttpSerializer\GroupResolver\ResponseGroupResolverInterface;
use GeoSocio\HttpSerializer\Loader\GroupLoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use PHPUnit\Framework\TestCase;

class KernelViewListenerTest extends TestCase
{
    /**
     * Test Kernel View Listener
     *
     * @dataProvider views
     *
     * @param string $method
     */
    public function testOnKernelView(string $method)
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

        $groupResolver = $this->createMock(ResponseGroupResolverInterface::class);

        $listener = new KernelViewListener(
            $serializer,
            $normalizer,
            $encoder,
            $eventDispatcher,
            $groupResolver
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->exactly(3))
            ->method('getRequestFormat')
            ->willReturn('test');
        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

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

    /**
     * Test Kernel View Listener
     */
    public function testOnKernelViewHasResponse()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $normalizer = $this->createMock(NormalizerInterface::class);
        $normalizer->expects($this->never())
            ->method('supportsNormalization');

        $encoder = $this->createMock(EncoderInterface::class);
        $encoder->expects($this->never())
            ->method('supportsEncoding');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $groupResolver = $this->createMock(ResponseGroupResolverInterface::class);

        $listener = new KernelViewListener(
            $serializer,
            $normalizer,
            $encoder,
            $eventDispatcher,
            $groupResolver
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->never())
            ->method('getRequestFormat');

        $result = new \stdClass();

        $event = $this->createMock(GetResponseForControllerResultEvent::class);
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
            ->method('getRequest')
            ->willReturn($request);
        $event->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($result);

        $this->assertSame($response, $listener->onKernelView($event));
    }

    /**
     * Test Kernel View Listener with unsupported encoding.
     */
    public function testOnKernelViewUnsupportedEncoding()
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
        $groupResolver = $this->createMock(ResponseGroupResolverInterface::class);

        $listener = new KernelViewListener(
            $serializer,
            $normalizer,
            $encoder,
            $eventDispatcher,
            $groupResolver
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
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

        $this->assertNull($response);
    }

    /**
     * Test Kernel View Listener with unsupported normalization.
     */
    public function testOnKernelViewUnsupportedNormalization()
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
        $groupResolver = $this->createMock(ResponseGroupResolverInterface::class);

        $listener = new KernelViewListener(
            $serializer,
            $normalizer,
            $encoder,
            $eventDispatcher,
            $groupResolver
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->exactly(2))
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

        $this->assertNull($response);
    }

    /**
     * Data provider for response tests.
     */
    public function views() : array
    {
         return [
             [
                 Response::HTTP_OK
             ],
             [
                 Request::METHOD_POST
             ],
             [
                 Request::METHOD_DELETE
             ],
         ];
    }
}
