<?php

namespace GeoSocio\HttpSerializer\Event;

use Symfony\Component\HttpFoundation\Request;

class DeserializeEvent extends AbstractSerializerEvent
{
    /**
     * @var string
     */
    public const NAME = 'http_serializer.deserialize';

    /**
     * @var string
     */
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

    /**
     * Set Type
     *
     * @param string $type
     */
    public function setType(string $type) : self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Type
     */
    public function getType()
    {
        return $this->type;
    }
}
