<?php

class TwigHelperTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $CI =& get_instance();
        $CI->load->library('twig');
        $CI->load->helper('url_helper');
    }

    public function setUp()
    {
        $CI =& get_instance();

        $loader = new Twig_Loader_Array([
            'base_url' => '{{ base_url(\'"><s>abc</s><a name="test\') }}',
            'site_url' => '{{ site_url(\'"><s>abc</s><a name="test\') }}',
            'anchor'   => '{{ anchor(uri, title, attributes) }}',
        ]);
        $setLoader = ReflectionHelper::getPrivateMethodInvoker(
            $CI->twig, 'setLoader'
        );
        $setLoader($loader);

        $resetTwig = ReflectionHelper::getPrivateMethodInvoker(
            $CI->twig, 'resetTwig'
        );
        $resetTwig();

        $addFunctions = ReflectionHelper::getPrivateMethodInvoker(
            $CI->twig, 'addFunctions'
        );
        $addFunctions();

        $this->obj = $CI->twig;
        $this->twig = $CI->twig->getTwig();
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
        $expected = '<a href="http://localhost/index.php/news/local/123" title="The best news!">My News</a>';
        $this->assertEquals($expected, $actual);

        $actual = $this->twig->render(
            'anchor',
            [
                'uri' => 'news/local/123',
                'title' => '<s>abc</s>',
                'attributes' => ['<s>name</s>' => '<s>val</s>']
            ]
        );
        $expected = '<a href="http://localhost/index.php/news/local/123" &lt;s&gt;name&lt;/s&gt;="&lt;s&gt;val&lt;/s&gt;">&lt;s&gt;abc&lt;/s&gt;</a>';
        $this->assertEquals($expected, $actual);
    }

    public function test_base_url()
    {
        $actual = $this->twig->render('base_url');
        $expected = 'http://localhost/&quot;&gt;&lt;s&gt;abc&lt;/s&gt;&lt;a name=&quot;test';
        $this->assertEquals($expected, $actual);
    }

    public function test_site_url()
    {
        $actual = $this->twig->render('site_url');
        $expected = 'http://localhost/index.php/&quot;&gt;&lt;s&gt;abc&lt;/s&gt;&lt;a name=&quot;test';
        $this->assertEquals($expected, $actual);
    }
}
