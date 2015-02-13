<?php
require_once "/home/vojta/public_html/Nette-2.2.6/Nette/loader.php";

\Tracy\Debugger::enable();

require_once "vendor/autoload.php";
require_once "lib.php";


$client = new \Packagist\Api\Client();
$package = "nette/nette";
$pkg = $client->get($package);

$vers = [];
foreach($pkg->getVersions() as $verName => $version) {
	$vers[$verName] = $version->getVersionNormalized();
}
$versionKey = getLatest($vers, TRUE);
echo "Package $package, version $versionKey" . PHP_EOL;

/** @var $version \Packagist\Api\Result\Package\Version */
$version = $pkg->getVersions()[$versionKey];


$chart = getRequirements($version, $package);



// var_dump($chart);

?>

<!doctype html>
<html>
<head>
	<title>Composer visualization</title>

	<script type="text/javascript" src="bower_components/vis/dist/vis.js"></script>
	<link href="bower_components/vis/dist/vis.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div id="mynetwork"></div>

<script type="text/javascript">
	// create an array with nodes
	var nodes = [
		<?php
			$show = '';
			foreach($chart->nodes as $package => $version) {
				$show .= '{id: "' . $package . '", label: "' . $package . '"},';
			}
			echo $show;
		?>
	];

	// create an array with edges
	var edges = [
		<?php
			$show = '';
			foreach($chart->edges as $id => $edge) {
				$show .= '{id: ' . $id . ', from: "' . $edge->from. '", to: "' . $edge->to . '"},';
			}
			echo $show;
		?>
	];

	// create a network
	var container = document.getElementById('mynetwork');
	var data= {
		nodes: nodes,
		edges: edges,
	};
	var options = {
		width: '400px',
		height: '400px'
	};
	var network = new vis.Network(container, data, options);
</script>

</body>
</html>