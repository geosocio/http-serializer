<?php

namespace GeoSocio\HttpSerializer\Loader;

use GeoSocio\HttpSerializer\Annotation\ResponseGroups;
use GeoSocio\HttpSerializer\GroupResolver\Request\GroupResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Group Loader
 */
class RequestGroupLoader extends GroupLoader implements GroupResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, string $type) : array
    {
        $annotations = array_filter($this->getAnnotations($request), function ($annotation) {
            // Remove the Response Groups from the list.
            if ($annotation instanceof ResponseGroups) {
                return false;
            }

            return $annotation instanceof Groups;
        });

        return $this->getGroups($annotations);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, string $type) : bool
    {
        return true;
    }
}
