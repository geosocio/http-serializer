<?php

namespace GeoSocio\Core\ArgumentResolver;

use GeoSocio\HttpSerializer\Loader\GroupLoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ContentResolver implements ArgumentValueResolverInterface
{
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

    public function __construct(
        SerializerInterface $serializer,
        DenormalizerInterface $denormalizer,
        DecoderInterface $decoder,
        GroupLoaderInterface $loader
    ) {
        $this->serializer = $serializer;
        $this->decoder = $decoder;
        $this->loader = $loader;
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
        yield $this->serializer->deserialize(
            $request->getContent(),
            $argument->getType(),
            $request->getRequestFormat(),
            [
                'groups' => $this->loader->getRequestGroups($request),
            ]
        );
    }
}
