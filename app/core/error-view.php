<?php

namespace core;


for($i=0;$i<count($c['errors']);$i++) {
	echo "
	<h3>".$c['errors'][$i]['message']."</h3>
	<ul>
		<li>File: ".$c['errors'][$i]['file']."</li>
		<li>Class: ".$c['errors'][$i]['class']."</li>
		<li>Function: ".$c['errors'][$i]['function']."</li>
		<li>Line: ".$c['errors'][$i]['line']."</li>
		<li>HTTP: ".$c['errors'][$i]['HTTP']."</li>
		
	</ul>
	";
}

for($i=0;$i<count($c['Trace']);$i++) {
	echo "
	<h3>".$c['Trace'][$i]['class']."::".$c['Trace'][$i]['function']."</h3>
	<ul>
		<li>File: ".$c['Trace'][$i]['file']."</li>
		<li>Line: ".$c['Trace'][$i]['line']."</li>
		<li>Type: ".$c['Trace'][$i]['type']."</li>
	";
	if(count($c['Trace'][$i]['args']) > 0) {
		echo "
		<li>
			<ul>
			";
		for($j=0;$j<count($c['Trace'][$i]['args']);$j++) {
			echo "
				<li>".$c['Trace'][$i]['args'][$j]."</li>";
		}
		
		echo "</ul>
		</li>";
	}
	echo "
	</ul>
	";
}
echo "<h3>Initialisation and Progression</h3>
<ul>
";

for($i=0;$i<count($c['RequestStack']);$i++) {
	echo "
	<li>".$c['RequestStack']['class'][$i]."
		<ul>
			<li>".($i == 0 ? "0" : ($c['RequestStack']['time'][$i]-$c['RequestStack']['time'][$i-1]))."</li>
			<li>".($i == 0 ? "0" : ($c['RequestStack']['memory'][$i]-$c['RequestStack']['memory'][$i-1]))." (".$c['RequestStack']['memory'][$i].")</li>
		</ul>
	</li>";
}
echo "</ul>";

function RecurseArray($arr) {
	if(is_array($arr)) {
		echo "<ul>\n";
		foreach($arr as $key => $val) {
			echo "<li>".$key." = </li>";
			RecurseArray($val);
		}
		echo "</ul>\n";
	} else {
		echo "<li>".$arr."</li>";
	}
}

RecurseArray($c['Parameters']);

?>