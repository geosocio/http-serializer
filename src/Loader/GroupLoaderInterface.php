<?php

namespace GeoSocio\HttpSerializer\Loader;

use Symfony\Component\HttpFoundation\Request;

interface GroupLoaderInterface
{
    public function getRequestGroups(Request $request) :? array;

    public function getResponseGroups(Request $request) :? array;
}
