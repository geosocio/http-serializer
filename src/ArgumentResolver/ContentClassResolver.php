<?php

namespace GeoSocio\HttpSerializer\ArgumentResolver;

use GeoSocio\HttpSerializer\Event\DeserializeEvent;
use GeoSocio\HttpSerializer\Loader\GroupLoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Content Class Resolver
 */
class ContentClassResolver implements ArgumentValueResolverInterface
{
    /**
     * @var array
     */
    const INTERNAL_TYPES = [
        'self',
        'array',
        'callable',
        'bool',
        'float',
        'int',
        'string',
        'iterable',
    ];

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var DenormalizerInterface
     */
    protected $denormalizer;

    /**
     * @var DecoderInterface
     */
    protected $decoder;

    /**
     * @var GroupLoaderInterface
     */
    protected $loader;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Content Class Resolver
     *
     * @param SerializerInterface $serializer
     * @param DenormalizerInterface $denormalizer
     * @param DecoderInterface $decoder
     * @param GroupLoaderInterface $loader
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        SerializerInterface $serializer,
        DenormalizerInterface $denormalizer,
        DecoderInterface $decoder,
        GroupLoaderInterface $loader,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->serializer = $serializer;
        $this->denormalizer = $denormalizer;
        $this->decoder = $decoder;
        $this->loader = $loader;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument) : bool
    {
        if (!$request->getContent()) {
            return false;
        }

        if (!$this->decoder->supportsDecoding($request->getRequestFormat())) {
            return false;
        }

        if (in_array($argument->getType(), self::INTERNAL_TYPES)) {
            return false;
        }

        if (!class_exists($argument->getType())) {
            return false;
        }

        if (!$this->denormalizer->supportsDenormalization($request->getContent(), $request->getRequestFormat())) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $event = new DeserializeEvent(
            $request->getContent(),
            $argument->getType(),
            $request->getRequestFormat(),
            [
                'groups' => $this->loader->getRequestGroups($request),
            ],
            $request
        );

        $this->eventDispatcher->dispatch(DeserializeEvent::NAME, $event);

        yield $this->serializer->deserialize(
            $event->getData(),
            $event->getType(),
            $event->getFormat(),
            $event->getContext()
        );
    }
}
