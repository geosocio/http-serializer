<?php

namespace GeoSocio\HttpSerializer\Loader;

use GeoSocio\HttpSerializer\Annotation\RequestGroups;
use GeoSocio\HttpSerializer\GroupResolver\ResponseGroupResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Group Loader
 */
class ResponseGroupLoader extends GroupLoader implements ResponseGroupResolverInterface
{

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, $object) : array
    {
        $annotations = array_filter($this->getAnnotations($request), function ($annotation) {
            // Remove the Request Groups from the list.
            if ($annotation instanceof RequestGroups) {
                return false;
            }

            return $annotation instanceof Groups;
        });

        return $this->getGroups($annotations);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, $object) : bool
    {
        return true;
    }
}
