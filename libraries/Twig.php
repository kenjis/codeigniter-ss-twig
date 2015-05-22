<?php
/**
 * Part of CodeIgniter Simple and Secure Twig
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/codeigniter-ss-twig
 */

class Twig
{
	private $functions_asis = [
		'base_url', 'site_url'
	];
	private $functions_safe = [
		'form_open', 'form_close', 'form_error', 'set_value', 'form_hidden'
	];

	private $twig;
	private $loader;

	public function createTwig()
	{
		if (ENVIRONMENT === 'production')
		{
			$debug = FALSE;
		}
		else
		{
			$debug = TRUE;
		}

		if ($this->loader === null) {
			$this->loader = new \Twig_Loader_Filesystem([VIEWPATH]);
		}

		$twig = new \Twig_Environment($this->loader, [
			'cache'      => APPPATH . '/cache/twig',
			'debug'      => $debug,
			'autoescape' => TRUE,
		]);

		if ($debug)
		{
			$twig->addExtension(new \Twig_Extension_Debug());
		}

		$this->twig = $twig;
		$this->addCIFunctions();
	}

	public function setLoader($loader)
	{
		$this->loader = $loader;
	}

	public function render($view, $params = [])
	{
		$this->createTwig();

		$view = $view . '.twig';
		$CI =& get_instance();
		$CI->output->set_output($this->twig->render($view, $params));
	}

	private function addCIFunctions()
	{
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
	}

	/**
	 * @param string $uri
	 * @param string $title
	 * @param array $attributes [changed] only array is acceptable
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
		return $this->twig;
	}
}
