<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Allow;

class AllowTest extends \PHPUnit_Framework_TestCase
{
    public function testAllowFromStringCreatesValidAllowHeader()
    {
        $allowHeader = Allow::fromString('Allow: GET, POST, PUT');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $allowHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Allow', $allowHeader);
        $this->assertEquals(array('GET', 'POST', 'PUT'), $allowHeader->getAllowedMethods());
    }

    public function testAllowFromStringSupportsExtensionMethods()
    {
        $allowHeader = Allow::fromString('Allow: GET, POST, PROCREATE');
        $this->assertTrue($allowHeader->isAllowedMethod('PROCREATE'));
    }

    public function testAllowFromStringWithNonPostMethod()
    {
        $allowHeader = Allow::fromString('Allow: GET');
        $this->assertEquals('GET', $allowHeader->getFieldValue());
    }

    public function testAllowGetFieldNameReturnsHeaderName()
    {
        $allowHeader = new Allow();
        $this->assertEquals('Allow', $allowHeader->getFieldName());
    }

    public function testAllowListAllDefinedMethods()
    {
        $methods = array(
            'OPTIONS' => false,
            'GET'     => true,
            'HEAD'    => false,
            'POST'    => true,
            'PUT'     => false,
            'DELETE'  => false,
            'TRACE'   => false,
            'CONNECT' => false,
            'PATCH'   => false,
        );
        $allowHeader = new Allow();
        $this->assertEquals($methods, $allowHeader->getAllMethods());
    }

    public function testAllowGetDefaultAllowedMethods()
    {
        $allowHeader = new Allow();
        $this->assertEquals(array('GET', 'POST'), $allowHeader->getAllowedMethods());
    }

    public function testAllowGetFieldValueReturnsProperValue()
    {
        $allowHeader = new Allow();
        $allowHeader->allowMethods(array('GET', 'POST', 'TRACE'));
        $this->assertEquals('GET, POST, TRACE', $allowHeader->getFieldValue());
    }

    public function testAllowToStringReturnsHeaderFormattedString()
    {
        $allowHeader = new Allow();
        $allowHeader->allowMethods(array('GET', 'POST', 'TRACE'));
        $this->assertEquals('Allow: GET, POST, TRACE', $allowHeader->toString());
    }

    public function testAllowChecksAllowedMethod()
    {
        $allowHeader = new Allow();
        $allowHeader->allowMethods(array('GET', 'POST', 'TRACE'));
        $this->assertTrue($allowHeader->isAllowedMethod('TRACE'));
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException', 'Invalid header value detected');
        $header = Allow::fromString("Allow: GET\r\n\r\nevilContent");
    }

    public function injectionMethods()
    {
        return array(
            'string' => array("\rG\r\nE\nT"),
            'array' => array(array("\rG\r\nE\nT")),
        );
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @dataProvider injectionMethods
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaAllowMethods($methods)
    {
        $header = new Allow();
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException', 'valid method');
        $header->allowMethods($methods);
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @dataProvider injectionMethods
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaDisallowMethods($methods)
    {
        $header = new Allow();
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException', 'valid method');
        $header->disallowMethods($methods);
    }
}
