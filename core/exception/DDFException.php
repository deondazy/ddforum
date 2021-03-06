<?php

namespace DDForum\Core\Exception;

use DDForum\Core\Site;

class DDFException extends \Exception
{
    protected $message = '';
    protected $isError = false;

    public function __construct($message = 'DDForum Error', $code = 0, $isError = false)
    {
        parent::__construct($message, $code);

        $this->isError = $isError;
    }

    public static function handle()
    {
        set_exception_handler(array(__CLASS__, 'exceptionHandler'));
        set_error_handler(array(__CLASS__, 'errorHandler'));
    }

    public static function exceptionHandler($exception)
    {
        if (isset($exception->isError) && $exception->isError) {
            return;
        }

        print Site::info($exception->getMessage(), true);
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if (!($errno & error_reporting())) {
            return;
        }

        // Don't be fooled, we can't actually handle most of these.
        $error_names = array(
        // @locale A fatal PHP error at runtime. Code execution is stopped
        E_ERROR => 'Error',
        // @locale A non-fatal PHP error at runtime. Code execution is not stopped
        E_WARNING => 'Warning',
        // @locale A fatal PHP error generated while parsing the PHP
        E_PARSE => 'Parse Error',
        // @locale PHP encountered something at runtime that could be an error or intended
        E_NOTICE => 'Notice',
        // @locale A fatal PHP error during PHP startup. Code execution is stopped.
        E_CORE_ERROR => 'Core Error',
        // @locale A non-fatal PHP during PHP startup. Code execution is not stopped.
        E_CORE_WARNING => 'Core Warning',
        // @locale A fatal PHP error at runtime. Code execution is stopped.
        E_COMPILE_ERROR => 'Compile Error',
        // @locale A non-fatal PHP error at runtime. Code execution is not stopped.
        E_COMPILE_WARNING => 'Compile Warning',
        // @locale A fatal error generated by Habari or an addon. Code execution is stopped.
        E_USER_ERROR => 'User Error',
        // @locale A non-fatal error generated by Habari or an addon. Code execution is not stopped.
        E_USER_WARNING => 'User Warning',
        // @locale PHP encountered something generated by Habari or an addon at runtime that could be an error or intended
        E_USER_NOTICE => 'User Notice',
        // @locale A suggestion from PHP that the code may need updated for interoperability or forward compatibility
        E_STRICT => 'Strict Notice',
        // @locale A fatal PHP error at runtime. An error handler may be able to work around it. If not, code execution stops.
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        );

        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            // @locale A notification from PHP that the code is outdated and may not work in the future
            $error_names[E_DEPRECATED] = 'Deprecated violation';
            // @locale A notification that the code is outdated and Habari may not work with it in the future
            $error_names[E_USER_DEPRECATED] = 'User deprecated violation';
        }

        if (ini_get('display_errors')) {
            printf(
                "<pre class=\"error\">\n<b>%s:</b> %s in %s line %s\n</pre>",
                $error_names[$errno],
                $errstr,
                $errfile,
                $errline
            );
        }
        return true;
    }
}
