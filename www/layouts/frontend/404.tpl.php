<pre>

	<?php
	if (!headers_sent()) {
		//header('Content-type: text/plain');
	}

	echo memory_get_peak_usage() . "\n";

	print_r($d);

	print_r(debug_backtrace());
	?>
</pre>