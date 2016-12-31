<? defined('BASEPATH') OR exit('No direct script access allowed'); ?>

A PHP Error was encountered

Severity:    <? echo $severity, "\n"; ?>
Message:     <? echo $message, "\n"; ?>
Filename:    <? echo $filepath, "\n"; ?>
Line Number: <? echo $line; ?>

<? if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

Backtrace:
<?	foreach (debug_backtrace() as $error): ?>
<?		if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
	File: <? echo $error['file'], "\n"; ?>
	Line: <? echo $error['line'], "\n"; ?>
	Function: <? echo $error['function'], "\n\n"; ?>
<?		endif ?>
<?	endforeach ?>

<? endif ?>
