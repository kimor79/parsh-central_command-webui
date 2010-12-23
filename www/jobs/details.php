<?php

include_once('Parsh/includes/ro.inc');
include_once('Parsh/www/www.inc');

if(empty($_GET['job_id'])) {
        $www->giveRedirect('/jobs/search.php');
        exit(0);
}

$r_details = $parsh->getJobDetailsByJob(array('job_id' => $_GET['job_id']));

if(count($r_details) != 1) {
        $www->giveRedirect('/jobs/search.php?job_id=' . urlencode($_GET['job_id']));
        exit(0);
}

$details = reset($r_details);

if($_GET['job_id'] != $details['job_id']) {
// Partial/wildcard was given, redirect to proper nodegroup
        $www->giveRedirect('/jobs/details.php?job_id=' . urlencode($details['job_id']));
        exit(0);
}

include('top.inc');

if(!empty($details['start_time'])) {
	$details['start_time'] = date('r', $details['start_time']);
} else {
	$details['start_time'] = 'Not started yet';
}

if(!empty($details['finish_time'])) {
	$details['finish_time'] = date('r', $details['finish_time']);
} else {
	if(empty($details['start_Time'])) {
		$details['finish_time'] = '';
	} else {
		$details['finish_time'] = 'Still running';
	}
}

$details['user'] = $details['real_user'];
if($details['real_user'] !== $details['run_as_user']) {
	$details['user'] .= sprintf(" (as %s)", $details['run_as_user']);
}
?>

<br>

<table class="table_box" cellpadding="3" cellspacing="5">
 <tr><td>

<table cellpadding="10">
 <tr>
  <td><b>Job ID:</b> <?php echo $details['job_id']; ?></td>
  <td><b>Nodes:</b> <?php printf("%d (%d at a time)", $details['total_nodes'], $details['parallel']); ?></td>
 </tr><tr>
  <td><b>User:</b> <?php echo $details['user']; ?></td>
  <td><b>From:</b> <?php echo gethostbyaddr($details['run_from_node']); ?></td>
 </tr><tr>
  <td><b>Start:</b> <?php echo $details['start_time']; ?></td>
  <td><b>Finish:</b> <?php echo $details['finish_time']; ?></td>
 </tr><tr>
  <td colspan=2><b>Remote Command:</b> <?php echo $details['command']; ?></td>
 </tr><tr>
  <td colspan=2><b>Files:</b> <?php echo $details['files']; ?></td>
 </tr><tr>
  <td colspan=2><b>Command Line:</b> <?php echo $details['argv']; ?></td>
 </tr>
</table>

 </td><td>
<img src="/img/job_status.php?job_id=<?php echo $details['job_id']; ?>">
 </td></tr>
</table>

<br>

<div id="divSearchJobNodes">
 <div class="hd"></div>
 <div class="bd">
<form name="formSearchJobNodes" method="GET" action="">
<table>
 <tr><td><label for="node">Node:<label></td><td><input type="text" name="node" size="30" value="<?php echo $_GET['node']; ?>"></td></tr>
 <tr><td><label for="stdout">STDOUT:<label></td><td><input type="text" name="stdout" size="30" value="<?php echo $_GET['stdout']; ?>"></td></tr>
 <tr><td><label for="stderr">STDERR:<label></td><td><input type="text" name="stderr" size="30" value="<?php echo $_GET['stderr']; ?>"></td></tr>
 <tr><td><label for="exit_status">Exit:<label></td><td><input type="text" name="exit_status" size="5" value="<?php echo $_GET['exit_status']; ?>"></td></tr>
 <tr><td><label for="error_message">Error:<label></td><td><input type="text" name="error_message" size="30" value="<?php echo $_GET['error_message']; ?>"></td></tr>
</table>
</form>
 </div>
 <div class="ft"></div>
</div>

<div id="divPanelNodeTip"></div>
<div id="divListNodes"></div>

</body>
</html>
<script type="text/javascript">
YAHOO.util.Event.addListener(window, "load", function() {
	var handleCancel = function() {
		this.form.reset();
	};

	var handleSubmit = function() {
		var searchRequest = '';

		if(myDialog.getData().node != '') {
			searchRequest += '&node=' + encodeURIComponent(myDialog.getData().node);
		}

		if(myDialog.getData().stdout != '') {
			searchRequest += '&stdout=' + encodeURIComponent(myDialog.getData().stdout);
		}

		if(myDialog.getData().stderr != '') {
			searchRequest += '&stderr=' + encodeURIComponent(myDialog.getData().stderr);
		}

		if(myDialog.getData().exit_status != '') {
			searchRequest += '&exit_status=' + encodeURIComponent(myDialog.getData().exit_status);
		}

		if(myDialog.getData().error_message != '') {
			searchRequest += '&error_message=' + encodeURIComponent(myDialog.getData().error_message);
		}

		window.location = '?job_id=<?php echo $details['job_id']; ?>&' + searchRequest;
	};

	var myButtons = [
		{ text:"Search", handler:handleSubmit, isDefault:true },
		{ text:"Reset", handler:handleCancel }
	];

	var myDialog = new YAHOO.widget.Dialog("divSearchJobNodes", {
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

	var myEnterDialog = new YAHOO.util.KeyListener("divSearchJobNodes", { keys:13 }, { fn:handleSubmit });
	myEnterDialog.enable();

<?php
$api_url_requests = array();

if(isset($_GET['node'])) {
	$api_url_requests[] = sprintf("node=%s", urlencode('%' . $_GET['node'] . '%'));
}

if(isset($_GET['stdout'])) {
	$api_url_requests[] = sprintf("stdout=%s", urlencode('%' . $_GET['stdout'] . '%'));
}

if(isset($_GET['stderr'])) {
	$api_url_requests[] = sprintf("stderr=%s", urlencode('%' . $_GET['stderr'] . '%'));
}

if(isset($_GET['exit_status'])) {
	$api_url_requests[] = sprintf("exit_status=%s", urlencode('%' . $_GET['exit_status'] . '%'));
}

if(isset($_GET['error_message'])) {
	$api_url_requests[] = sprintf("error_message=%s", urlencode('%' . $_GET['error_message'] . '%'));
}

?>
	var myColumnDefs = [
		{key:"node", label:"Node", sortable:true},
		{key:"start_time", label:"Start", sortable:true, formatter:YAHOO.widget.DataTable.formatDate},
		{key:"finish_time", label:"Finish", sortable:true, formatter:YAHOO.widget.DataTable.formatDate},
		{key:"stdout", label:"STDOUT", sortable:true, formatter:YAHOO.BG.formatLongString},
		{key:"stderr", label:"STDERR", sortable:true, formatter:YAHOO.BG.formatLongString},
		{key:"exit_status", label:"Exit", sortable:true},
		{key:"error_message", label:"Error", sortable:true, formatter:YAHOO.BG.formatLongString}
	];

	var sUrl = "/api/r/v1/list_nodes_by_job.php?format=json&job_id=<?php printf("%s&%s", $details['job_id'], implode('&', $api_url_requests)); ?>&";

	var myDataSource = new YAHOO.util.DataSource(sUrl);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	myDataSource.responseSchema = {
		resultsList: "records",
		fields: [
			{key:"node"},
			{key:"start_time", parser:YAHOO.util.DataSource.parseDate},
			{key:"finish_time", parser:YAHOO.util.DataSource.parseDate},
			{key:"stdout"},
			{key:"stderr"},
			{key:"exit_status"},
			{key:"error_message"}
		],
		metaFields: {
			totalRecords: "totalRecords"
		}
	};

	var myConfigs = {
		initialRequest: "sort=finish_time&dir=desc&startIndex=0&results=100",
		dynamicData: true,
		sortedBy: {key:"finish_time", dir:"desc"},
		paginator: new YAHOO.widget.Paginator({
			rowsPerPage:100,
			rowsPerPageOptions: YAHOO.BG.paginatorRowsPerPageOptions,
			template: YAHOO.BG.paginatorTemplate
		})
	};

	myDataTable = new YAHOO.widget.DataTable("divListNodes", myColumnDefs, myDataSource, myConfigs);

	myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
		oPayload.totalRecords = oResponse.meta.totalRecords;
		return oPayload;
	};

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
		var node = record.getData('node');

		window.location = '/nodes/details.php?node=' + node;
	});
});
</script>
