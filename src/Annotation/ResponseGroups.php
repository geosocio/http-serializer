<?php

namespace GeoSocio\HttpSerializer\Annotation;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Annotation class for @ResponseGroups().
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class ResponseGroups extends Groups
{

}
