<?php

namespace Kenjis\CI4Twig;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 */
final class TwigHelperTest extends TestCase
{
    private Environment $twig;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        helper('url');
    }

    protected function setUp(): void
    {
        $twig = new Twig();

        $loader = new ArrayLoader(
            [
                'base_url' => '{{ base_url(\'"><s>abc</s><a name="test\') }}',
                'site_url' => '{{ site_url(\'"><s>abc</s><a name="test\') }}',
                'anchor'   => '{{ anchor(uri, title, attributes) }}',
            ]
        );
        $setLoader = ReflectionHelper::getPrivateMethodInvoker(
            $twig,
            'setLoader'
        );
        $setLoader($loader);

        $resetTwig = ReflectionHelper::getPrivateMethodInvoker(
            $twig,
            'resetTwig'
        );
        $resetTwig();

        $addFunctions = ReflectionHelper::getPrivateMethodInvoker(
            $twig,
            'addFunctions'
        );
        $addFunctions();

        $this->twig = $twig->getTwig();
    }

    public function testAnchor()
    {
        $actual = $this->twig->render(
            'anchor',
            [
                'uri'        => 'news/local/123',
                'title'      => 'My News',
                'attributes' => ['title' => 'The best news!'],
            ]
        );
        // $expected = '<a href="http://localhost/index.php/news/local/123" title="The best news!">My News</a>'; // CI3
        $expected = '<a href="http://localhost/index.php/news/local/123" title="The best news!">My News</a>';
        $this->assertSame($expected, $actual);

        $actual = $this->twig->render(
            'anchor',
            [
                'uri'        => 'news/local/123',
                'title'      => '<s>abc</s>',
                'attributes' => ['<s>name</s>' => '<s>val</s>'],
            ]
        );
        $expected = '<a href="http://localhost/index.php/news/local/123" &lt;s&gt;name&lt;/s&gt;="&lt;s&gt;val&lt;/s&gt;">&lt;s&gt;abc&lt;/s&gt;</a>';
        $this->assertSame($expected, $actual);
    }

    public function testBaseUrl()
    {
        $actual = $this->twig->render('base_url');
        // expected = 'http://localhost/&quot;&gt;&lt;s&gt;abc&lt;/s&gt;&lt;a name=&quot;test'; // CI3
        $expected = 'http://localhost/%22%3E%3Cs%3Eabc%3C/s%3E%3Ca%20name=%22test';
        $this->assertSame($expected, $actual);
    }

    public function testSiteUrl()
    {
        $actual = $this->twig->render('site_url');
        // $expected = 'http://localhost/index.php/&quot;&gt;&lt;s&gt;abc&lt;/s&gt;&lt;a name=&quot;test'; // CI3
        $expected = 'http://localhost/index.php/%22%3E%3Cs%3Eabc%3C/s%3E%3Ca%20name=%22test';
        $this->assertSame($expected, $actual);
    }
}
