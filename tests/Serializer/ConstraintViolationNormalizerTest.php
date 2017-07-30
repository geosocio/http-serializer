<?php

namespace GeoSocio\HttpSerializer\Serializer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Constraint Violation Normalizer Test.
 */
class ConstraintViolationNormalizerTest extends TestCase
{
    /**
     * Test Supports Normalization.
     */
    public function testNormalize()
    {
        $message = 'Test Message';
        $propertyPath = 'id';
        $code = 123;

        $normalizer = new ConstraintViolationNormalizer();
        $violation = $this->createMock(ConstraintViolationInterface::class);
        $violation->expects($this->once())
            ->method('getMessage')
            ->willReturn($message);
        $violation->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);
        $violation->expects($this->once())
            ->method('getCode')
            ->willReturn($code);

        $result = $normalizer->normalize($violation);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals($message, $result['message']);
        $this->assertArrayHasKey('propertyPath', $result);
        $this->assertEquals($propertyPath, $result['propertyPath']);
        $this->assertArrayHasKey('code', $result);
        $this->assertEquals($code, $result['code']);
    }

    /**
     * Test Supports Normalization.
     */
    public function testSupportsNormalization()
    {
        $normalizer = new ConstraintViolationNormalizer();

        $violation = $this->createMock(ConstraintViolationInterface::class);

        $this->assertTrue($normalizer->supportsNormalization($violation));
    }
}
