<?php

namespace GeoSocio\Event;

use GeoSocio\HttpSerializer\Event\DeserializeEvent;
use GeoSocio\HttpSerializer\Event\AbstractSerializerEventTest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Deserialize Event Test.
 */
class DeserializeEventTest extends AbstractSerializerEventTest
{
    /**
     * {@inheritdoc}
     */
    public function getSubject($data, $format, $context, $request)
    {
        return new DeserializeEvent($data, \stdClass::class, $format, $context, $request);
    }

    /**
     * Test Type.
     */
    public function testType()
    {
        $data = new \stdClass();
        $type = \stdClass::class;
        $format= 'test';
        $context = [
            'test',
        ];

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event = new DeserializeEvent($data, $type, $format, $context, $request);

        $this->assertSame($type, $event->getType());
        $type = \DateTime::class;
        $event->setType($type);
        $this->assertSame($type, $event->getType());
    }
}
