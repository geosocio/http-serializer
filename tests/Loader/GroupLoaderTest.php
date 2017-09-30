<?php

namespace GeoSocio\HttpSerializer\Loader;

use GeoSocio\HttpSerializer\Annotation\RequestGroups;
use GeoSocio\HttpSerializer\Annotation\ResponseGroups;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

abstract class GroupLoaderTest extends TestCase
{

    public function getAnnotations()
    {
        return [
            new Groups([
                'value' => [
                    'test',
                ]
            ]),
            new RequestGroups([
                'value' => [
                    'test'
                ]
            ]),
            new RequestGroups([
                'value' => [
                    'test2'
                ]
            ]),
            new ResponseGroups([
                'value' => [
                    'test3'
                ]
            ]),
            new MaxDepth([
                'value' => 1,
            ]),
        ];
    }
}
