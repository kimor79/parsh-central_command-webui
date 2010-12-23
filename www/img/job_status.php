<?php

require('phplot/phplot.php');

include('Parsh/includes/ro.inc');

$h_success = 0;
$h_non = 0;
$h_error = 0;
$h_still = 0;
$h_not = 0;

if($parsh->validateJobID($_GET['job_id'])) {
	$query = 'SELECT `exit_status`, `start_time`, `finish_time` FROM `nodes`';
	$query .= sprintf(" WHERE `job_id`='%d'", mysql_real_escape_string($_GET['job_id']));
	$result = do_mysql_query($query);

	if($result[0] !== true) {
		echo $result[1];
		exit(0);
	}

	while($line = mysql_fetch_assoc($result[1])) {
		if($line['exit_status'] == 0) {
			if($line['start_time'] > 0) {
				if($line['finish_time'] > 0) {
					$h_success++;
				} else {
					$h_still++;
				}
			} else {
				$h_not++;
			}
		} elseif($line['exit_status'] > 0) {
			$h_non++;
		} elseif($line['exit_status'] < 0) {
			$h_error++;
		}
	}
}

$data = array(
	array('success', $h_success),
	array('non-zero', $h_non),
	array('error', $h_error),
	array('still running', $h_still),
	array('not started', $h_not),
);

$plot = new PHPlot(500, 200);
$plot->SetDataType('text-data-single');
$plot->SetDataValues($data);
$plot->SetPlotType('pie');

foreach ($data as $row) {
	$plot->setLegend(implode(': ', $row));
}

$plot->SetDataColors(array('DarkGreen', 'red', 'purple', 'blue', 'gray'));
$plot->SetShading(0);
$plot->SetLabelScalePosition(0.2);
$plot->SetBackgroundColor('#E7E7E7');

$plot->DrawGraph();
