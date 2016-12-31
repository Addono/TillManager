<?
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>A PHP Error was encountered</h4>

<p>Severity: <? echo $severity; ?></p>
<p>Message:  <? echo $message; ?></p>
<p>Filename: <? echo $filepath; ?></p>
<p>Line Number: <? echo $line; ?></p>

<? if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

	<p>Backtrace:</p>
	<? foreach (debug_backtrace() as $error): ?>

		<? if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

			<p style="margin-left:10px">
			File: <? echo $error['file'] ?><br />
			Line: <? echo $error['line'] ?><br />
			Function: <? echo $error['function'] ?>
			</p>

		<? endif ?>

	<? endforeach ?>

<? endif ?>

</div>