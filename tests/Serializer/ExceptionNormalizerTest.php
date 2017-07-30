<?php

namespace GeoSocio\HttpSerializer\Serializer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Exception Normalizer Test.
 */
class ExceptionNormalizerTest extends TestCase
{
    /**
     * Test Supports Normalization.
     */
    public function testNormalize()
    {
        $normalizer = new ExceptionNormalizer('dev');

        $main = $this->createMock(NormalizerInterface::class);
        $normalizer->setNormalizer($main);

        $message = 'Test Message';
        $code = 123;
        $exception = new \Exception('Test Message', $code);

        $result = $normalizer->normalize($exception);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals($message, $result['message']);
        $this->assertArrayHasKey('code', $result);
        $this->assertEquals($code, $result['code']);
        $this->assertArrayHasKey('exception', $result);
        $this->assertEquals('Exception', $result['exception']);
        $this->assertArrayHasKey('previous', $result);
        $this->assertNull($result['previous']);
        $this->assertArrayHasKey('trace', $result);
    }

    /**
     * Test Supports Normalization.
     */
    public function testSupportsNormalization()
    {
        $normalizer = new ExceptionNormalizer();

        $exception = new \Exception();

        $this->assertTrue($normalizer->supportsNormalization($exception));
    }
}
