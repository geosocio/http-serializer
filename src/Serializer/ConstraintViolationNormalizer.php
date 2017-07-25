<?php

namespace GeoSocio\HttpSerializer\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Exception Normalizer
 */
class ConstraintViolationNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'message' => $object->getMessage(),
            'propertyPath' => $object->getPropertyPath(),
            'code' => $object->getCode(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ConstraintViolationInterface;
    }
}
