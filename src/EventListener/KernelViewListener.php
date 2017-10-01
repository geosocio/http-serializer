<?php

namespace GeoSocio\HttpSerializer\EventListener;

use GeoSocio\HttpSerializer\Event\SerializeEvent;
use GeoSocio\HttpSerializer\GroupResolver\ResponseGroupResolverInterface;
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
     * @var ResponseGroupResolverInterface
     */
    protected $groupResolver;

    /**
     * Creates the Event Listener.
     *
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @param EncoderInterface $encoder
     * @param EventDispatcherInterface $eventDispatcher
     * @param ResponseGroupResolverInterface $groupResolver
     */
    public function __construct(
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        EncoderInterface $encoder,
        EventDispatcherInterface $eventDispatcher,
        ResponseGroupResolverInterface $groupResolver
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

        if (is_iterable($result)) {
            $data = $result;

            // Convert objects to arrays.
            if ($result instanceof \Traversable) {
                $data = iterator_to_array($result);
            }

            // Determine if array is a collection or just a standard array.
            $bad = array_filter($data, function ($item) use ($request) {
                if (!is_object($item)) {
                    return true;
                }

                if (!$this->normalizer->supportsNormalization($item, $request->getRequestFormat())) {
                    return true;
                }

                return false;
            });

            // If array is a collection and everything can be normalized,
            // normalize each item before serialization.
            if (!count($bad)) {
                $result = array_map(function ($item) use ($request) {
                    return $this->normalizer->normalize($item, $request->getRequestFormat(), [
                        'groups' => $this->groupResolver->resolve($request, $item),
                        'enable_max_depth' => true,
                    ]);
                }, $data);
            }
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

        $groups = null;
        if (is_object($result)) {
            $groups = $this->groupResolver->resolve($request, $result);
        }

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
