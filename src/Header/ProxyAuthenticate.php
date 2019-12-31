<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.33
 */
class ProxyAuthenticate implements MultipleHeaderInterface
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = GenericHeader::splitHeaderLine($headerLine);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'proxy-authenticate') {
            throw new Exception\InvalidArgumentException('Invalid header line for Proxy-Authenticate string: "' . $name . '"');
        }

        // @todo implementation details
        $header->value = $value;

        return $header;
    }

    public function getFieldName()
    {
        return 'Proxy-Authenticate';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Proxy-Authenticate: ' . $this->getFieldValue();
    }

    public function toStringMultipleHeaders(array $headers)
    {
        $strings = array($this->toString());
        foreach ($headers as $header) {
            if (!$header instanceof ProxyAuthenticate) {
                throw new Exception\RuntimeException(
                    'The ProxyAuthenticate multiple header implementation can only accept an array of ProxyAuthenticate headers'
                );
            }
            $strings[] = $header->toString();
        }
        return implode("\r\n", $strings);
    }
}
