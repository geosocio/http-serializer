<?php

namespace GeoSocio\HttpSerializer\Event;

use Symfony\Component\HttpFoundation\Request;

class DeserializeEvent extends AbstractSerializerEvent
{
    public const NAME = 'http_serializer.deserialize';

    protected $type;

    public function __construct(
        $data,
        string $type,
        string $format,
        array $context = null,
        Request $request = null
    ) {
        $this->type = $type;
        parent::__construct($data, $format, $context, $request);
    }

    public function setType(string $type) : self
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }
}
