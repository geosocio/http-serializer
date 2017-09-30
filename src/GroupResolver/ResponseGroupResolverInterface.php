<?php

namespace GeoSocio\HttpSerializer\GroupResolver;

use Symfony\Component\HttpFoundation\Request;

/**
 * Verification Interface.
 */
interface ResponseGroupResolverInterface
{
    /**
     * Generate the groups from the object.
     *
     * @param Request $request
     * @param object $object
     *
     * @return array An array of groups.
     */
    public function resolve(Request $request, $object) : array;

    /**
     * Deteremine if resolver supports the currect subject.
     *
     * @param object $object
     *
     * @return bool
     */
    public function supports(Request $request, $object) : bool;
}
