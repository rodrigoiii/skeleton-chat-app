<?php

namespace Core;

use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Http\UploadedFile;

abstract class BaseRequest
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Redirect on itself if the provided data are invalid.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        method_exists($this, "messages") ?
            $this->validate($request, $this->rules(), $this->messages()) :
            $this->validate($request, $this->rules());

        if ($this->failed())
        {
            $referer_header = $request->getHeader('HTTP_REFERER');
            $referer = array_shift($referer_header);

            header("Location: {$referer}");
            die;
        }
        else
        {
            // remove old input after the request successfully process.
            if(isset($_SESSION['old_input']))
            {
                unset($_SESSION['old_input']);
            }
        }
    }

    /**
     * Return Request property as parameter provided.
     *
     * @param  string
     * @return mixed
     */
    public function __get($param)
    {
        return $this->request->{$param};
    }

    /**
     * Return Request method as parameter with arguments provided.
     *
     * @param  string $name
     * @param  string|array $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array([$this->request, $name], $args);
    }

    /**
     * Put all fails in session
     * @param  Psr\Http\Message\RequestInterface $request
     * @param  array $rules
     * @return BaseRequest
     */
    public function validate($request, $rules, $messages=[])
    {
        $errors = [];
        $files = $request->getUploadedFiles();

        foreach ($rules as $field => $rule)
        {
            try
            {
                // check if the field is for file
                if (isset($files[$field]))
                {
                    if (is_array($files[$field])) // multiple file
                    {
                        $files = $files[$field];
                        foreach ($files as $file_row)
                        {
                            if ($file_row instanceof UploadedFile)
                            {
                                $rule->setName(str_title(ucfirst($field)))->assert(new \SplFileInfo($file_row->file));
                            }
                        }
                    }
                    else // single file
                    {
                        if ($files[$field] instanceof UploadedFile)
                        {
                            $rule->setName(str_title(ucfirst($field)))->assert(new \SplFileInfo($files[$field]->file));
                        }
                    }
                }
                else
                {
                    $rule->setName(str_title(ucfirst($field)))->assert($request->getParam($field));
                }
            } catch (NestedValidationException $e) {
                $e->findMessages($messages);
                $errors[$field] = $e->getMessages();
            }
        }

        $_SESSION['errors'] = $errors;
        return __CLASS__;
    }

    /**
     * Check if the validation is fail
     * @return boolean
     */
    public function failed()
    {
        return !empty($_SESSION['errors']);
    }

    /**
     * To be define on sub class.
     *
     * @return void
     */
    abstract public function rules();
}
