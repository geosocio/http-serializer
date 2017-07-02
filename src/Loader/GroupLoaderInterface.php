<?php

namespace GeoSocio\HttpSerializer\Loader;

use Symfony\Component\HttpFoundation\Request;

interface GroupLoaderInterface
{
    public function getRequestGroups(Request $request);

    public function getResponseGroups(Request $request);
}
