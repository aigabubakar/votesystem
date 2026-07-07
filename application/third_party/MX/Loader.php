<?php
/**
 * Modular Extensions - HMVC
 * MX_Loader – extends CI_Loader to load from module paths.
 * PHP 8.2 patched.
 */
#[\AllowDynamicProperties]
class MX_Loader extends CI_Loader
{
    protected array $_module_paths = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add a module path.
     */
    public function add_module_path(string $path): void
    {
        if (!in_array($path, $this->_module_paths, true)) {
            $this->_module_paths[] = rtrim($path, '/') . '/';
        }
    }

    /**
     * Remove a module path.
     */
    public function remove_module_path(string $path): void
    {
        $this->_module_paths = array_filter(
            $this->_module_paths,
            fn($p) => $p !== rtrim($path, '/') . '/'
        );
    }

    /**
     * Get list of module paths.
     */
    public function get_module_paths(bool $include_app = false): array
    {
        return $include_app
            ? array_merge($this->_module_paths, [APPPATH])
            : $this->_module_paths;
    }

    /**
     * Load a module view.
     */
    public function view($view, $vars = [], $return = false)
    {
        // Support module/view syntax (e.g., 'admin/dashboard')
        $parts = explode('/', $view);
        if (count($parts) >= 2) {
            $module = $parts[0];
            $view_file = implode('/', array_slice($parts, 1));
            $mod_view_path = APPPATH . 'modules/' . $module . '/views/';
            if (is_file($mod_view_path . $view_file . '.php')) {
                $this->_ci_view_paths = array_merge(
                    [$mod_view_path => true],
                    $this->_ci_view_paths
                );
                $view = $view_file;
            }
        }

        // Attempt to find view in module paths added via add_module_path()
        foreach ($this->_module_paths as $module_path) {
            if (is_file($module_path . 'views/' . $view . '.php')) {
                $this->_ci_view_paths = array_merge(
                    [$module_path . 'views/' => true],
                    $this->_ci_view_paths
                );
                break;
            }
        }
        return parent::view($view, $vars, $return);
    }

    /**
     * Load a module model.
     */
    public function model($model, $name = '', $db_conn = false)
    {
        if (is_array($model)) {
            foreach ($model as $m) {
                $this->model($m);
            }
            return;
        }

        if (empty($model)) {
            return;
        }

        $name = $name ?: basename($model);

        // Already loaded?
        $CI =& get_instance();
        if (isset($CI->$name)) {
            return;
        }

        // Search module paths
        foreach ($this->_module_paths as $module_path) {
            $model_path = $module_path . 'models/' . $model . '.php';
            if (is_file($model_path)) {
                if ($db_conn !== false && !class_exists('CI_DB', false)) {
                    if ($db_conn === true) {
                        $db_conn = '';
                    }
                    $CI->load->database($db_conn, false, true);
                }
                require_once $model_path;
                $model_class = ucfirst($model);
                $CI->$name = new $model_class();
                return;
            }
        }

        // Fall back to CI default loader
        parent::model($model, $name, $db_conn);
    }
}
