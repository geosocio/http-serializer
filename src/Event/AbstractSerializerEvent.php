<?php

namespace GeoSocio\HttpSerializer\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractSerializerEvent extends Event
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var Request
     */
    protected $request;

    /**
     * AbstractSerializerEvent
     *
     * @param mixed $data
     * @param string $format
     * @param array|null $context
     * @param Request|null $request
     */
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

    /**
     * Set Data.
     *
     * @param mixed $data
     */
    public function setData($data) : self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set Format.
     *
     * @param string $format
     */
    public function setFormat(string $format) : self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Format.
     */
    public function getFormat() :? string
    {
        return $this->format;
    }

    /**
     * Set Format.
     *
     * @param array $context
     */
    public function setContext(array $context) : self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Format
     */
    public function getContext() :? array
    {
        return $this->context;
    }

    /**
     * Set Request.
     *
     * @param Request $request
     */
    public function setRequest(Request $request) : self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Request
     */
    public function getRequest() :? Request
    {
        return $this->request;
    }
}
