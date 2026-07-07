<?php
/**
 * Modular Extensions - HMVC
 * MX_Router – extends CI_Router to support modules.
 * PHP 8.2 patched.
 */
#[\AllowDynamicProperties]
class MX_Router extends CI_Router
{
    public ?string $module = null;

    public function fetch_module(): ?string
    {
        return $this->module;
    }

    public function set_directory($dir, $append = FALSE)
    {
        $this->directory = $dir . '/';
    }

    /**
     * Override _validate_request to detect module/controller segments.
     */
    protected function _validate_request($segments)
    {
        if (empty($segments)) {
            return $segments;
        }

        // Check if the first segment is a module
        foreach (MX_Modules::$locations as $location => $offset) {
            if (is_dir($location . $segments[0])) {
                $this->module = $segments[0];
                $this->set_directory('../modules/' . $segments[0] . '/controllers/');
                
                // Check if the second segment is the controller
                if (isset($segments[1]) && is_file($location . $segments[0] . '/controllers/' . ucfirst($segments[1]) . '.php')) {
                    return array_values(array_slice($segments, 1));
                }
                // Fallback: check if the first segment is the controller
                if (is_file($location . $segments[0] . '/controllers/' . ucfirst($segments[0]) . '.php')) {
                    $segments[0] = strtolower($segments[0]);
                    return $segments;
                }

                return $segments;
            }
        }

        return parent::_validate_request($segments);
    }

    /**
     * Returns the module-qualified controller class name if inside a module.
     */
    public function fetch_class(): string
    {
        return $this->class;
    }

    protected function _set_default_controller()
    {
        if (empty($this->default_controller)) {
            show_error('Unable to determine what should be displayed. A default route has not been specified in the routing file.');
        }

        if (sscanf($this->default_controller, '%[^/]/%s', $class, $method) !== 2) {
            $method = 'index';
        }

        // Standard controller check
        if (file_exists(APPPATH . 'controllers/' . $this->directory . ucfirst($class) . '.php')) {
            $this->set_class($class);
            $this->set_method($method);
            $this->uri->rsegments = [1 => $class, 2 => $method];
            return;
        }

        // Module check
        if ($segments = $this->_validate_request([$class, $method])) {
            $this->set_class($segments[0]);
            $this->set_method($segments[1] ?? 'index');
            $this->uri->rsegments = [1 => $segments[0], 2 => $segments[1] ?? 'index'];
            return;
        }
    }
}
