<?php

namespace GeoSocio\HttpSerializer\ArgumentResolver;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class ContentArrayResolverTest extends TestCase
{

    public function testSupports()
    {
        $data = new \stdClass();
        $content = json_encode($data);

        $decoder = $this->createMock(DecoderInterface::class);
        $decoder->expects($this->once())
            ->method('supportsDecoding')
            ->with('test')
            ->willReturn(true);

        $resolver = new ContentArrayResolver($decoder);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn($content);
        $request->expects($this->once())
            ->method('getRequestFormat')
            ->willReturn('test');

        $metadata = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->once())
            ->method('getType')
            ->willReturn('array');

        $result = $resolver->supports($request, $metadata);

        $this->assertTrue($result);
    }
}
