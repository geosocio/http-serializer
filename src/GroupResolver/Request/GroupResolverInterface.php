<?php

namespace GeoSocio\HttpSerializer\GroupResolver\Request;

use Symfony\Component\HttpFoundation\Request;

/**
 * Verification Interface.
 */
interface GroupResolverInterface
{
    /**
     * Generate the groups from the object.
     *
     * @param Request $request
     * @param string $type
     *
     * @return array An array of groups.
     */
    public function resolve(Request $request, string $type) : array;
}
