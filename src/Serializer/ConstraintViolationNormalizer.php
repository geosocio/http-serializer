<?php

namespace GeoSocio\HttpSerializer\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Exception Normalizer
 */
class ConstraintViolationNormalizer implements NormalizerInterface
{
    /**
     * @var NameConverterInterface|null
     */
    protected $nameConverter;

    /**
     * Constraint Violoation Normalizer
     *
     * @param NameConverterInterface|null $nameConverter
     */
    public function __construct(?NameConverterInterface $nameConverter = null)
    {
        $this->nameConverter = $nameConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {

        // Convert the peroperty paths so they match the serialization.
        $propertyPath = $object->getPropertyPath();
        if ($this->nameConverter) {
            $properties = explode('.', $propertyPath);
            $properties = array_map(function ($property) {
                return $this->nameConverter->normalize($property);
            }, $properties);
            $propertyPath = implode('.', $properties);
        }

        $data = [
            'message' => $object->getMessage(),
            'propertyPath' => $propertyPath,
            'code' => $object->getCode(),
        ];

        if ($this->nameConverter) {
            foreach ($data as $key => $value) {
                $normalized[$this->nameConverter->normalize($key)] = $value;
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
