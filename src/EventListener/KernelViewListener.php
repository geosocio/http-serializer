<?php

namespace GeoSocio\HttpSerializer\EventListener;

use GeoSocio\HttpSerializer\Event\SerializeEvent;
use GeoSocio\HttpSerializer\Loader\GroupLoaderInterface;
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
     * @var GroupLoaderInterface
     */
    protected $loader;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Creates the Event Listener.
     *
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @param EncoderInterface $encoder
     * @param GroupLoaderInterface $loader
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        EncoderInterface $encoder,
        GroupLoaderInterface $loader,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->serializer = $serializer;
        $this->normalizer = $normalizer;
        $this->encoder = $encoder;
        $this->loader = $loader;
        $this->eventDispatcher = $eventDispatcher;
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
        if (!$this->normalizer->supportsNormalization($result, $request->getRequestFormat())) {
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

        $groups = $this->loader->getResponseGroups($request);

        $serializeEvent = new SerializeEvent(
            $result,
            $request->getRequestFormat(),
            [
                'groups' => $groups,
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
