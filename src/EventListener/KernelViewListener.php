<?php

namespace GeoSocio\HttpSerializer\EventListener;

use GeoSocio\HttpSerializer\Loader\GroupLoaderInterface;
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
     * Creates the Event Listener.
     */
    public function __construct(
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        EncoderInterface $encoder,
        GroupLoaderInterface $loader
    ) {
        $this->serializer = $serializer;
        $this->normalizer = $normalizer;
        $this->loader = $loader;
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
        if (!$event->hasResponse()) {
            return $event->getResponse();
        }

        // If the request format is not supported, nothing more can be done
        // without the serializer throwing an exception.
        if (!$this->encoder->supportsEncoding($request->getRequestFormat())) {
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

        // Allow the groups to be modified on each item in a collection.
        if (is_iterable($result) && $this->isCollection($result)) {
            if (is_object($result) && method_exists($result, 'toArray')) {
                $result = $result->toArray();
            }

            $result = array_map(function ($item) {
                return $this->normalize($item, $groups);
            }, $result);
        } else {
            $result = $this->normalize($result, $groups);
        }

        $response = new Response(
            $this->serializer->serialize($result, $request->getRequestFormat()),
            $status
        );
        $event->setResponse($response);

        return $response;
    }

    /**
     * Determines if the iterable is a collection or an associative array.
     */
    protected function isCollection(iterable $data) : bool
    {
        $keys = array_keys($data);
        return $keys === array_keys($keys);
    }

    /**
     * Normalize an object.
     */
    protected function normalize($result, $groups = null)
    {
        $context = [
            'groups' => $groups,
            'enable_max_depth' => true,
        ];

        return $this->normalizer->normalize($result, null, $context);
    }
}
