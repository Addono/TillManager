<? defined('BASEPATH') OR exit('No direct script access allowed'); ?>

An uncaught Exception was encountered

Type:        <? echo get_class($exception), "\n"; ?>
Message:     <? echo $message, "\n"; ?>
Filename:    <? echo $exception->getFile(), "\n"; ?>
Line Number: <? echo $exception->getLine(); ?>

<? if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

Backtrace:
<?	foreach ($exception->getTrace() as $error): ?>
<?		if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
	File: <? echo $error['file'], "\n"; ?>
	Line: <? echo $error['line'], "\n"; ?>
	Function: <? echo $error['function'], "\n\n"; ?>
<?		endif ?>
<?	endforeach ?>

<? endif ?>
