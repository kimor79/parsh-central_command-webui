<?php

$project = 'parsh';
$title = 'Parsh';
include('Ops/head.inc');

include('Parsh/www/www.inc');

$tabs = array(
	'search.php' => 'Search',
	'details.php' => 'Details',
);

?>

<body class="yui-skin-sam">

<?php

include('Parsh/www/body_top.inc');
$www->showSubTabs($tabs, '/nodes', 'search.php');

?>
