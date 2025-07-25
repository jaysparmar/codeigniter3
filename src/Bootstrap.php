<?php

namespace CodeIgniter3;

use CodeIgniter3\Constants\Env;
use Dotenv\Dotenv;

class Bootstrap
{
    public static function init()
    {
        // Define CI_VERSION constant if not already defined
        if (!defined('CI_VERSION')) {
            define('CI_VERSION', '3.1.13');
        }

        self::loadENV();
        Env::init();

        switch (ENVIRONMENT) {
            case 'development':
                error_reporting(-1);
                ini_set('display_errors', 1);
                break;

            case 'testing':
            case 'production':
                ini_set('display_errors', 0);
                if (version_compare(PHP_VERSION, '5.3', '>=')) {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
                } else {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
                }
                break;

            default:
                header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
                echo 'The application environment is not set correctly.';
                exit(1); // EXIT_ERROR
        }


        /*
           * ---------------------------------------------------------------
           *  Resolve the system path for increased reliability
           * ---------------------------------------------------------------
           */
        // Get the project root directory (4 levels up from vendor/jaysparmar/codeigniter3/src/)
        $project_root = dirname(__DIR__, 4);
        $system_path = __DIR__ . DIRECTORY_SEPARATOR . 'System';
        $application_folder = $project_root . DIRECTORY_SEPARATOR . 'application';
        $view_folder = '';

        if (defined('STDIN')) {
            chdir($project_root);
        }

        if (($_temp = realpath($system_path)) !== false) {
            $system_path = $_temp . DIRECTORY_SEPARATOR;
        } else {
            $system_path = strtr(
                    rtrim($system_path, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                ) . DIRECTORY_SEPARATOR;
        }

        if (!is_dir($system_path)) {
            header('HTTP/1.1 503 Service Unavailable.', true, 503);
            echo 'Your system folder path does not appear to be set correctly.';
            exit(3);
        }

        define('SELF', pathinfo($project_root . DIRECTORY_SEPARATOR . 'index.php', PATHINFO_BASENAME));
        define('BASEPATH', $system_path);
        define('FCPATH', $project_root . DIRECTORY_SEPARATOR);
        define('SYSDIR', basename(BASEPATH));

        if (is_dir($application_folder)) {
            if (($_temp = realpath($application_folder)) !== false) {
                $application_folder = $_temp;
            } else {
                $application_folder = strtr(
                    rtrim($application_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
            }
        } elseif (is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR)) {
            $application_folder = BASEPATH . strtr(
                    trim($application_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
        } else {
            header('HTTP/1.1 503 Service Unavailable.', true, 503);
            echo 'Your application folder path does not appear to be set correctly.';
            exit(3);
        }

        define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);

        if (!isset($view_folder[0]) && is_dir(APPPATH . 'views' . DIRECTORY_SEPARATOR)) {
            $view_folder = APPPATH . 'views';
        } elseif (is_dir($view_folder)) {
            if (($_temp = realpath($view_folder)) !== false) {
                $view_folder = $_temp;
            } else {
                $view_folder = strtr(
                    rtrim($view_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
            }
        } elseif (is_dir(APPPATH . $view_folder . DIRECTORY_SEPARATOR)) {
            $view_folder = APPPATH . strtr(
                    trim($view_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
        } else {
            header('HTTP/1.1 503 Service Unavailable.', true, 503);
            echo 'Your view folder path does not appear to be set correctly.';
            exit(3);
        }

        define('VIEWPATH', $view_folder . DIRECTORY_SEPARATOR);

        /*
         * --------------------------------------------------------------------
         * LOAD THE BOOTSTRAP FILE
         * --------------------------------------------------------------------
         */

        require_once BASEPATH . 'core/CodeIgniter.php';
    }


    /**
     * Initialize CodeIgniter for CLI usage only (without loading the full web application)
     */
    public static function initCLI()
    {
        // Define CI_VERSION constant if not already defined
        if (!defined('CI_VERSION')) {
            define('CI_VERSION', '3.1.13');
        }

        self::loadENV();
        Env::init();

        // Set error reporting for CLI
        error_reporting(-1);
        ini_set('display_errors', 1);

        /*
         * ---------------------------------------------------------------
         *  Resolve the system path for increased reliability
         * ---------------------------------------------------------------
         */
        // Get the project root directory (4 levels up from vendor/jaysparmar/codeigniter3/src/)
        $project_root = dirname(__DIR__, 4);
        $system_path = __DIR__ . DIRECTORY_SEPARATOR . 'System';
        $application_folder = $project_root . DIRECTORY_SEPARATOR . 'application';
        $view_folder = '';

        if (defined('STDIN')) {
            chdir($project_root);
        }

        if (($_temp = realpath($system_path)) !== false) {
            $system_path = $_temp . DIRECTORY_SEPARATOR;
        } else {
            $system_path = strtr(
                    rtrim($system_path, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                ) . DIRECTORY_SEPARATOR;
        }

        if (!is_dir($system_path)) {
            echo 'Your system folder path does not appear to be set correctly.';
            exit(3);
        }

        define('SELF', pathinfo($project_root . DIRECTORY_SEPARATOR . 'index.php', PATHINFO_BASENAME));
        define('BASEPATH', $system_path);
        define('FCPATH', $project_root . DIRECTORY_SEPARATOR);
        define('SYSDIR', basename(BASEPATH));

        if (is_dir($application_folder)) {
            if (($_temp = realpath($application_folder)) !== false) {
                $application_folder = $_temp;
            } else {
                $application_folder = strtr(
                    rtrim($application_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
            }
        } elseif (is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR)) {
            $application_folder = BASEPATH . strtr(
                    trim($application_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
        } else {
            echo 'Your application folder path does not appear to be set correctly.';
            exit(3);
        }

        define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);

        if (!isset($view_folder[0]) && is_dir(APPPATH . 'views' . DIRECTORY_SEPARATOR)) {
            $view_folder = APPPATH . 'views';
        } elseif (is_dir($view_folder)) {
            if (($_temp = realpath($view_folder)) !== false) {
                $view_folder = $_temp;
            } else {
                $view_folder = strtr(
                    rtrim($view_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
            }
        } elseif (is_dir(APPPATH . $view_folder . DIRECTORY_SEPARATOR)) {
            $view_folder = APPPATH . strtr(
                    trim($view_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
        } else {
            echo 'Your view folder path does not appear to be set correctly.';
            exit(3);
        }

        define('VIEWPATH', $view_folder . DIRECTORY_SEPARATOR);

        // Load only essential files for CLI usage
        require_once BASEPATH . 'core/Common.php';
        
        // Load application constants
        if (file_exists(APPPATH . 'config/constants.php')) {
            require_once APPPATH . 'config/constants.php';
        }
    }

    public static function loadConstants(): void
    {

    }

    public static function loadENV(): void
    {

        $baseDir = dirname(__DIR__, 4);

        if (file_exists($baseDir . '/.env')) {
            $dotenv = Dotenv::createImmutable($baseDir);
            $dotenv->safeLoad(); // safeLoad won't throw error if file missing
        } else {
            echo '.env not found at ' . $baseDir . '/.env';
        }
    }

}

