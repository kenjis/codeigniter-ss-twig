<?php
/**
 * Part of CodeIgniter Simple and Secure Twig
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/codeigniter-ss-twig
 */

namespace Kenjis\CI4Twig;

class Twig
{
    /**
     * @var array Paths to Twig templates
     */
    private $paths = [];

    /**
     * @var array Twig Environment Options
     * @see https://twig.symfony.com/doc/3.x/api.html#environment-options
     */
    private $config = [];

    /**
     * @var array Functions to add to Twig
     */
    private $functions_asis = [
        'base_url',
        'site_url',
    ];

    /**
     * @var array Functions with `is_safe` option
     * @see https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
     */
    private $functions_safe = [
        'form_open',
        'form_close',
        'form_error',
        'form_hidden',
        'set_value',
        'csrf_field',
//		'form_open_multipart', 'form_upload', 'form_submit', 'form_dropdown',
//		'set_radio', 'set_select', 'set_checkbox',
    ];

    /**
     * @var bool Whether functions are added or not
     */
    private $functions_added = false;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var \Twig\Loader\FilesystemLoader
     */
    private $loader;

    public function __construct($params = [])
    {
        if (isset($params['functions'])) {
            $this->functions_asis =
                array_unique(
                    array_merge($this->functions_asis, $params['functions'])
                );
            unset($params['functions']);
        }

        if (isset($params['functions_safe'])) {
            $this->functions_safe =
                array_unique(
                    array_merge(
                        $this->functions_safe,
                        $params['functions_safe']
                    )
                );
            unset($params['functions_safe']);
        }

        if (isset($params['paths'])) {
            $this->paths = $params['paths'];
            unset($params['paths']);
        } else {
            $this->paths = APPPATH . 'Views/';
        }

        // default Twig config
        $this->config = [
            'cache' => WRITEPATH . 'cache/twig',
            'debug' => ENVIRONMENT !== 'production',
            'autoescape' => 'html',
        ];

        $this->config = array_merge($this->config, $params);
    }

    protected function resetTwig()
    {
        $this->twig = null;
        $this->createTwig();
    }

    protected function createTwig()
    {
        // $this->twig is singleton
        if ($this->twig !== null) {
            return;
        }

        if ($this->loader === null) {
            $this->loader = new \Twig\Loader\FilesystemLoader($this->paths);
        }

        $twig = new \Twig\Environment($this->loader, $this->config);

        if ($this->config['debug']) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
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
     * @param string $name The global name
     * @param mixed $value The global value
     */
    public function addGlobal($name, $value)
    {
        $this->createTwig();
        $this->twig->addGlobal($name, $value);
    }

    /**
     * Renders Twig Template and Outputs
     *
     * @param string $view Template filename without `.twig`
     * @param array $params Array of parameters to pass to the template
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function display($view, $params = [])
    {
        echo $this->render($view, $params);
    }

    /**
     * Renders Twig Template and Returns as String
     *
     * @param string $view Template filename without `.twig`
     * @param array $params Array of parameters to pass to the template
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render($view, $params = []): string
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
        if ($this->functions_added) {
            return;
        }

        // as is functions
        foreach ($this->functions_asis as $function) {
            if (function_exists($function)) {
                $this->twig->addFunction(
                    new \Twig\TwigFunction(
                        $function,
                        $function
                    )
                );
            }
        }

        // safe functions
        foreach ($this->functions_safe as $function) {
            if (function_exists($function)) {
                $this->twig->addFunction(
                    new \Twig\TwigFunction(
                        $function,
                        $function,
                        ['is_safe' => ['html']]
                    )
                );
            }
        }

        // customized functions
        if (function_exists('anchor')) {
            $this->twig->addFunction(
                new \Twig\TwigFunction(
                    'anchor',
                    [$this, 'safe_anchor'],
                    ['is_safe' => ['html']]
                )
            );
        }

        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'validation_list_errors',
                [$this, 'validation_list_errors'],
                ['is_safe' => ['html']]
            )
        );

        $this->functions_added = true;
    }

    /**
     * @param string $uri
     * @param string $title
     * @param array $attributes only array is acceptable
     * @return string
     */
    public function safe_anchor(
        $uri = '',
        $title = '',
        $attributes = []
    ): string {
        $uri = esc($uri, 'url');
        $title = esc($title);

        $new_attr = [];
        foreach ($attributes as $key => $val) {
            $new_attr[esc($key)] = $val;
        }

        return anchor($uri, $title, $new_attr);
    }

    public function validation_list_errors(): string
    {
        return \Config\Services::validation()->listErrors();
    }

    /**
     * @return \Twig\Environment
     */
    public function getTwig(): \Twig\Environment
    {
        $this->createTwig();
        return $this->twig;
    }
}
