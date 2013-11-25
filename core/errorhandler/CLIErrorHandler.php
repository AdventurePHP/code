<?php
namespace APF\core\errorhandler;

/**
 * @package APF\core\errorhandler
 * @class CLIErrorHandler
 *
 * Implements a cli error handler. Logs errors to a logfile and displays
 * output to cli.
 *
 * @author Tobias Lückel
 * @version
 * Version 0.1, 25.11.2013<br />
 */
class CLIErrorHandler extends DefaultErrorHandler {
    /**
     * @public
     *
     * Implements the error handling function, that is called by the APF error handling function.
     *
     * @param string $errorNumber error number
     * @param string $errorMessage error message
     * @param string $errorFile error file
     * @param string $errorLine error line
     * @param array  $errorContext error context
     *
     * @author Tobias Lückel[Megger]
     * @version
     * Version 0.1, 25.11.2013<br />
     */
    public function handleError($errorNumber, $errorMessage, $errorFile, $errorLine, array $errorContext)
    {
        // fill attributes
        $this->errorNumber  = $errorNumber;
        $this->errorMessage = $errorMessage;
        $this->errorFile    = $errorFile;
        $this->errorLine    = $errorLine;

        // log error
        $this->logError();

        // build nice error page
        echo $this->buildErrorOutput();
    }

    /**
     * @protected
     *
     * Creates the error output.
     *
     * @return string The APF error output.
     *
     * @author Tobias Lückel[Megger]
     * @version
     * Version 0.1, 25.11.2013<br />
     */
    protected function buildErrorOutput() {
        $output = PHP_EOL;
        $output .= '[' . $this->generateErrorID() . ']';
        $output .= '[' . $this->errorNumber . ']';
        $output .= ' ' . $this->errorMessage . PHP_EOL;
        $output .= "\t" . $this->errorFile . ':' . $this->errorLine . PHP_EOL;
        $output .= 'Stacktrace:' . PHP_EOL;

        $stacktrace = array_reverse(debug_backtrace());
        foreach ($stacktrace as $item) {
            // don't display any further messages, because these belong to the error manager
            if (isset($item['function']) && preg_match('/handleError/i', $item['function'])) {
                break;
            }
            $output .= "\t";
            if (isset($item['class'])) {
                $output .= $item['class'];
            }
            if (isset($item['type'])) {
                $output .= $item['type'];
            }
            if (isset($item['function'])) {
                $output .= $item['function'] . '()';
            }
            $output .= PHP_EOL;
            $output .= "\t\t";
            if (isset($item['file'])) {
                $output .= $item['file'];
            }
            if (isset($item['line'])) {
                $output .= ':' . $item['line'];
            }
            $output .= PHP_EOL;
        }

        $output .= PHP_EOL;

        return $output;
    }
}