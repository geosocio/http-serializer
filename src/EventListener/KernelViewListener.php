<?php

namespace GeoSocio\HttpSerializer\EventListener;

use GeoSocio\HttpSerializer\Event\SerializeEvent;
use GeoSocio\HttpSerializer\GroupResolver\Response\GroupResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Listen for Exceptions.
 */
class KernelViewListener
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
     * @var GroupResolverInterface
     */
    protected $groupResolver;

    /**
     * Creates the Event Listener.
     *
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @param EncoderInterface $encoder
     * @param EventDispatcherInterface $eventDispatcher
     * @param GroupResolverInterface $groupResolver
     */
    public function __construct(
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        EncoderInterface $encoder,
        EventDispatcherInterface $eventDispatcher,
        GroupResolverInterface $groupResolver
    ) {
        $this->serializer = $serializer;
        $this->normalizer = $normalizer;
        $this->encoder = $encoder;
        $this->eventDispatcher = $eventDispatcher;
        $this->groupResolver = $groupResolver;
    }

    /**
     * Handle the Kernel Exception.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event) :? Response
    {
        $request = $event->getRequest();
        $result = $event->getControllerResult();

        // If the event already has a response, do not override it.
        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        // If the request format is not supported, nothing more can be done
        // without the serializer throwing an exception.
        if (!$this->encoder->supportsEncoding($request->getRequestFormat())) {
            return null;
        }

        // If the normalizer cannot normalize the result, then there is nothing
        // more than can be done without the serializer throwing an exception.
        if (is_object($result) && !$this->normalizer->supportsNormalization($result, $request->getRequestFormat())) {
            return null;
        }

        $status = Response::HTTP_OK;
        switch ($request->getMethod()) {
            case Request::METHOD_POST:
                $status = Response::HTTP_CREATED;
                break;
            case Request::METHOD_DELETE:
                $status = Response::HTTP_NO_CONTENT;
                break;
        }

        $serializeEvent = new SerializeEvent(
            $result,
            $request->getRequestFormat(),
            [
                'groups' => $this->groupResolver->resolve($request, $result),
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
