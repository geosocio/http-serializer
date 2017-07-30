<?php

namespace GeoSocio\Event;

use GeoSocio\HttpSerializer\Event\SerializeEvent;
use GeoSocio\HttpSerializer\Event\AbstractSerializerEventTest;

/**
 * Serialize Event Test.
 */
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
