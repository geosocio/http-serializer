<?php

namespace GeoSocio\Tests\HttpSerializer\Event;

use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

abstract class AbstractSerializerEventTest extends TestCase
{
    /**
     * TestData.
     */
    public function testData()
    {
        $data = new \stdClass();
        $format= 'test';
        $context = [];

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getSubject($data, $format, $context, $request);

        $this->assertSame($data, $event->getData());
        $data = new \stdClass();
        $event->setData($data);
        $this->assertSame($data, $event->getData());
    }

    /**
     * Test Format.
     */
    public function testFormat()
    {
        $data = new \stdClass();
        $format= 'test';
        $context = [];

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getSubject($data, $format, $context, $request);

        $this->assertSame($format, $event->getFormat());
        $format = 'test2';
        $event->setFormat($format);
        $this->assertSame($format, $event->getFormat());
    }

    /**
     * Test Context.
     */
    public function testContext()
    {
        $data = new \stdClass();
        $format= 'test';
        $context = [
            'test',
        ];

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getSubject($data, $format, $context, $request);

        $this->assertSame($context, $event->getContext());
        $context = [
            'text2',
        ];
        $event->setContext($context);
        $this->assertSame($context, $event->getContext());
    }

    /**
     * Test Context.
     */
    public function testRequest()
    {
        $data = new \stdClass();
        $format= 'test';
        $context = [
            'test',
        ];

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getSubject($data, $format, $context, $request);

        $this->assertSame($request, $event->getRequest());

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event->setRequest($request);
        $this->assertSame($request, $event->getRequest());
    }

    /**
     * Get Subject.
     *
     * @param mixed $data
     * @param string $format
     * @param array $context
     * @param Request $request
     */
    abstract public function getSubject($data, $format, $context, $request);
}
