<?php

namespace GeoSocio\HttpSerializer\EventListener;

use GeoSocio\HttpSerializer\Event\SerializeEvent;
use GeoSocio\HttpSerializer\Loader\GroupLoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Listen for Exceptions.
 */
class KernelExceptionListener
{

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var EncoderInterface
     */
    protected $encoder;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Creates the Event Listener.
     */
    public function __construct(
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        EncoderInterface $encoder,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->serializer = $serializer;
        $this->normalizer = $normalizer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Handle the Kernel Exception.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event) : Response
    {
        // You get the exception object from the received event
        $exception = $event->getException();
        $request = $event->getRequest();

        // If the event already has a response, do not override it.
        if (!$event->hasResponse()) {
            return $event->getResponse();
        }

        // Override the default request format.
        // @TODO Make this configurable!
        if ($request->getRequestFormat() === 'html') {
            $request->setRequestFormat('json');
        }

        // If the request format is not supported, nothing more can be done
        // without the serializer throwing an exception.
        if (!$this->encoder->supportsEncoding($request->getRequestFormat())) {
            return null;
        }

        // If the normalizer cannot normalize the result, then there is nothing
        // more than can be done without the serializer throwing an exception.
        if (!$this->normalizer->supportsNormalization($exception, $request->getRequestFormat())) {
            return null;
        }

        $status = 500;
        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();
        }

        $serializeEvent = new SerializeEvent(
            $exception,
            $request->getRequestFormat(),
            [
                'enable_max_depth' => true,
            ],
            $request
        );

        $this->eventDispatcher->dispatch(SerializeEvent::NAME, $serializeEvent);

        $response = new Response(
            $this->serializer->serialize(
                $serializeEvent->getData(),
                $serializeEvent->getFormat(),
                $serializeEvent->getContext()
            ),
            $status
        );
        $event->setResponse($response);

        return $response;
    }
}
