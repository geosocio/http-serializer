<?php

namespace GeoSocio\HttpSerializer\Loader;

use Doctrine\Common\Annotations\Reader;
use GeoSocio\HttpSerializer\Annotation\RequestGroups;
use GeoSocio\HttpSerializer\Annotation\ResponseGroups;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Serializer\Annotation\Groups;

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
     */
    public function __construct(
        ControllerResolverInterface $resolver,
        Reader $reader
    ) {
        $this->resolver = $resolver;
        $this->reader = $reader;
    }

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

    protected function getGroups(array $annotations) :? array
    {
        if (empty($annotations)) {
            return null;
        }

        return array_reduce($annotations, function ($carry, $annotation) {
            return array_merge($carry, $annotation->getGroups());
        }, []);
    }

    protected function getAnnotations(Request $request) : array
    {
        [$class, $name] = $this->resolver->getController($event->getRequest());
        return $this->reader->getMethodAnnotations(new \ReflectionMethod($class, $name));
    }
}
