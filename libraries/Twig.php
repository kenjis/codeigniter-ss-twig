<?php
/**
 * Part of CodeIgniter Simple and Secure Twig
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/codeigniter-ss-twig
 */

// If you don't use Composer, uncomment below
/*
require_once APPPATH . 'third_party/Twig-1.xx.x/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
*/

class Twig
{
	private $config = [];

	private $functions_asis = [
		'base_url', 'site_url'
	];
	private $functions_safe = [
		'form_open', 'form_close', 'form_error', 'set_value', 'form_hidden'
	];

	/**
	 * @var bool Whether functions are added or not
	 */
	private $functions_added = FALSE;

	/**
	 * @var Twig_Environment
	 */
	private $twig;

	/**
	 * @var Twig_Loader_Filesystem
	 */
	private $loader;

	public function __construct($params = [])
	{
		// default config
		$this->config = [
			'paths' => [VIEWPATH],
			'cache' => APPPATH . 'cache/twig',
		];

		$this->config = array_merge($this->config, $params);

		if (isset($params['functions']))
		{
			$this->functions_asis = 
				array_unique(
					array_merge($this->functions_asis, $params['functions'])
				);
		}
		if (isset($params['functions_safe']))
		{
			$this->functions_safe = 
				array_unique(
					array_merge($this->functions_safe, $params['functions_safe'])
				);
		}
	}

	protected function resetTwig()
	{
		$this->twig = null;
		$this->createTwig();
	}

	protected function createTwig()
	{
		// $this->twig is singleton
		if ($this->twig !== null)
		{
			return;
		}

		if (ENVIRONMENT === 'production')
		{
			$debug = FALSE;
		}
		else
		{
			$debug = TRUE;
		}

		if ($this->loader === null)
		{
			$this->loader = new \Twig_Loader_Filesystem($this->config['paths']);
		}

		$twig = new \Twig_Environment($this->loader, [
			'cache'      => $this->config['cache'],
			'debug'      => $debug,
			'autoescape' => TRUE,
		]);

		if ($debug)
		{
			$twig->addExtension(new \Twig_Extension_Debug());
		}

		$this->twig = $twig;
	}

	protected function setLoader($loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Registers a Global
	 * 
	 * @param string $name  The global name
	 * @param mixed  $value The global value
	 */
	public function addGlobal($name, $value)
	{
		$this->createTwig();
		$this->twig->addGlobal($name, $value);
	}

	/**
	 * Renders Twig Template and Set Output
	 * 
	 * @param string $view   Template filename without `.twig`
	 * @param array  $params Array of parameters to pass to the template
	 */
	public function display($view, $params = [])
	{
		$CI =& get_instance();
		$CI->output->set_output($this->render($view, $params));
	}

	/**
	 * Renders Twig Template and Returns as String
	 * 
	 * @param string $view   Template filename without `.twig`
	 * @param array  $params Array of parameters to pass to the template
	 * @return string
	 */
	public function render($view, $params = [])
	{
		$this->createTwig();
		// We call addFunctions() here, because we must call addFunctions()
		// after loading CodeIgniter functions in a controller.
		$this->addFunctions();

		$view = $view . '.twig';
		return $this->twig->render($view, $params);
	}

	protected function addFunctions()
	{
		// Runs only once
		if ($this->functions_added)
		{
			return;
		}

		// as is functions
		foreach ($this->functions_asis as $function)
		{
			if (function_exists($function))
			{
				$this->twig->addFunction(
					new \Twig_SimpleFunction(
						$function,
						$function
					)
				);
			}
		}

		// safe functions
		foreach ($this->functions_safe as $function)
		{
			if (function_exists($function))
			{
				$this->twig->addFunction(
					new \Twig_SimpleFunction(
						$function,
						$function,
						['is_safe' => ['html']]
					)
				);
			}
		}

		// customized functions
		if (function_exists('anchor'))
		{
			$this->twig->addFunction(
				new \Twig_SimpleFunction(
					'anchor',
					[$this, 'safe_anchor'],
					['is_safe' => ['html']]
				)
			);
		}

		$this->functions_added = TRUE;
	}

	/**
	 * @param string $uri
	 * @param string $title
	 * @param array  $attributes [changed] only array is acceptable
	 * @return string
	 */
	public function safe_anchor($uri = '', $title = '', $attributes = [])
	{
		$uri = html_escape($uri);
		$title = html_escape($title);
		
		$new_attr = [];
		foreach ($attributes as $key => $val)
		{
			$new_attr[html_escape($key)] = html_escape($val);
		}

		return anchor($uri, $title, $new_attr);
	}

	/**
	 * @return \Twig_Environment
	 */
	public function getTwig()
	{
		$this->createTwig();
		return $this->twig;
	}
}
