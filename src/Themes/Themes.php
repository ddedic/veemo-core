<?php
/**
 * Project: Veemo
 * User: ddedic
 * Email: dedic.d@gmail.com
 * Date: 16/03/15
 * Time: 20:44
 */

namespace Veemo\Core\Themes;

use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\View\Factory as ViewFactory;

use Veemo\Core\Themes\Exceptions\UnknownThemeException;
use Veemo\Core\Themes\Exceptions\UnknownViewFileException;
use Veemo\Core\Themes\Exceptions\UnknownLayoutFileException;
use Veemo\Core\Themes\Exceptions\UnknownPartialFileException;


/**
 * Class Themes
 * @package Veemo\Core\Themes
 */
class Themes
{
    /**
     * @var string
     */
    public static $namespace = 'theme';

    /**
     * @var string
     */
    protected $active;

    /**
     * @var string
     */
    protected $layout;

    /**
     * Regions in the theme.
     *
     * @var array
     */
    protected $regions = array();

    /**
     * Content arguments.
     *
     * @var array
     */
    protected $arguments = array();

    /**
     * Data bindings.
     *
     * @var array
     */
    protected $bindings = array();

    /**
     * Content dot path.
     *
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var ThemeManager;
     */
    protected $manager;

    /**
     * @var array
     */
    protected $components;

    /**
     * Theme configuration.
     *
     * @var mixed
     */
    protected $themeConfig;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var View
     */
    protected $viewFactory;

    /**
     * Constructor method.
     *
     * @param ThemeManager $manager
     * @param Filesystem $files
     * @param Repository $config
     * @param ViewFactory $viewFactory
     */
    public function __construct(ThemeManager $manager, Filesystem $files, Repository $config, ViewFactory $viewFactory)
    {
        $this->manager = $manager;
        $this->config = $config;
        $this->files = $files;
        $this->viewFactory = $viewFactory;

        $this->active = $this->getActive();
        $this->layout = $this->getLayout();
        $this->type = $this->getType();
    }

    /**
     * Register theme
     *
     * @return null
     */
    public function registers()
    {
        $this->uses($this->getActive());
    }


    /**
     * @return $this
     */
    public function frontend()
    {
        $this->type = 'frontend';

        return $this;
    }


    /**
     * @return $this
     */
    public function backend()
    {
        $this->type = 'backend';

        return $this;
    }


    /**
     * @param $theme
     * @return $this
     */
    public function uses($theme)
    {

        // If theme name is not set, so use default from config.
        if ($theme != false) {
            $this->active = $theme;
        }


        // Is theme ready?
        if (!$this->exists($theme)) {
            throw new UnknownThemeException("Theme [$theme] not found.");
        }

        // Load theme config
        $this->themeConfig = $this->getThemeConfig();

        // Set default theme layout
        $this->layout = $this->getThemeConfig('default_layout');


        // Add location to look up view.
        $this->addPathLocation($this->path() . '/views');


        return $this;

    }


    /**
     * Render theme view file.
     *
     * @param string $view
     * @param array $data
     * @param int $statusCode
     * @return View
     */
    public function view($view, $data = array(), $statusCode = 200)
    {
        //dd($this->viewFactory->getFinder());

        $viewNamespace = null;

        // MAIN THEME / PARENT THEME
        $views['theme'] = $this->getThemeNamespace($view);

        // MODULE
        $views['module'] = $this->getModuleView($view);

        // BASE
        $views['base'] = $view;

        /*
        echo "<pre>";
        print_r($views);
        print_r($this->viewFactory->getFinder());
        echo "</pre>";
        echo "<hr />";
        echo "<br />";
        */

        foreach ($views as $view) {

            if ($this->viewFactory->exists($view)) {
                $viewNamespace = $view;
                break;
            }
        }


        if ($viewNamespace == null) {
            throw new UnknownViewFileException(("Theme [$this->active] View [$view] not found."));
        }


        $content = $this->viewFactory->make($viewNamespace, $data)->render();

        // View path of content.
        $this->content = $view;


        // Set up a content regional.
        $this->regions['content'] = $content;


        return $this;
    }


    /**
     * Return a template with content.
     *
     * @param  integer $statusCode
     * @throws UnknownLayoutFileException
     * @return Response
     */
    public function render($statusCode = 200)
    {
        $path = $this->getThemeNamespace('layouts.' . $this->layout);

        if (!$this->viewFactory->exists($path)) {
            throw new UnknownLayoutFileException("Layout [$this->layout] not found.");
        }

        $content = $this->viewFactory->make($path)->render();

        // Append status code to view.
        $content = new Response($content, $statusCode);

        return $content;
    }

    /**
     * Find view location.
     *
     * @param  boolean $realpath
     * @return string
     */
    public function location($realpath = false)
    {
        if ($this->viewFactory->exists($this->content))
        {
            return ($realpath) ? $this->viewFactory->getFinder()->find($this->content) : $this->content;
        }
    }


    /**
     * Gets active theme.
     *
     * @return string
     */
    public function getActive()
    {
        return $this->active ?: $this->config->get('veemo.themes.themeDefault');
    }


    /**
     * Gets active layout.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout ?: $this->config->get('veemo.themes.layoutDefault');
    }

    /**
     * Set up a layout name.
     *
     * @param  string $layout
     * @return Theme
     */
    public function layout($layout)
    {
        // If layout name is not set, so use default from config.
        if ($layout != false) {
            $this->layout = $layout;
        }

        return $this;
    }

    /**
     * Gets active theme.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type ?: $this->config->get('veemo.themes.themeDefaultType');
    }

    /**
     * Check if given theme exists.
     *
     * @param  string $theme
     * @return bool
     */
    public function exists($theme)
    {
        $themes = [];

        if ($this->type == 'frontend') {
            $themes = $this->manager->frontend()->toArray();

        } elseif ($this->type == 'backend') {
            $themes = $this->manager->backend()->toArray();
        }


        foreach ($themes as $name => $config) {
            if (strtolower($theme) == strtolower($name))
                return true;
        }

    }


    /**
     * Get theme config.
     *
     * @param  string $key
     * @return mixed
     */
    public function getThemeConfig($key = null)
    {
        // Config inside a public theme.
        // This config having buffer by array object.
        if ($this->active and $this->type) {
            $this->themeConfig = [];


            // @todo Catch and throw custom exception
            // try {
            $theme_path = $this->config->get('veemo.themes.themeDir.' . $this->type) . '/' . $this->active;

            // Require public theme config.
            $minorConfigPath = $theme_path . '/config.php';
            $this->themeConfig = $this->files->getRequire($minorConfigPath);

            // Setup theme path
            $this->themeConfig['path'] = $theme_path;


            // } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
            //     var_dump($e->getMessage());
            // }
        }

        return is_null($key) ? $this->themeConfig : array_get($this->themeConfig, $key);
    }


    /**
     * Get current theme name.
     *
     * @return string
     */
    public function getThemeName()
    {
        return $this->active;
    }


    /**
     * Get theme path.
     *
     * @param  string $forceThemeName
     * @return string
     */
    public function path($forceThemeName = null)
    {
        $themeDir = $this->config->get('veemo.themes.themeDir.' . $this->type);
        $theme = $this->active;

        if ($forceThemeName != false) {
            $theme = $forceThemeName;
        }

        return $themeDir . '/' . $theme;
    }


    /**
     * Add location path to look up.
     *
     * @param string $location
     */
    protected function addPathLocation($location)
    {
        // First path is in the selected theme.
        $hints[] = $location;


        // This is nice feature to use inherit from another.
        if ($this->getThemeConfig('parent')) {
            // Inherit from theme name.
            $parent = $this->getThemeConfig('parent') . '/views';

            // Inherit theme path.
            $parentPath = $this->path($parent);

            if ($this->files->isDirectory($parentPath)) {
                array_push($hints, $parentPath);
            }
        }


        // Add namespace with hinting paths.
        $this->viewFactory->addNamespace($this->getThemeNamespace(), $hints);
    }


    /**
     * Get theme namespace.
     *
     * @param string $path
     *
     * @return string
     */
    public function getThemeNamespace($path = '')
    {
        $namespace = static::$namespace . '.' . $this->type;

        if ($path != false) {
            return $namespace . '::' . $path;
        }

        return $namespace;
    }


    /**
     * Get module view file.
     *
     * @param  string $view
     * @return null|string
     */
    protected function getModuleView($view)
    {
        if (app('Veemo\Core\Modules\Modules')) {

            $viewSegments = explode('.', $view);
            $moduleViewNamespace = null;
            $views = [];

            if ($viewSegments[0] == 'modules') {
                $module = $viewSegments[1];
                $view = implode('.', array_slice($viewSegments, 2));


                // --- TYPE SPECIFIC

                // views/{type}/{current_theme}
                $views['type_plus_current_theme'] = "module.{$module}::{$this->type}.{$this->active}.{$view}";

                // views/{type}/{parent_theme}
                if ($this->getThemeConfig('parent') !== null) {
                    $views['type_plus_parent_theme'] = "module.{$module}::{$this->type}.{$this->getThemeConfig('parent')}.{$view}";
                }

                // views/{type}/{default_theme}
                $views['type_plus_default_theme'] = "module.{$module}::{$this->type}.{$this->config->get('veemo.themes.themeDefault')}.{$view}";


                // --- THEME SPECIFIC

                // views/{current_theme}
                $views['current_theme'] = "module.{$module}::{$this->active}.{$view}";

                // views/parent_theme}
                if ($this->getThemeConfig('parent') !== null) {
                    $views['parent_theme'] = "module.{$module}::{$this->getThemeConfig('parent')}.{$view}";
                }

                // views/default_theme}
                $views['default_theme'] = "module.{$module}::{$this->config->get('veemo.themes.themeDefault')}.{$view}";


                // --- BASE SPECIFIC

                // views/{current_theme}
                $views['module_base_view'] = "module.{$module}::{$view}";


                foreach ($views as $module_view) {

                    if ($this->viewFactory->exists($module_view)) {
                        $moduleViewNamespace = $module_view;
                        break;
                    }
                }

                return $moduleViewNamespace ?: null;
            }

        }


        return null;
    }


    /**
     * Set up a partial.
     *
     * @param  string $view
     * @param  array $args
     * @throws UnknownPartialFileException
     * @return mixed
     */
    public function partial($view, $args = array())
    {
        $partial = null;
        $moduleViewNamespace = null;


        $viewSegments = explode('.', $view);
        $partialViews = [];

        if ($viewSegments[0] == 'modules') {

            $module = $viewSegments[1];
            $view = implode('.', array_slice($viewSegments, 2));


            // Check public/themes
            $partialViews['theme'] = $this->getThemeNamespace("modules.{$module}.partials.{$view}");

            // Check module
            $partialViews['module'] = $this->getModuleView("modules.{$module}.partials.{$view}");

            // Check base
            $partialViews['base'] = $module . '/partials/' . $view ;


        } else {

            // Check public/themes
            $partialViews['theme'] = $this->getThemeNamespace('partials/' . $view);

            // Check base
            $partialViews['base'] = 'partials/' . $view;


        }


        //dd($partialViews);

        foreach ($partialViews as $partialView) {

            if ($this->viewFactory->exists($partialView)) {
                $partial = $partialView;
                break;
            }
        }

        //dd($partialViews);

        if ( $partial == null)
        {
            throw new UnknownPartialFileException("Partial view [$view] not found.");
        }

        return $this->loadPartial($partial, $args);
    }

    /**
     * Load a partial
     *
     * @param  string $view
     * @param  array  $args
     * @throws UnknownPartialFileException
     * @return mixed
     */
    public function loadPartial($view, $args)
    {

        $partial = $this->viewFactory->make($view, $args)->render();

        $this->regions[$view] = $partial;

        return $this->regions[$view];
    }

    /**
     * Assign data across all views.
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return mixed
     */
    public function share($key, $value)
    {
        return $this->viewFactory->share($key, $value);
    }


    /**
     * Check region exists.
     *
     * @param  string $region
     * @return boolean
     */
    public function has($region)
    {
        return (boolean)isset($this->regions[$region]);
    }

    /**
     * Render a region.
     *
     * @param  string $region
     * @param  mixed $default
     * @return string
     */
    public function get($region, $default = null)
    {
        if ($this->has($region)) {
            return $this->regions[$region];
        }

        return $default ? $default : '';
    }

    /**
     * Render a region.
     *
     * @param  string $region
     * @param  mixed $default
     * @return string
     */
    public function place($region, $default = null)
    {
        return $this->get($region, $default);
    }

    /**
     * Place content in sub-view.
     *
     * @return string
     */
    public function content()
    {
        return $this->regions['content'];
    }

    /**
     * Set a place to regions.
     *
     * @param  string $region
     * @param  string $value
     * @return Theme
     */
    public function set($region, $value)
    {
        // Content is reserve region for render sub-view.
        if ($region == 'content') return;

        $this->regions[$region] = $value;

        return $this;
    }

    /**
     * Append a place to existing region.
     *
     * @param  string $region
     * @param  string $value
     * @return Theme
     */
    public function append($region, $value)
    {
        return $this->appendOrPrepend($region, $value, 'append');
    }

    /**
     * Prepend a place to existing region.
     *
     * @param  string $region
     * @param  string $value
     * @return Theme
     */
    public function prepend($region, $value)
    {
        return $this->appendOrPrepend($region, $value, 'prepend');
    }

    /**
     * Append or prepend existing region.
     *
     * @param  string $region
     * @param  string $value
     * @param  string $type
     * @return Theme
     */
    protected function appendOrPrepend($region, $value, $type = 'append')
    {
        // If region not found, create a new region.
        if (isset($this->regions[$region])) {
            if ($type == 'prepend') {
                $this->regions[$region] = $value . $this->regions[$region];
            } else {
                $this->regions[$region] .= $value;
            }
        } else {
            $this->set($region, $value);
        }

        return $this;
    }


    /**
     * Magic method for set, prepend, append, has, get.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters = array())
    {
        $callable = preg_split('|[A-Z]|', $method);

        if (in_array($callable[0], array('set', 'prepend', 'append', 'has', 'get'))) {
            $value = lcfirst(preg_replace('|^' . $callable[0] . '|', '', $method));

            array_unshift($parameters, $value);

            return call_user_func_array(array($this, $callable[0]), $parameters);
        }

        trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);
    }

}