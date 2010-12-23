<?php

$project = 'parsh';
$title = 'Parsh';
include('Ops/head.inc');

include('Parsh/www/www.inc');

$tabs = array(
	'manpage.php' => 'Man Page',
);

?>

<body class="yui-skin-sam">

<?php

include('Parsh/www/body_top.inc');
$www->showSubTabs($tabs, '/docs', 'manpage.php');

?>
