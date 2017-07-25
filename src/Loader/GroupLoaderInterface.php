<?php

namespace GeoSocio\HttpSerializer\Loader;

use Symfony\Component\HttpFoundation\Request;

/**
 * Group Loader
 */
interface GroupLoaderInterface
{
    /**
     * Gets the Request Groups
     *
     * @param Request $request
     *
     * @return array|null
     */
    public function getRequestGroups(Request $request) :? array;

    /**
     * Gets the Response Groups
     *
     * @param Request $request
     *
     * @return array|null
     */
    public function getResponseGroups(Request $request) :? array;
}
