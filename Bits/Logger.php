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
 * a static logger
 *
 * @author Gyula Madarasz <gyula.madarasz at gmail.com>
 */
class Logger extends Uninstanceable {
	
	private static $messages = array();
	
	/**
	 *
	 * @var bool add br tag to end of line and other html formating
	 */
	private static $html = true;
	
	private static $filename = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bits.log';
	
	/**
	 * 
	 * @param string $msg add a message to log
	 * @param string $type set message type for filtering the log
	 * @param string $dateFormat date format for logging, date function will use it
	 */
	public static function message($msg, $type = 'LOG', $dateFormat = "Y-m-d H:i:s") {
		$_bO = '';
		$_bC = '';
		if(Logger::$html) {
			$_bO = '<b>';
			$_bC = '</b>';
		}
		Logger::$messages[] = array($type, (Logger::$html?'<pre>':'') . "$_bO* Log on " . date($dateFormat) . "> [$type]$_bC $msg" . (Logger::$html?'<br></pre>':PHP_EOL));
	}
	
	/**
	 * 
	 * @param string $filter filter log messages or false
	 * @return string log messages by string
	 */
	public static function getLog($filter = false) {
		$output = '';
		foreach(Logger::$messages as $msg) {
			if(!$filter || $filter==$msg[0]) {
				$output .= $msg[1];
			}
		}
		return $output;
	}
	
	/**
	 * 
	 * @param string $filter filter log messages or false
	 * @throws Exception throw an exception on file write error
	 */
	public static function save($filter = false) {		
		if($log = Logger::getLog($filter)) {
			if(!file_put_contents(Logger::$filename, $log, FILE_APPEND)) {
				throw new Exception("Logger error on save to $filename file.");
			}
		}
	}
	
	/**
	 * clear log array
	 */
	public static function clear() {
		Logger::$messages = array();
	}
	
	/**
	 * 
	 * @param string $msg log message
	 * @param string $type set message type for filtering the log
	 * @param type $writeAll switch off type filtering on log write into log file if set true
	 * @param type $dateFormat date format for logging, date function will use it
	 */
	public static function write($msg, $type = false, $writeAll = true, $dateFormat = "Y-m-d H:i:s") {
		Logger::message($msg, $type, $dateFormat, Logger::$html);
		Logger::save($writeAll?false:$type, Logger::$filename);
		Logger::clear($type);
	}
	
}

?>