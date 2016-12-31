<?
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>An uncaught Exception was encountered</h4>

<p>Type: <? echo get_class($exception); ?></p>
<p>Message: <? echo $message; ?></p>
<p>Filename: <? echo $exception->getFile(); ?></p>
<p>Line Number: <? echo $exception->getLine(); ?></p>

<? if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

	<p>Backtrace:</p>
	<? foreach ($exception->getTrace() as $error): ?>

		<? if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

			<p style="margin-left:10px">
			File: <? echo $error['file']; ?><br />
			Line: <? echo $error['line']; ?><br />
			Function: <? echo $error['function']; ?>
			</p>
		<? endif ?>

	<? endforeach ?>

<? endif ?>

</div>