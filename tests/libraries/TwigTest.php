<?php

class TwigTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $CI =& get_instance();
        $CI->load->library('Twig');
        $CI->load->helper('url_helper');
        $CI->load->helper('form_helper');
    }

    public function testRedner()
    {
        $obj = new Twig(['paths' => __DIR__ . '/../templates/']);
        
        $data = [
            'name' => 'CodeIgniter',
        ];
        $output = $obj->render('welcome', $data);
        $this->assertEquals('Hello CodeIgniter!'."\n", $output);
    }

    public function testDisplay()
    {
        $obj = new Twig(['paths' => __DIR__ . '/../templates/']);
        
        $data = [
            'name' => 'CodeIgniter',
        ];
        $obj->display('welcome', $data);
        $CI =& get_instance();
        $output = $CI->output->get_output();
        $this->assertEquals('Hello CodeIgniter!'."\n", $output);
    }

    public function testAddGlobal()
    {
        $obj = new Twig(['paths' => __DIR__ . '/../templates/']);
        $obj->addGlobal('sitename', 'Twig Test Site');
        
        $output = $obj->render('global');
        $this->assertEquals('<title>Twig Test Site</title>'."\n", $output);
    }

    public function testAddCIFunctionsRunsOnlyOnce()
    {
        $obj = new Twig(['paths' => __DIR__ . '/../templates/']);
        
        $data = [
            'name' => 'CodeIgniter',
        ];

        $ref_obj = new ReflectionObject($obj);
        $ref_property = $ref_obj->getProperty('add_ci_functions');
        $ref_property->setAccessible(true);
        $add_ci_functions = $ref_property->getValue($obj);
        $this->assertEquals(false, $add_ci_functions);

        $output = $obj->render('welcome', $data);

        $ref_obj = new ReflectionObject($obj);
        $ref_property = $ref_obj->getProperty('add_ci_functions');
        $ref_property->setAccessible(true);
        $add_ci_functions = $ref_property->getValue($obj);
        $this->assertEquals(true, $add_ci_functions);

        // Calls render() twice
        $output = $obj->render('welcome', $data);

        $ref_obj = new ReflectionObject($obj);
        $ref_property = $ref_obj->getProperty('add_ci_functions');
        $ref_property->setAccessible(true);
        $add_ci_functions = $ref_property->getValue($obj);
        $this->assertEquals(true, $add_ci_functions);
    }
}
