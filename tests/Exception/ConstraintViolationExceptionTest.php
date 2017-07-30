<?php

namespace GeoSocio\HttpSerializer\Exception;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Contraint Violation Exception Test.
 */
class ConstraintViolationExceptionTest extends TestCase
{
    /**
     * Test Exception.
     */
    public function testException()
    {
        $list = $this->createMock(ConstraintViolationListInterface::class);
        $exception = new ConstraintViolationException($list);

        $this->assertSame($list, $exception->getConstraintViolations());
    }
}
