<?php

namespace GeoSocio\HttpSerializer\ArgumentResolver;

use GeoSocio\HttpSerializer\Event\DeserializeEvent;
use GeoSocio\HttpSerializer\GroupResolver\Request\GroupResolverInterface;
use GeoSocio\HttpSerializer\Exception\ConstraintViolationException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var GroupResolverInterface
     */
    protected $groupResolver;

    /**
     * Content Class Resolver
     *
     * @param SerializerInterface $serializer
     * @param DenormalizerInterface $denormalizer
     * @param DecoderInterface $decoder
     * @param EventDispatcherInterface $eventDispatcher
     * @param ValidatorInterface $validator
     * @param GroupResolverInterface $groupResolver
     */
    public function __construct(
        SerializerInterface $serializer,
        DenormalizerInterface $denormalizer,
        DecoderInterface $decoder,
        EventDispatcherInterface $eventDispatcher,
        ValidatorInterface $validator,
        GroupResolverInterface $groupResolver
    ) {
        $this->serializer = $serializer;
        $this->denormalizer = $denormalizer;
        $this->decoder = $decoder;
        $this->eventDispatcher = $eventDispatcher;
        $this->validator = $validator;
        $this->groupResolver = $groupResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument) : bool
    {
        if (!$request->getContent()) {
            return false;
        }

        // Only support POST & PUT since PATCH does not have the entire object.
        if (!in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT])) {
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

        if (!$this->denormalizer->supportsDenormalization(
            $request->getContent(),
            $argument->getType(),
            $request->getRequestFormat()
        )) {
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
                'groups' => $this->groupResolver->resolve($request, $argument->getType()),
            ],
            $request
        );

        $this->eventDispatcher->dispatch(DeserializeEvent::NAME, $event);

        $value = $this->serializer->deserialize(
            $event->getData(),
            $event->getType(),
            $event->getFormat(),
            $event->getContext()
        );

        $errors = $this->validator->validate($value);

        if (count($errors)) {
            throw new ConstraintViolationException($errors);
        }

        yield $value;
    }
}
