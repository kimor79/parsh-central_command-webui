<?php

include_once('Parsh/includes/ro.inc');
include_once('Parsh/www/www.inc');

$node_names = array(
	'host',
	'hostname',
	'name',
	'node',
	'node_name',
	'nodename',
);

foreach($node_names as $n_name) {
	if(!isset($_GET[$n_name])) {
		continue;
	}

	$r_details = $parsh->getNodeDetailsByNode(array('node' => $_GET[$n_name]));
	if(count($r_details) == 1) {
		$details = reset($r_details);

		if($_GET[$n_name] != $details['node']) {
		// non-fqdn was given, redirect to fqdn
			$www->giveRedirect('/nodes/details.php?node=' . urlencode($details['node']));
			exit(0);
		}
	} else {
		$www->giveRedirect('/nodes/search.php?node=' . urlencode($_GET[$n_name]));
		exit(0);
	}
}

if(empty($details)) {
        $www->giveRedirect('/nodes/search.php');
        exit(0);
}

include('top.inc');

?>

<br>

<table class="table_box" cellpadding="3" cellspacing="5">
 <tr><td>

<table cellpadding="10">
 <tr>
  <td><b>Node:</b> <?php echo $details['node']; ?></td>
 </tr><tr>
  <td><b>Jobs:</b> <?php echo $details['total_jobs']; ?></td>
 </tr>
</table>

 </td><td>
<img src="/img/node_status.php?node=<?php echo $details['node']; ?>">
 </td></tr>
</table>

<br>

<div id="divSearchNodeJobs">
 <div class="hd"></div>
 <div class="bd">
<form name="formSearchNodeJobs" method="GET" action="">
<table>
 <tr><td><label for="job_id">Job ID:<label></td><td><input type="text" name="job_id" size="5" value="<?php echo $_GET['job_id']; ?>"></td></tr>
 <tr><td><label for="user">User:<label></td><td><input type="text" name="user" size="30" value="<?php echo $_GET['user']; ?>"></td></tr>
 <tr><td><label for="command">Command:<label></td><td><input type="text" name="command" size="30" value="<?php echo $_GET['command']; ?>"></td></tr>
 <tr><td><label for="files">Files:<label></td><td><input type="text" name="files" size="30" value="<?php echo $_GET['files']; ?>"></td></tr>
 <tr><td><label for="run_from_node">Ran From:<label></td><td><input type="text" name="run_from_node" size="30" value="<?php echo $_GET['run_from_node']; ?>"></td></tr>
 <tr><td><label for="parallel">At Once:<label></td><td><input type="text" name="parallel" size="5" value="<?php echo $_GET['parallel']; ?>"></td></tr>
 <tr><td><label for="argv">Args:<label></td><td><input type="text" name="argv" size="30" value="<?php echo $_GET['argv']; ?>"></td></tr>
</table>
</form>
 </div>
 <div class="ft"></div>
</div>

<br><div></div><br>

<div id="divPanelNodeTip"></div>
<div id="divListJobs"></div>

</body>
</html>
<script type="text/javascript">
YAHOO.util.Event.addListener(window, "load", function() {
	var handleCancel = function() {
		this.form.reset();
	};

	var handleSubmit = function() {
		var searchRequest = '';

		if(myDialog.getData().job_id != '') {
			searchRequest += '&job_id=' + encodeURIComponent(myDialog.getData().job_id);
		}

		if(myDialog.getData().user != '') {
			searchRequest += '&user=' + encodeURIComponent(myDialog.getData().user);
		}

		if(myDialog.getData().run_from_node != '') {
			searchRequest += '&run_from_node=' + encodeURIComponent(myDialog.getData().run_from_node);
		}

		if(myDialog.getData().command != '') {
			searchRequest += '&command=' + encodeURIComponent(myDialog.getData().command);
		}

		if(myDialog.getData().files != '') {
			searchRequest += '&files=' + encodeURIComponent(myDialog.getData().files);
		}

		if(myDialog.getData().parallel != '') {
			searchRequest += '&parallel=' + encodeURIComponent(myDialog.getData().parallel);
		}

		if(myDialog.getData().argv != '') {
			searchRequest += '&argv=' + encodeURIComponent(myDialog.getData().argv);
		}

		window.location = '?node=<?php echo $details['node']; ?>&' + searchRequest;
	};

	var myButtons = [
		{ text:"Search", handler:handleSubmit, isDefault:true },
		{ text:"Reset", handler:handleCancel }
	];

	var myDialog = new YAHOO.widget.Dialog("divSearchNodeJobs", {
		close: false,
		draggable: false,
		fixedcenter: false,
		hideaftersubmit: false,
		underlay: "none",
		visible: true,
		width: "400px",
		zIndex: 0
	});

	myDialog.cfg.queueProperty("buttons", myButtons);
	myDialog.render();

	var myEnterDialog = new YAHOO.util.KeyListener("divSearchNodeJobs", { keys:13 }, { fn:handleSubmit });
	myEnterDialog.enable();

<?php
$api_url_requests = array();

if(isset($_GET['job_id'])) {
	$api_url_requests[] = sprintf("job_id=%s", urlencode('%' . $_GET['job_id'] . '%'));
}

if(isset($_GET['user'])) {
	$api_url_requests[] = sprintf("user=%s", urlencode('%' . $_GET['user'] . '%'));
}

if(isset($_GET['run_from_node'])) {
	$api_url_requests[] = sprintf("run_from_node=%s", urlencode('%' . $_GET['run_from_node'] . '%'));
}

if(isset($_GET['parallel'])) {
	$api_url_requests[] = sprintf("parallel=%s", urlencode('%' . $_GET['parallel'] . '%'));
}

if(isset($_GET['command'])) {
	$api_url_requests[] = sprintf("command=%s", urlencode('%' . $_GET['command'] . '%'));
}

if(isset($_GET['files'])) {
	$api_url_requests[] = sprintf("files=%s", urlencode('%' . $_GET['files'] . '%'));
}

if(isset($_GET['argv'])) {
	$api_url_requests[] = sprintf("argv=%s", urlencode('%' . $_GET['argv'] . '%'));
}

?>
	var myColumnDefs = [
		{key:"job_id", label:"Job ID", sortable:true},
		{key:"real_user", label:"Who", sortable:true, formatter:formatRunUser},
		{key:"start_time", label:"Start", sortable:true, formatter:YAHOO.widget.DataTable.formatDate},
		{key:"finish_time", label:"Finish", sortable:true, formatter:YAHOO.widget.DataTable.formatDate},
		{key:"command", label:"Command", sortable:true, formatter:YAHOO.BG.formatLongString},
		{key:"files", label:"Files", sortable:true, formatter:YAHOO.BG.formatLongString},
		{key:"stdout", label:"STDOUT", sortable:true, formatter:YAHOO.BG.formatLongString},
		{key:"stderr", label:"STDERR", sortable:true, formatter:YAHOO.BG.formatLongString},
		{key:"exit_status", label:"Exit", sortable:true},
		{key:"error_message", label:"Error", sortable:true, formatter:YAHOO.BG.formatLongString}
	];

	var sUrl = "/api/r/v1/list_jobs_by_node.php?format=json&node=<?php printf("%s&%s", $details['node'], implode('&', $api_url_requests)); ?>&";

	var myDataSource = new YAHOO.util.DataSource(sUrl);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	myDataSource.responseSchema = {
		resultsList: "records",
		fields: [
			{key:"job_id", parser:"number"},
			{key:"real_user"},
			{key:"run_as_user"},
			{key:"start_time", parser:YAHOO.util.DataSource.parseDate},
			{key:"finish_time", parser:YAHOO.util.DataSource.parseDate},
			{key:"command"},
			{key:"files"},
			{key:"stdout"},
			{key:"stderr"},
			{key:"exit_status"},
			{key:"error_message"},
		],
		metaFields: {
			totalRecords: "totalRecords"
		}
	};

	var myConfigs = {
		initialRequest: 'sort=finish_time&dir=desc&startIndex=0&results=100',
		draggableColumns: true,
		dynamicData: true,
		sortedBy: {key:"finish_time", dir:"desc"},
		paginator: new YAHOO.widget.Paginator({
			rowsPerPage:100,
			rowsPerPageOptions: YAHOO.BG.paginatorRowsPerPageOptions,
			template: YAHOO.BG.paginatorTemplate
		})
	};

	myDataTable = new YAHOO.widget.DataTable("divListJobs", myColumnDefs, myDataSource, myConfigs);

	myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
		oPayload.totalRecords = oResponse.meta.totalRecords;
		return oPayload;
	};

	myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
	myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

	var panel = new YAHOO.widget.Panel("widgetPanel", {
		visible:false, draggable:true, close:true, fixedcenter:true
	});

	panel.setBody('');
	panel.render(YAHOO.util.Dom.get('divPanelNodeTip'));

	myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
	myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

	myDataTable.subscribe("cellClickEvent", function(oArgs) {
		var target = oArgs.target;
		var record = this.getRecord(target);
		var key = this.getColumn(target).getKey();

		panel.cfg.setProperty("fixedcenter", true);

		switch(key) {
			case 'stdout':
			case 'stderr':
			case 'error_message':
				panel.setHeader(this.getColumn(target).label);
				panel.setBody('<pre>' + record.getData(key) + '</pre>');
				panel.cfg.setProperty("visible", true);
				panel.cfg.setProperty("fixedcenter", false);
				return false;
		}
	});

	myDataTable.subscribe("rowClickEvent", function(oArgs) {
		var target = oArgs.target;
		var record = this.getRecord(target);
		var jobid = record.getData('job_id');

		window.location = '/jobs/details.php?job_id=' + jobid;
	});
});
</script>
