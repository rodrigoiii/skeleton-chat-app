<?php

namespace Core;

use Core\ErrorHandlers\ErrorHandler;
use Core\ErrorHandlers\NotAllowedHandler;
use Core\ErrorHandlers\NotFoundHandler;
use Core\ErrorHandlers\PhpErrorHandler;
use DI\Bridge\Slim\App as SlimApp;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Dotenv\Environment\Adapter\PutenvAdapter;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Exception\InvalidPathException;
use Illuminate\Database\Capsule\Manager;
use Psr\Container\ContainerInterface;
use Slim\Csrf\Guard;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

class App extends SlimApp
{
    /**
     * @var array
     */
    protected $definitions;

    /**
     * @var array
     */
    protected $custom_definitions;

    public function __construct(array $custom_definitions = [])
    {
        $this->definitions = [
            'settings.displayErrorDetails' => config("app.debug"),
            'settings.database' => config("database"),
            'settings.tracy' => config("debugbar.settings")
        ];

        $this->custom_definitions = $custom_definitions;

        parent::__construct();
    }

    protected function configureContainer(ContainerBuilder $builder)
    {
        $this->pushErrorHandlersDefinition($this->definitions);
        $this->pushViewDefinition($this->definitions);
        $this->pushControllersDefinition($this->definitions);
        $this->pushCoreMiddlewaresDefinition($this->definitions);
        $this->pushMiddlewaresDefinition($this->definitions);
        $this->pushCsrfDefinition($this->definitions);
        $this->pushTwigProfile($this->definitions);
        $this->pushFlashDefinition($this->definitions);
        $this->pushRequestsDefinition($this->definitions);

        $builder->addDefinitions(array_merge($this->definitions, $this->custom_definitions));
    }

    private function pushErrorHandlersDefinition(array &$definitions)
    {
        # override not found handler
        $definitions['notFoundHandler'] = function(Twig $view)
        {
            return new NotFoundHandler($view);
        };

        # override not allowed handler
        $definitions['notAllowedHandler'] = function(Twig $view)
        {
            return new NotAllowedHandler($view);
        };

        # override error handler
        $definitions['errorHandler'] = function(Twig $view)
        {
            return new ErrorHandler($view);
        };

        # override php error handler
        $definitions['phpErrorHandler'] = function(Twig $view)
        {
            return new PhpErrorHandler($view);
        };

        if (is_dev())
        {
            unset($definitions['errorHandler']);
            unset($definitions['phpErrorHandler']);
        }
    }

    private function pushViewDefinition(array &$definitions)
    {
        # twig view
        $definitions['view'] = function(ContainerInterface $c)
        {
            $use_dist = filter_var(config("app.use_dist"), FILTER_VALIDATE_BOOLEAN);

            $path = resources_path($use_dist ? "dist-views" : "views");
            $config = config("view");
            $settings = array_key_exists('settings', $config) ? $config['settings'] : [];

            $view = new Twig($path, $settings);

            $view->addExtension(new TwigExtension($c->get('router'), $c->get('request')->getUri()));

            if (array_key_exists('functions', $config))
            {
                $functions = $config['functions'];

                foreach ($functions as $function) {
                    $view->getEnvironment()->addFunction(new \Twig_SimpleFunction($function, $function));
                }
            }

            $errors = null;
            if (isset($_SESSION['errors']))
            {
                $session_value = $_SESSION['errors'];
                unset($_SESSION['errors']);

                $errors = $session_value;
            }

            $view->getEnvironment()->addGlobal('errors', $errors);

            # Make 'flash' global
            $view->getEnvironment()->addGlobal('flash', $c->get('flash'));

            return $view;
        };

        # Service injection
        $definitions[Twig::class] = function(ContainerInterface $c)
        {
            return $c->get('view');
        };
    }

    private function pushControllersDefinition(array &$definitions)
    {
        $controllers_directory = app_path("src/Controllers");

        $controller_files = get_files($controllers_directory);

        if (!empty($controller_files))
        {
            $controllers = array_map(function($controller) use($controllers_directory) {
                $new_controller = str_replace("{$controllers_directory}/", "", $controller);
                return basename(str_replace("/", "\\", $new_controller), ".php");
            }, $controller_files);

            $errors = [];

            foreach ($controllers as $controller)
            {
                $controller_definition = $controller;
                $controller_definition_value = get_app_namespace() . "Controllers\\{$controller}";

                if (!array_key_exists($controller_definition, $definitions))
                {
                    $definitions[$controller_definition] = function(ContainerInterface $c) use ($controller_definition_value)
                    {
                        return new $controller_definition_value($c);
                    };
                }
                else
                {
                    $errors[] = "{$controller} is already exist inside of definitions.";
                }
            }

            if (!empty($errors))
            {
                exit("Error: Please fix the ff. before run the application: <li>" . implode("</li><li>", $errors));
            }
        }
    }

    private function pushCoreMiddlewaresDefinition(array &$definitions)
    {
        $middlewares_directory = core_path("classes/Middlewares");

        $middleware_files = get_files($middlewares_directory);

        if (!empty($middleware_files))
        {
            $middlewares = array_map(function($middleware) use($middlewares_directory) {
                $new_middleware = str_replace("{$middlewares_directory}/", "", $middleware);
                return basename(str_replace("/", "\\", $new_middleware), ".php");
            }, $middleware_files);

            $errors = [];

            foreach ($middlewares as $middleware)
            {
                $middleware_definition = "Core\\{$middleware}";
                $middleware_definition_value = "Core\\Middlewares\\{$middleware}";

                if (!array_key_exists($middleware_definition, $definitions))
                {
                    $definitions[$middleware_definition] = function(ContainerInterface $c) use ($middleware_definition_value)
                    {
                        return new $middleware_definition_value($c);
                    };
                }
                else
                {
                    $errors[] = "{$middleware} is already exist inside of definitions.";
                }
            }

            if (!empty($errors))
            {
                exit("Error: Please fix the ff. before run the application: <li>" . implode("</li><li>", $errors));
            }
        }
    }

    private function pushMiddlewaresDefinition(array &$definitions)
    {
        $middlewares_directory = app_path("src/Middlewares");

        $middleware_files = get_files($middlewares_directory);

        if (!empty($middleware_files))
        {
            $middlewares = array_map(function($middleware) use($middlewares_directory) {
                $new_middleware = str_replace("{$middlewares_directory}/", "", $middleware);
                return basename(str_replace("/", "\\", $new_middleware), ".php");
            }, $middleware_files);

            $errors = [];

            foreach ($middlewares as $middleware)
            {
                $middleware_definition = $middleware;
                $middleware_definition_value = get_app_namespace() . "Middlewares\\{$middleware}";

                if (!array_key_exists($middleware_definition, $definitions))
                {
                    $definitions[$middleware_definition] = function(ContainerInterface $c) use ($middleware_definition_value)
                    {
                        return new $middleware_definition_value($c);
                    };
                }
                else
                {
                    $errors[] = "{$middleware} is already exist inside of definitions.";
                }
            }

            if (!empty($errors))
            {
                exit("Error: Please fix the ff. before run the application: <li>" . implode("</li><li>", $errors));
            }
        }
    }

    private function pushCsrfDefinition(array &$definitions)
    {
        # slim/csrf
        $definitions['csrf'] = function(Twig $view)
        {
            $guard = new Guard;
            $guard->setFailureCallable(function($request, $response, $next) use($view) {
                if (is_prod())
                {
                    return $view->render(
                        $response->withStatus(403)->withHeader('Content-Type', "text/html"),
                        config('twig-view.error_pages.403')
                    );
                }

                return $response->withStatus(403)
                                ->withHeader('Content-Type', "text/html")
                                ->write("Failed CSRF check!");
            });
            return $guard;
        };
    }

    private function pushTwigProfile(array &$definitions)
    {
        # tracy debug bar need this
        $definitions['twig_profile'] = function()
        {
            return new \Twig_Profiler_Profile;
        };
    }

    private function pushFlashDefinition(array &$definitions)
    {
        $definitions['flash'] = function()
        {
            return new Messages;
        };
    }

    private function pushRequestsDefinition(array &$definitions)
    {
        $requests_directory = app_path("src/Requests");

        $request_files = get_files($requests_directory);

        if (!empty($request_files))
        {
            $requests = array_map(function($request) use($requests_directory) {
                $new_request = str_replace("{$requests_directory}/", "", $request);
                return basename(str_replace("/", "\\", $new_request), ".php");
            }, $request_files);

            $errors = [];

            foreach ($requests as $request)
            {
                $request_definition = get_app_namespace() . "Requests\\{$request}";

                if (!array_key_exists($request_definition, $definitions))
                {
                    $definitions[$request_definition] = function(ContainerInterface $c) use($request_definition)
                    {
                        return new $request_definition($c->get('request'));
                    };
                }
                else
                {
                    $errors[] = "{$request} is already exist inside of definitions.";
                }
            }

            if (!empty($errors))
            {
                exit("Error: Please fix the ff. before run the application: <li>" . implode("</li><li>", $errors));
            }
        }
    }

    public function loadDatabaseConnection($is_query_log_enabled = false)
    {
        $capsule = new Manager;
        $capsule->addConnection($this->definitions['settings.database']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $connection = $capsule::connection();

        if ($is_query_log_enabled)
        {
            $connection->enableQueryLog();
        }
    }

    public static function createEnvironment()
    {
        try {
            $dotEnvFactory = new DotenvFactory([
                new PutenvAdapter
            ]);

            return Dotenv::create(base_path(), null, $dotEnvFactory);
        } catch (InvalidPathException $e) {
            die($e->getMessage());
        }
    }
}
