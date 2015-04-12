<?php
/**
 * Project: Veemo
 * User: ddedic
 * Email: dedic.d@gmail.com
 * Date: 12/04/15
 * Time: 23:07
 */



/**
 * Get the theme instance.
 *
 * @param  string  $themeType
 * @param  string  $themeName
 * @param  string  $layoutName
 * @return \Veemo\Core\Themes\Theme
 */
function theme($themeType = 'frontend', $themeName = null, $layoutName = null)
{
    $theme = app('veemo.themes');

    if ($themeName)
    {
        $theme->$themeType()->uses($themeName);
    }

    if ($layoutName)
    {
        $theme->layout($layoutName);
    }

    return $theme;
}


/**
 * @param string $filename
 * @param bool $imgTag
 * @param array $attributes
 * @param bool $secure
 * @return string
 */
function theme_image($filename = null, $imgTag = false, $attributes = [],  $secure = false)
{

    if ($filename && $filename !== null)
    {
        $theme = app('veemo.themes');

        return $theme->asset()->image($filename, $imgTag, $attributes,  $secure);
    }

    // @todo ENV detect
    app('log')->error('Method {theme_image} error. Filename parameter is not set.');

    return null;
}



/**
 * @param string $module
 * @param string $filename
 * @param bool $imgTag
 * @param array $attributes
 * @param bool $secure
 * @return string
 */
function module_image($module = null, $filename = null, $imgTag = false, $attributes = [],  $secure = false)
{

    if ($module && $module !== null && $filename && $filename !== null)
    {
        $theme = app('veemo.themes');

        return $theme->asset()->module($module)->image($filename, $imgTag, $attributes,  $secure);
    }

    // @todo ENV detect
    app('log')->error('Method {module_image} error. Module name or Filename parameter is not set.');


    return null;
}