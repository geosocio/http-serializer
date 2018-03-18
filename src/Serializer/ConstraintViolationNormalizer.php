<?php

namespace GeoSocio\HttpSerializer\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

/**
 * Exception Normalizer
 */
class ConstraintViolationNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {

        // Convert the peroperty paths so they match the serialization.
        $propertyPath = $object->getPropertyPath();
        if ($this->normalizer && $this->normalizer->nameConverter instanceof NameConverterInterface) {
            $properties = explode('.', $propertyPath);
            $properties = array_map(function ($property) {
                return $this->normalizer->nameConverter->normalize($property);
            }, $properties);
            $propertyPath = implode('.', $properties);
        }

        $data = [
            'message' => $object->getMessage(),
            'propertyPath' => $propertyPath,
            'code' => $object->getCode(),
        ];

        if ($this->normalizer && $this->normalizer->nameConverter instanceof NameConverterInterface) {
            foreach ($data as $key => $value) {
                $normalized[$this->normalizer->nameConverter->normalize($key)] = $value;
            }
        } else {
            $normalized = $data;
        }

        return $normalized;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ConstraintViolationInterface;
    }
}
