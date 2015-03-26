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


        $this->active = $this->getConfig('themeDefault');

    }

    /**
     * Register custom namespaces for all themes.
     *
     * @return null
     */
    public function register()
    {
        $this->uses($this->getActive());
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

        // Add location to look up view.
        $this->addPathLocation($this->path() . '/views');

        //dd($this->path() . '/views');

        // Fire event before set up a theme.
        //$this->fire('before', $this);

        // Before from a public theme config.
        //$this->fire('appendBefore', $this);

        // Add asset path to asset container.
        //$this->asset->addPath($this->path().'/'.$this->getConfig('containerDir.asset'));

        return $this;

    }

    /**
     * Get theme path.
     *
     * @param  string $forceThemeName
     * @return string
     */
    public function path($forceThemeName = null)
    {
        $themeDir = $this->getConfig('themeDir');

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
        if ($this->getConfig('parent')) {
            // Inherit from theme name.
            $parent = $this->getConfig('parent') . '/views';

            // Inherit theme path.
            $parentPath = $this->path($parent);

            if ($this->files->isDirectory($parentPath)) {
                array_push($hints, $parentPath);
            }
        }


        //dd($hints);

        // Add namespace with hinting paths.
        $this->viewFactory->addNamespace($this->getThemeNamespace(), $hints);
    }




    /**
     * Check if given theme exists.
     *
     * @param  string $theme
     * @return bool
     */
    public function exists($theme)
    {
        $themes = $this->manager->all();

        foreach ($themes as $name => $config) {
            if (strtolower($theme) == strtolower($name))
                return true;
        }

    }

    /**
     * Gets themes path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path ?: $this->config->get('veemo.themes.themeDir');
    }

    /**
     * Sets themes path.
     *
     * @param string $path
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
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
     * Sets active theme.
     *
     * @return Themes
     */
    public function setActive($theme)
    {
        $this->active = $theme;

        return $this;
    }

    /**
     * Get theme layout.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Sets theme layout.
     *
     * @return Themes
     */
    public function setLayout($layout)
    {
        $this->layout = $this->getThemeNamespace($layout);

        return $this;
    }

    /**
     * Render theme view file.
     *
     * @param string $view
     * @param array $data
     * @return View
     */
    public function view($view, $data = array())
    {
        $parent = null;
        $viewNamespace = null;


        // MAIN THEME
        $views['theme'] = $this->getThemeNamespace($view);

        // PARENT THEME
        $views['parent'] = $this->getThemeParentNamespace($view);

        // MODULE
        $views['module'] = $this->getModuleView($view);

        // BASE
        $views['base'] = $view;

        //dd($views);

        foreach ($views as $view) {

            if ($this->viewFactory->exists($view)) {
                $viewNamespace = $view;
                break;
            }
        }


        if($viewNamespace == null)
            throw new UnknownViewFileException(("View [$view] not found."));
        else
            return $this->renderView($viewNamespace, $data);

    }

    /**
     * Renders the defined view.
     *
     * @param  string $view
     * @param  mixed $data
     * @return viewFactory
     */
    protected function renderView($view, $data)
    {
        //$this->autoloadComponents($this->getActive());

        if (!is_null($this->layout)) {
            $data['theme_layout'] = $this->getLayout();
        }

        return $this->viewFactory->make($view, $data);

    }

    /**
     * Return a new theme view response from the application.
     *
     * @param  string $view
     * @param  array $data
     * @param  int $status
     * @param  array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response($view, $data = array(), $status = 200, array $headers = array())
    {
        return new Response($this->view($view, $data), $status, $headers);
    }

    /**
     * Gets the specified themes path.
     *
     * @param string $theme
     * @return string
     */
    public function getThemePath($theme)
    {
        return $this->getPath() . "/{$theme}/";
    }


    /**
     * Autoload a themes compontents file.
     *
     * @param  string $theme
     * @return null
     */
    protected function autoloadComponents($theme)
    {
        $activeTheme = $this->getActive();
        $path = $this->getPath();
        $parent = null;//$this->getProperty($activeTheme.'::parent');
        $themePath = $path . '/' . $theme;
        $componentsFilePath = $themePath . '/components.php';

        if (!empty($parent)) {
            $parentPath = $path . '/' . $parent;
            $parentComponentsFilePath = $parentPath . '/components.php';

            if (file_exists($parentPath)) {
                include($parentComponentsFilePath);
            }
        }

        if (file_exists($componentsFilePath)) {
            include($componentsFilePath);
        }
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

            if ($viewSegments[0] == 'modules') {
                $module = $viewSegments[1];
                $view = implode('.', array_slice($viewSegments, 2));

                return "module.{$module}::{$view}";
            }

        }


        return null;
    }



    // -------------------------------

    /**
     * Get theme config.
     *
     * @param  string $key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        // Main package config.
        if (!$this->themeConfig) {
            $this->themeConfig = $this->config->get('veemo.themes');
        }

        // Config inside a public theme.
        // This config having buffer by array object.
        if ($this->active and !isset($this->themeConfig['themes'][$this->active])) {
            $this->themeConfig['themes'][$this->active] = array();

            try {
                // Require public theme config.
                $minorConfigPath = $this->themeConfig['themeDir'] . '/' . $this->active . '/config.php';

                $this->themeConfig['themes'][$this->active] = $this->files->getRequire($minorConfigPath);
            } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                //var_dump($e->getMessage());
            }
        }

        // Evaluate theme config.
        $this->themeConfig = $this->evaluateConfig($this->themeConfig);

        return is_null($key) ? $this->themeConfig : array_get($this->themeConfig, $key);
    }


    /**
     * Evaluate config.
     *
     * Config minor is at public folder [theme]/config.php,
     * thet can be override package config.
     *
     * @param  mixed $config
     * @return mixed
     */
    protected function evaluateConfig($config)
    {
        if (!isset($config['themes'][$this->active])) {
            return $config;
        }

        // Config inside a public theme.
        $minorConfig = $config['themes'][$this->active];

        // Before event is special case, It's combination.
        if (isset($minorConfig['events']['before'])) {
            $minorConfig['events']['appendBefore'] = $minorConfig['events']['before'];

            unset($minorConfig['events']['before']);
        }

        // Merge two config into one.
        $config = array_replace_recursive($config, $minorConfig);

        // Reset theme config.
        $config['themes'][$this->active] = array();

        return $config;
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
     * Get current layout name.
     *
     * @return string
     */
    public function getLayoutName()
    {
        return $this->layout;
    }


    /**
     * Get theme namespace.
     *
     * @param string $path
     *
     * @return string
     */
    public function getThemeNamespace($path = '', $theme = null)
    {
        if ($theme == null) {
            // Namespace relate with the theme name.
            $namespace = static::$namespace . '.' . $this->getThemeName();

        } else {
            $namespace = static::$namespace . '.' . $theme;

        }

        if ($path != false) {
            return $namespace . '::' . $path;
        }

        return $namespace;
    }

    /**
     * @param string $path
     * @param null $theme
     * @return null|string
     */
    public function getThemeParentNamespace($path = '', $theme = null)
    {
        if ($this->getConfig('parent')) {
            // Inherit from theme name.
            $parent = $this->getConfig('parent');

            // Inherit theme path.
            $parentPath = $this->path($parent);

            if ($this->files->isDirectory($parentPath)) {
                $namespace = static::$namespace . '.' . $parent;

                if ($path != false) {
                    return $namespace . '::' . $path;
                }

                return $namespace;

            }
        }

        return null;
    }


}
