<?php
/**
 * Modular Extensions - HMVC
 * Wiredesignz 2023 – PHP 8.2 patched build
 *
 * Modules class: handles module discovery, file location, and loading.
 */
class MX_Modules
{
    public static array $registry  = [];
    public static array $locations = [];

    /**
     * Register module locations.
     */
    public static function register(array $locations): void
    {
        foreach ($locations as $location => $offset) {
            if (is_dir($location)) {
                self::$locations[$location] = $offset;
            }
        }
    }

    /**
     * Load a module file.
     */
    public static function load(array $module): bool|string
    {
        [$type, $file] = $module;
        return self::find($type, $file, '');
    }

    /**
     * Find a module resource file.
     *
     * @param string $file   Dot-separated path: module[/subpath]
     * @param string $base   One of: controllers, models, views, config, etc.
     * @param string $ext    File extension (with dot)
     */
    public static function find(string $file, string $base, string $ext): bool|array
    {
        $segments = explode('/', $file);
        $file     = array_pop($segments);

        $module = implode('/', $segments);

        foreach (self::$locations as $location => $offset) {
            $module_path = $location . ($module ? $module . '/' : '');

            // Try direct path first (e.g. module/base/file.php)
            if (is_file($path = $module_path . $base . $file . $ext)) {
                return [$module_path, $file];
            }

            // Try module sub-path
            foreach (glob($location . '*', GLOB_ONLYDIR) as $mod_dir) {
                $mod_name = basename($mod_dir);
                if ($mod_name === $module || !$module) {
                    if (is_file($path = $mod_dir . '/' . $base . $file . $ext)) {
                        return [$mod_dir . '/', $file];
                    }
                }
            }
        }
        return false;
    }

    /**
     * Parse a module from a URI string like "module/controller/method"
     */
    public static function parse_routes(string $module, string $uri): ?string
    {
        foreach (self::$locations as $location => $offset) {
            if (is_dir($location . $module)) {
                $config = $location . $module . '/config/routes.php';
                if (is_file($config)) {
                    include $config;
                    if (isset($route) && is_array($route)) {
                        foreach ($route as $pattern => $target) {
                            $pattern = str_replace([':any', ':num'], ['.+', '[0-9]+'], $pattern);
                            if (preg_match('#^' . $pattern . '$#', $uri)) {
                                return preg_replace('#^' . $pattern . '$#', $target, $uri);
                            }
                        }
                    }
                }
                return $uri;
            }
        }
        return null;
    }
}
