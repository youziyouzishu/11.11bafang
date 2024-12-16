<?php

namespace app\common\log;

use think\contract\LogHandlerInterface;
use think\facade\Request;

class CustomLogger implements LogHandlerInterface
{

	public function save(array $log): bool
	{
		$destination = $this->getLogFile();
		$info = [];

		foreach ($log as $level => $messages) {
			foreach ($messages as $message) {
				$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
				$file = isset($backtrace[2]['file']) ? $backtrace[2]['file'] : 'unknown';
				$line = isset($backtrace[2]['line']) ? $backtrace[2]['line'] : 0;

				$info[] = sprintf(
					'[%s][%s] %s in %s:%d',
					date($this->config['time_format']),
					strtoupper($level),
					$message,
					$file,
					$line
				);
			}
		}

		return error_log(implode(PHP_EOL, $info) . PHP_EOL, 3, $destination);
	}
}
