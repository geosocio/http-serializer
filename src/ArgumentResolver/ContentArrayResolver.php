<?php

namespace GeoSocio\HttpSerializer\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * Content Array Resolver
 */
class ContentArrayResolver implements ArgumentValueResolverInterface
{

    /**
     * @var DecoderInterface
     */
    protected $decoder;

    /**
     * Content Array Resolver
     *
     * @param DecoderInterface $decoder
     */
    public function __construct(DecoderInterface $decoder)
    {
        $this->decoder = $decoder;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument) : bool
    {
        if (!$request->getContent()) {
            return false;
        }

        if ($argument->getType() !== 'array') {
            return false;
        }

        if (!$this->decoder->supportsDecoding($request->getRequestFormat())) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->decoder->decode($request->getContent(), $request->getRequestFormat());
    }
}
