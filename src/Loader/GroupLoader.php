<?php

namespace GeoSocio\HttpSerializer\Loader;

use Doctrine\Common\Annotations\Reader;
use GeoSocio\HttpSerializer\Annotation\RequestGroups;
use GeoSocio\HttpSerializer\Annotation\ResponseGroups;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Group Loader
 */
class GroupLoader
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
     * Gets the Request Groups
     *
     * @param Request $request
     *
     * @return array|null
     */
    public function getRequestGroups(Request $request) :? array
    {
        $annotations = array_filter($this->getAnnotations($request), function ($annotation) {
            if ($annotation instanceof ResponseGroups) {
                return false;
            }

            return $annotation instanceof Groups;
        });

        return $this->getGroups($annotations);
    }

    /**
     * Gets the Response Groups
     *
     * @param Request $request
     *
     * @return array|null
     */
    public function getResponseGroups(Request $request) :? array
    {
        $annotations = array_filter($this->getAnnotations($request), function ($annotation) {
            if ($annotation instanceof RequestGroups) {
                return false;
            }

            return $annotation instanceof Groups;
        });

        return $this->getGroups($annotations);
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
            return null;
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
