<?php

namespace GeoSocio\HttpSerializer\EventListener;

use GeoSocio\HttpSerializer\Event\SerializeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
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
     * @var string
     */
    protected $defaultFormat;

    /**
     * Creates the Event Listener.
     *
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @param EncoderInterface $encoder
     * @param EventDispatcherInterface $eventDispatcher
     * @param string|null $defaultFormat
     */
    public function __construct(
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        EncoderInterface $encoder,
        EventDispatcherInterface $eventDispatcher,
        ?string $defaultFormat = null
    ) {
        $this->serializer = $serializer;
        $this->normalizer = $normalizer;
        $this->encoder = $encoder;
        $this->eventDispatcher = $eventDispatcher;
        $this->defaultFormat = $defaultFormat;
    }

    /**
     * Handle the Kernel Exception.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event) :? Response
    {
        // You get the exception object from the received event
        $exception = $event->getException();
        $request = $event->getRequest();

        // If the event already has a response, do not override it.
        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        $format = $request->getRequestFormat($this->defaultFormat);

        // If the request format is not supported, nothing more can be done
        // without the serializer throwing an exception.
        if (!$this->encoder->supportsEncoding($format)) {
            return null;
        }

        // If the normalizer cannot normalize the result, then there is nothing
        // more than can be done without the serializer throwing an exception.
        if (!$this->normalizer->supportsNormalization($exception, $format)) {
            return null;
        }

        $status = 500;
        $headers = [];
        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();
            $headers = $exception->getHeaders();
        }

        $context = [
            'enable_max_depth' => true,
        ];

        $serializeEvent = new SerializeEvent($exception, $format, $context, $request);

        $this->eventDispatcher->dispatch(SerializeEvent::NAME, $serializeEvent);

        $response = new Response(
            $this->serializer->serialize(
                $serializeEvent->getData(),
                $serializeEvent->getFormat(),
                $serializeEvent->getContext()
            ),
            $status,
            $headers
        );

        // Add the default content type headers.
        if (!$response->headers->has('Content-Type')) {
            if ($mimeType = $request->getMimeType($format)) {
                $response->headers->set('Content-Type', $mimeType);
            }
        }

        $event->setResponse($response);

        return $response;
    }
}
