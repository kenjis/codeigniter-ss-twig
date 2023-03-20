<?php

namespace Kenjis\CI4Twig;

use ReflectionObject;

require __DIR__ . '/../twig_functions.php';

/**
 * @internal
 */
final class TwigTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        helper('url');
        helper('form');
    }

    public function testRender()
    {
        $obj = new Twig(['paths' => __DIR__ . '/../templates/']);

        $data = [
            'name' => 'CodeIgniter',
        ];
        $output = $obj->render('welcome', $data);
        $this->assertSame("Hello CodeIgniter!\n", $output);
    }

    public function testDisplay()
    {
        $obj = new Twig(['paths' => __DIR__ . '/../templates/']);

        $this->expectOutputString("Hello CodeIgniter!\n");

        $data = [
            'name' => 'CodeIgniter',
        ];
        $obj->display('welcome', $data);
    }

    public function testAddGlobal()
    {
        $obj = new Twig(['paths' => __DIR__ . '/../templates/']);
        $obj->addGlobal('sitename', 'Twig Test Site');

        $output = $obj->render('global');
        $this->assertSame("<title>Twig Test Site</title>\n", $output);
    }

    public function testAddFunctionsRunsOnlyOnce()
    {
        $obj = new Twig(['paths' => __DIR__ . '/../templates/']);

        $data = [
            'name' => 'CodeIgniter',
        ];

        $ref_obj      = new ReflectionObject($obj);
        $ref_property = $ref_obj->getProperty('functions_added');
        $ref_property->setAccessible(true);
        $functions_added = $ref_property->getValue($obj);
        $this->assertFalse($functions_added);

        $output = $obj->render('welcome', $data);

        $ref_obj      = new ReflectionObject($obj);
        $ref_property = $ref_obj->getProperty('functions_added');
        $ref_property->setAccessible(true);
        $functions_added = $ref_property->getValue($obj);
        $this->assertTrue($functions_added);

        // Calls render() twice
        $output = $obj->render('welcome', $data);

        $ref_obj      = new ReflectionObject($obj);
        $ref_property = $ref_obj->getProperty('functions_added');
        $ref_property->setAccessible(true);
        $functions_added = $ref_property->getValue($obj);
        $this->assertTrue($functions_added);
    }

    public function testFunctionAsIs()
    {
        $obj = new Twig([
            'paths'     => __DIR__ . '/../templates/',
            'functions' => ['md5'],
            'cache'     => false,
        ]);

        $output = $obj->render('functions_asis');
        $this->assertSame("900150983cd24fb0d6963f7d28e17f72\n", $output);
    }

    public function testFunctionSafe()
    {
        $obj = new Twig([
            'paths'          => __DIR__ . '/../templates/',
            'functions_safe' => ['test_safe'],
            'cache'          => false,
        ]);

        $output = $obj->render('functions_safe');
        $this->assertSame("<s>test</s>\n", $output);
    }

    public function testFunctionCustomized()
    {
        $obj = new Twig([
            'paths'     => __DIR__ . '/../templates/',
            'functions' => ['validation_list_errors'],
            'cache'     => false,
        ]);

        $output = $obj->render('functions_customized_override');
        $this->assertSame("override\n", $output);
    }

    public function testFilter()
    {
        $obj = new Twig([
            'paths'   => __DIR__ . '/../templates/',
            'filters' => ['str_rot13'],
            'cache'   => false,
        ]);

        $output = $obj->render('filters');
        $this->assertSame("PbqrVtavgre Fvzcyr naq Frpher Gjvt\n", $output);
    }
}
