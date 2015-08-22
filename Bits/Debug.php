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
 * for easier debugging
 *
 * @author Gyula Madarasz <gyula.madarasz at gmail.com>
 */
class Debug {
	
	/**
	 *
	 * @var bool add br tag to end of line and other html formating 
	 */
	public static $html = true;
	
	/**
	 * 
	 * @param int $start start callstack deep over
	 * @return string printed callstack
	 */
	public static function getTrace($start = 1) {
		$_bO = '';
		$_bC = '';
		if(Debug::$html) {
			$_bO = '<b>';
			$_bC = '</b>';
		}
		$separator = Debug::$html ? "<br>" : PHP_EOL;
		$argMaxLen = 40;
		$outputs = array();
		$backtrace = debug_backtrace();
		$deep = 0;
		foreach($backtrace as $trace) {
			if($deep >= $start) {
				foreach($trace['args'] as &$arg) {
					$argPref = '';
					$argPost = '';
					if(is_object($arg)) {
						$argPref = get_class($arg) . ":";
						$argPost = "";
						$arg = json_encode(get_object_vars($arg));
					}
					else if(is_array($arg)) {
						$argPref = "Array:";
						$argPost = "";
						$arg = json_encode($arg);
					}
					else if(is_string($arg)) {					
						$argPref = "\"";
						$argPost = "\"";
					}
					if(strlen($arg) > $argMaxLen) {
						$arg = substr($arg, 0, $argMaxLen-2) . '..';
					}
					$arg = $argPref . $arg . $argPost;
				}
				$outputs[] = "Call from $_bO" . (isset($trace['file']) ? "{$trace['file']} ($_bO{$trace['line']}$_bC)" : '???') . "$_bC at $_bO" . 
					(isset($trace['class']) ? $trace['class'] : '') . (isset($trace['type']) ? $trace['type'] : '') . $trace['function'] . "$_bC(" .
					implode(', ', $trace['args']) . ")";
			}
			$deep++;
		}
		$output = Debug::getInfo(implode($separator, $outputs), Debug::$html, false);
		return $output;
	}
	
	/**
	 * 
	 * @param mixed $data show details of data
	 * @param bool $trace show trace info
	 * @param bool $show if true echo output otherwise return output string
	 * @return string
	 */
	public static function getInfo($data = null, $trace = true, $show = false) {
		$output = ''; //$trace ? Debug::getTrace(Debug::$html) : '';
		if(empty($data)) {
			$output .= 'EMPTY';
		}
		else if(is_null($data)) {
			$output .= 'NULL';
		}
		else if($data === false) {
			$output .= 'FALSE';
		}
		else if($data === '') {
			$output .= 'STRING(0)';
		}
		else if($data && is_string($data)){
			$output .= $data;
		}
		else {
			$output .= json_encode($data, true);
		}
//		if(Debug::$html) {
//			$output = "<pre>" . nl2br($output) . "</pre>";
//		}
		if($show) {
			echo $output;
		}
		else {
			return $output;
		}
	}

}

?>