<?php

namespace GeoSocio\HttpSerializer\Serializer;

use GeoSocio\HttpSerializer\Exception\ConstraintViolationExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;

/**
 * Exception Normalizer
 */
class ExceptionNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * @var string
     */
    protected $environment;

    /**
     * Exception Normalizer
     *
     * @param string|null $environment
     */
    public function __construct(?string $environment = null)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data = [
            'message' => $object->getMessage(),
            'code' => $object->getCode(),
        ];

        if ($object instanceof ConstraintViolationExceptionInterface) {
            $data['constraintViolations'] = $this->normalizer->normalize($object->getConstraintViolations(), $format, $context);
        }

        if ($this->environment === 'dev') {
            $trace = array_map(function ($line) {
                return preg_replace('/^#(\\d+) (.*)$/u', '$2', $line);
            }, $object->getTraceAsString() ? explode("\n", $object->getTraceAsString()) : []);
            $data = array_merge($data, [
                'exception' => get_class($object),
                'previous' => $this->normalizer->normalize($object->getPrevious(), $format, $context),
                'file' => $object->getFile(),
                'line' => $object->getLine(),
                'trace' => $trace,
            ]);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \Throwable;
    }
}
