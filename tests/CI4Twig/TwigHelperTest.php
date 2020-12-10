<?php

namespace Kenjis\CI4Twig;

class TwigHelperTest extends TestCase
{
    private $twig;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        helper('url');
    }

    public function setUp(): void
    {
        $twig = new Twig();

        $loader = new \Twig\Loader\ArrayLoader(
            [
                'base_url' => '{{ base_url(\'"><s>abc</s><a name="test\') }}',
                'site_url' => '{{ site_url(\'"><s>abc</s><a name="test\') }}',
                'anchor' => '{{ anchor(uri, title, attributes) }}',
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

    public function test_anchor()
    {
        $actual = $this->twig->render(
            'anchor',
            [
                'uri' => 'news/local/123',
                'title' => 'My News',
                'attributes' => ['title' => 'The best news!']
            ]
        );
//		$expected = '<a href="http://localhost/index.php/news/local/123" title="The best news!">My News</a>'; // CI3
        $expected = '<a href="http://localhost/index.php/news/local/123" title="The&#x20;best&#x20;news&#x21;">My News</a>';
        $this->assertEquals($expected, $actual);

        $actual = $this->twig->render(
            'anchor',
            [
                'uri' => 'news/local/123',
                'title' => '<s>abc</s>',
                'attributes' => ['<s>name</s>' => '<s>val</s>']
            ]
        );
//		$expected = '<a href="http://localhost/index.php/news/local/123" &lt;s&gt;name&lt;/s&gt;="&lt;s&gt;val&lt;/s&gt;">&lt;s&gt;abc&lt;/s&gt;</a>'; // CI3
        $expected = '<a href="http://localhost/index.php/news/local/123" &lt;s&gt;name&lt;/s&gt;="&lt;s&gt;val&lt;&#x2F;s&gt;">&lt;s&gt;abc&lt;/s&gt;</a>';
        $this->assertEquals($expected, $actual);
    }

    public function test_base_url()
    {
        $actual = $this->twig->render('base_url');
//		$expected = 'http://localhost/&quot;&gt;&lt;s&gt;abc&lt;/s&gt;&lt;a name=&quot;test'; // CI3
        $expected = 'http://localhost/%22%3E%3Cs%3Eabc%3C/s%3E%3Ca%20name=%22test';
        $this->assertEquals($expected, $actual);
    }

    public function test_site_url()
    {
        $actual = $this->twig->render('site_url');
//		$expected = 'http://localhost/index.php/&quot;&gt;&lt;s&gt;abc&lt;/s&gt;&lt;a name=&quot;test'; // CI3
        $expected = 'http://localhost/index.php/%22%3E%3Cs%3Eabc%3C/s%3E%3Ca%20name=%22test';
        $this->assertEquals($expected, $actual);
    }
}
