<?php
namespace APF\core\exceptionhandler;

/**
 * @package APF\core\exceptionhandler
 * @class CLIExceptionHandler
 *
 * Implements a cli exception handler for uncaught exceptions.
 *
 * @author Tobias Lückel
 * @version
 * Version 0.1, 25.11.2013<br />
 */
class CLIExceptionHandler extends DefaultExceptionHandler {
    /**
     * @public
     *
     * Implements the exception handling function, that is called by the APF exception handling
     * function.
     *
     * @param \Exception $exception the thrown exception.
     *
     * @author Tobias Lückel
     * @version
     * Version 0.1, 25.11.2013<br />
     */
    public function handleException(\Exception $exception)
    {

        // fill attributes
        $this->exceptionNumber  = $exception->getCode();
        $this->exceptionMessage = $exception->getMessage();
        $this->exceptionFile    = $exception->getFile();
        $this->exceptionLine    = $exception->getLine();
        $this->exceptionTrace   = $exception->getTrace();
        $this->exceptionType    = get_class($exception);

        // log exception
        $this->logException();

        // build nice exception page
        echo $this->buildExceptionOutput();
    }

    /**
     * @protected
     *
     * Creates the exception output.
     *
     * @return string the exception output.
     *
     * @author Tobias Lückel[Megger]
     * @version
     * Version 0.1, 25.11.2013<br />
     */
    protected function buildExceptionOutput() {
        $output = PHP_EOL;
        $output .= '[' . $this->generateExceptionID() . ']';
        $output .= '[' . $this->exceptionNumber . ']';
        $output .= ' ' . $this->exceptionMessage . PHP_EOL;
        $output .= "\t" . $this->exceptionFile . ':' . $this->exceptionLine . PHP_EOL;
        $output .= 'Stacktrace:' . PHP_EOL;

        $stacktrace = array_reverse($this->exceptionTrace);
        foreach ($stacktrace as $item) {
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
            if (isset($item['file'])) {
                $output .= ':' . $item['line'];
            }
            $output .= PHP_EOL;
        }

        $output .= PHP_EOL;

        return $output;
    }
}