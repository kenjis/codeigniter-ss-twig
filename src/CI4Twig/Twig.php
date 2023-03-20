<?php
/**
 * Part of CodeIgniter Simple and Secure Twig
 *
 * @license   MIT License
 * @copyright 2015 Kenji Suzuki
 * @see       https://github.com/kenjis/codeigniter-ss-twig
 */

namespace Kenjis\CI4Twig;

use Config\Services;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Twig
{
    /**
     * @var array Paths to Twig templates
     */
    private $paths = [];

    /**
     * @var array Twig Environment Options
     *
     * @see https://twig.symfony.com/doc/3.x/api.html#environment-options
     */
    private $config = [];

    /**
     * @var array Functions to add to Twig
     */
    private array $functions_asis = [
        'base_url',
        'site_url',
    ];

    /**
     * @var array Filters to add to Twig
     */
    private array $filters = [];

    /**
     * @var array Functions with `is_safe` option
     *
     * @see https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
     */
    private array $functions_safe = [
        'form_open',
        'form_close',
        'form_error',
        'form_hidden',
        'set_value',
        'csrf_field',
        // 'form_open_multipart', 'form_upload', 'form_submit', 'form_dropdown',
        // 'set_radio', 'set_select', 'set_checkbox',
    ];

    /**
     * @var bool Whether functions are added or not
     */
    private bool $functions_added = false;

    /**
     * @var bool Whether filters are added or not
     */
    private bool $filters_added = false;

    private ?Environment $twig = null;

    /**
     * @var FilesystemLoader
     */
    private $loader;

    public function __construct($params = [])
    {
        if (isset($params['functions'])) {
            $this->functions_asis = array_unique(
                array_merge($this->functions_asis, $params['functions'])
            );
            unset($params['functions']);
        }

        if (isset($params['functions_safe'])) {
            $this->functions_safe = array_unique(
                array_merge(
                    $this->functions_safe,
                    $params['functions_safe']
                )
            );
            unset($params['functions_safe']);
        }

        if (isset($params['filters'])) {
            $this->filters = array_unique(
                array_merge($this->filters, $params['filters'])
            );
            unset($params['filters']);
        }

        if (isset($params['paths'])) {
            $this->paths = $params['paths'];
            unset($params['paths']);
        } else {
            $this->paths = [APPPATH . 'Views/'];
        }

        // default Twig config
        /** @psalm-suppress UndefinedConstant */
        $this->config = [
            'cache'      => WRITEPATH . 'cache/twig',
            'debug'      => ENVIRONMENT !== 'production',
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
            $this->loader = new FilesystemLoader($this->paths);
        }

        $twig = new Environment($this->loader, $this->config);

        if ($this->config['debug']) {
            $twig->addExtension(new DebugExtension());
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
     * Renders Twig Template and Outputs
     *
     * @param string $view   Template filename without `.twig`
     * @param array  $params Array of parameters to pass to the template
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function display($view, $params = [])
    {
        echo $this->render($view, $params);
    }

    /**
     * Renders Twig Template and Returns as String
     *
     * @param string $view   Template filename without `.twig`
     * @param array  $params Array of parameters to pass to the template
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render($view, $params = []): string
    {
        $this->createTwig();

        // We call addFunctions() here, because we must call addFunctions()
        // after loading CodeIgniter functions in a controller.
        $this->addFunctions();
        $this->addFilters();

        $view = $view . '.twig';

        return $this->twig->render($view, $params);
    }

    protected function addFilters()
    {
        // Runs only once
        if ($this->filters_added) {
            return;
        }

        foreach ($this->filters as $filter) {
            if (function_exists($filter)) {
                $this->twig->addFilter(
                    new TwigFilter($filter, $filter)
                );
            }
        }

        $this->filters_added = true;
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
                    new TwigFunction($function, $function)
                );
            }
        }

        // safe functions
        foreach ($this->functions_safe as $function) {
            if (function_exists($function)) {
                $this->twig->addFunction(
                    new TwigFunction(
                        $function,
                        $function,
                        ['is_safe' => ['html']]
                    )
                );
            }
        }

        // customized functions
        $this->addCustomizedFunctions();

        $this->functions_added = true;
    }

    protected function addCustomizedFunctions()
    {
        $functions = array_merge($this->functions_asis, $this->functions_safe);

        if (! in_array('anchor', $functions, true) && function_exists('anchor')) {
            $this->twig->addFunction(
                new TwigFunction(
                    'anchor',
                    [$this, 'safe_anchor'],
                    ['is_safe' => ['html']]
                )
            );
        }

        if (! in_array('validation_list_errors', $functions, true)) {
            $this->twig->addFunction(
                new TwigFunction(
                    'validation_list_errors',
                    [$this, 'validation_list_errors'],
                    ['is_safe' => ['html']]
                )
            );
        }
    }

    /**
     * @param string $uri
     * @param string $title
     * @param array  $attributes only array is acceptable
     */
    public function safe_anchor(
        $uri = '',
        $title = '',
        $attributes = []
    ): string {
        $uri   = esc($uri, 'url');
        $title = esc($title);

        $new_attr = [];

        foreach ($attributes as $key => $val) {
            $new_attr[esc($key)] = $val;
        }

        return anchor($uri, $title, $new_attr);
    }

    public function validation_list_errors(): string
    {
        return Services::validation()->listErrors();
    }

    public function getTwig(): Environment
    {
        $this->createTwig();

        return $this->twig;
    }
}
