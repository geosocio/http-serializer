<?php

namespace GeoSocio\HttpSerializer\Event;

class SerializeEvent extends AbstractSerializerEvent
{
    /**
     * @var string
     */
    public const NAME = 'http_serializer.serialize';
}
