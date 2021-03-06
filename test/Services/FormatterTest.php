<?php

namespace League\Url\Test\Services;

use League\Url\Services\Formatter;
use League\Url;
use PHPUnit_Framework_TestCase;

/**
 * @group url
 */
class FormatterTest extends PHPUnit_Framework_TestCase
{
    private $url;

    public function setUp()
    {
        $this->url = Url\Url::createFromUrl(
            'http://login:pass@gwóźdź.pl:443/test/query.php?kingkong=toto&foo=bar+baz#doc3'
        );
    }

    public function testFormatHostAscii()
    {
        $formatter = new Formatter();
        $this->assertSame(Formatter::HOST_AS_UNICODE, $formatter->getHostEncoding());
        $formatter->setHostEncoding(Formatter::HOST_AS_ASCII);
        $this->assertSame(Formatter::HOST_AS_ASCII, $formatter->getHostEncoding());
        $this->assertSame('xn--gwd-hna98db.pl', $formatter->format($this->url->host));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidHostEncoding()
    {
        (new Formatter())->setHostEncoding('toto');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidQueryEncoding()
    {
        (new Formatter())->setQueryEncoding('toto');
    }

    public function testFormatWithSimpleString()
    {
        $url       = 'http://login:pass@gwóźdź.pl:443/test/query.php?kingkong=toto&foo=bar+baz#doc3';
        $expected  = 'http://login:pass@xn--gwd-hna98db.pl:443/test/query.php?kingkong=toto&amp;foo=bar%20baz#doc3';
        $formatter = new Formatter();
        $formatter->setQuerySeparator('&amp;');
        $formatter->setHostEncoding(Formatter::HOST_AS_ASCII);
        $this->assertSame($expected, $formatter->format($url));
    }

    public function testFormatComponent()
    {
        $scheme = new Url\Scheme('git+ssh');
        $this->assertSame($scheme->__toString(), (new Formatter())->format($scheme));
    }

    public function testFormatHostUnicode()
    {
        $formatter = new Formatter();
        $formatter->setHostEncoding(Formatter::HOST_AS_UNICODE);
        $this->assertSame('gwóźdź.pl', $formatter->format($this->url->host));
    }

    public function testFormatQueryRFC1738()
    {
        $formatter = new Formatter();
        $this->assertSame(PHP_QUERY_RFC3986, $formatter->getQueryEncoding());
        $formatter->setQueryEncoding(PHP_QUERY_RFC1738);
        $this->assertSame(PHP_QUERY_RFC1738, $formatter->getQueryEncoding());
        $this->assertSame('kingkong=toto&foo=bar+baz', $formatter->format($this->url->query));
    }

    public function testFormatQueryRFC3986()
    {
        $formatter = new Formatter();
        $formatter->setQueryEncoding(PHP_QUERY_RFC3986);
        $this->assertSame('kingkong=toto&foo=bar%20baz', $formatter->format($this->url->query));
    }

    public function testFormatQueryWithSeparator()
    {
        $formatter = new Formatter();
        $this->assertSame('&', $formatter->getQuerySeparator());
        $formatter->setQuerySeparator('&amp;');
        $this->assertSame('&amp;', $formatter->getQuerySeparator());
        $this->assertSame('kingkong=toto&amp;foo=bar%20baz', $formatter->format($this->url->query));
    }

    public function testFormatURL()
    {
        $formatter = new Formatter();
        $formatter->setQuerySeparator('&amp;');
        $formatter->setHostEncoding(Formatter::HOST_AS_ASCII);
        $expected = 'http://login:pass@xn--gwd-hna98db.pl:443/test/query.php?kingkong=toto&amp;foo=bar%20baz#doc3';
        $this->assertSame($expected, $formatter->format($this->url));
    }

    public function testFormatWithoutAuthority()
    {
        $formatter = new Formatter();
        $formatter->setQuerySeparator('&amp;');
        $formatter->setHostEncoding(Formatter::HOST_AS_ASCII);
        $expected = '/test/query.php?kingkong=toto&amp;foo=bar%20baz#doc3';
        $url = $this->url->withScheme(null)->withHost(null)->withPort(null)->withUserInfo(null, null);
        $this->assertSame($expected, $formatter->format($url));
    }
}
