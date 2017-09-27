<?php

namespace GeoSocio\HttpSerializer\Loader;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * Group Loader
 */
abstract class GroupLoader
{
    /**
     * @var ControllerResolverInterface
     */
    protected $resolver;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Creates the Event Listener.
     *
     * @param ControllerResolverInterface $resolver
     * @param Reader $reader
     */
    public function __construct(
        ControllerResolverInterface $resolver,
        Reader $reader
    ) {
        $this->resolver = $resolver;
        $this->reader = $reader;
    }

    /**
     * Gets the groups from annotations.
     *
     * @param array $annotations
     *
     * @return array|null
     */
    protected function getGroups(array $annotations) :? array
    {
        if (empty($annotations)) {
            return [];
        }

        return array_values(array_unique(array_reduce($annotations, function ($carry, $annotation) {
            return array_merge($carry, $annotation->getGroups());
        }, [])));
    }

    /**
     * Gets the group annotations from the request
     *
     * @param Request $request
     *
     * @return array|null
     */
    protected function getAnnotations(Request $request) : array
    {
        $controller = $this->resolver->getController($request);

        if (!$controller) {
            return [];
        }

        [$class, $name] = $controller;
        return $this->reader->getMethodAnnotations(new \ReflectionMethod($class, $name));
    }
}
