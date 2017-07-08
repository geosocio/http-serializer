<?php

namespace GeoSocio\HttpSerializer\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractSerializerEvent extends Event
{
    protected $data;
    protected $format;
    protected $context;
    protected $request;

    public function __construct(
        $data,
        string $format,
        array $context = null,
        Request $request = null
    ) {
        $this->data = $data;
        $this->format = $format;
        $this->context = $context;
        $this->request = $request;
    }

    public function setData($data) : self
    {
        $this->data = $data;

        return $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setFormat(string $format) : self
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat() :? string
    {
        return $this->format;
    }

    public function setContext(array $context) : self
    {
        $this->context = $context;

        return $this;
    }

    public function getContext() :? array
    {
        return $this->context;
    }

    public function setRequest(Request $request) : self
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest() :? Request
    {
        return $this->request;
    }
}
