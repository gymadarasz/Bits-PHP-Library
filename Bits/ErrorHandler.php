<?php

/*
 * The MIT License
 *
 * Copyright 2015 Gyula Madarasz <gyula.madarasz at gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Bits;

/**
 * an error handler
 *
 * @author Gyula Madarasz <gyula.madarasz at gmail.com>
 */
class ErrorHandler extends Uninstanceable {
	
	private static $oldErrorHandler = false;
	
	private static $initialized = false;
	
	/**
	 *
	 * @var bool show error context when error given
	 */
	public static $showErrorContext = true;
	
	/**
	 *
	 * @var bool show trace information when error given
	 */
	public static $showTrace = true;
	
	/**
	 *
	 * @var bool show br tag at end of lines
	 */
	public static $showHtml = false;
	
	/**
	 *
	 * @var bool save error into log file
	 */
	public static $logErrors = true;
	
	/**
	 *
	 * @var bool show error on output
	 */
	public static $displayErrors = false;
	
	/**
	 * 
	 * @var mixed send error reporting email to the definiated email address or set to false
	 */
	public static $emailErrors = 'gyula.madarasz@gmail.com';
	
	/**
	 *
	 * @var string set error reporting email subject, use %host% marker to add host information into subject
	 */
	public static $emailErrorsSubject = 'Error report on %host%';
	
	/**
	 * call this to initialize an use this error handler
	 * @param int $errorType set error types, default is E_ALL | E_STRICT
	 */
	public static function initialize($errorType = E_ALL | E_STRICT) {
		error_reporting($errorType);
		ini_set('display_errors', 1);
		if(ErrorHandler::$initialized) {
			trigger_error('Error handler already initialized.');
		}
		else {
			ErrorHandler::$oldErrorHandler = set_error_handler(function($errno, $errstr, $errfile = null, $errline = null, array $errcontext = null){
				$output = "Catch an error: #$errno - $errstr in $errfile at $errline" . PHP_EOL;
				if(ErrorHandler::$showErrorContext && $errcontext) {
					$output .= "Error context: " . Debug::getInfo($errcontext, false, false) . PHP_EOL;
				}
				if(ErrorHandler::$showTrace) {
					$output .= "Trace info:" . PHP_EOL . Debug::getTrace();
				}
				if(ErrorHandler::$showHtml) {
					$output = nl2br($output);
				}
				if(ErrorHandler::$logErrors) {
					Logger::write($output, 'ERROR');
				}
				if(ErrorHandler::$displayErrors) {
					echo $output;
				}
				if(ErrorHandler::$emailErrors) {
					if(!mail(ErrorHandler::$emailErrors, str_replace('%host%', "{$_SERVER['HTTP_HOST']} ({$_SERVER['SERVER_NAME']} - {$_SERVER['SERVER_ADDR']})", ErrorHandler::$emailErrorsSubject), $output)) {
						throw new Exception('Error reporting email sending faild.');
					}
				}
				if (ErrorHandler::$oldErrorHandler) {
					// call prevent error handler
					$func = ErrorHandler::$oldErrorHandler;
					$func($errno, $errstr, $errfile, $errline, $errcontext);
				}
			}, $errorType);			
			ErrorHandler::$initialized = true;
		}
	}
	
}

?>