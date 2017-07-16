<?php

namespace GeoSocio\Tests\Event;

use GeoSocio\HttpSerializer\Event\SerializeEvent;
use GeoSocio\Tests\HttpSerializer\Event\AbstractSerializerEventTest;

class SerializeEventTest extends AbstractSerializerEventTest
{
    /**
     * {@inheritdoc}
     */
    public function getSubject($data, $format, $context, $request)
    {
        return new SerializeEvent($data, $format, $context, $request);
    }
}
