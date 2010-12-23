<?php
include('top.inc');

?>

<br>

<div id="divSearchJobs">
 <div class="hd"></div>
 <div class="bd">
<form name="formSearchJobs" method="GET" action="">
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

		window.location = '?' + searchRequest;
	};

	var myButtons = [
		{ text:"Search", handler:handleSubmit, isDefault:true },
		{ text:"Reset", handler:handleCancel }
	];

	var myDialog = new YAHOO.widget.Dialog("divSearchJobs", {
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

	var myEnterDialog = new YAHOO.util.KeyListener("divSearchJobs", { keys:13 }, { fn:handleSubmit });
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
		{key:"total_nodes", label:"Nodes", sortable:true},
		{key:"parallel", label:"At Once", sortable:true},
		{key:"command", label:"Command", sortable:true, formatter:YAHOO.BG.formatLongString},
		{key:"files", label:"Files", sortable:true, formatter:YAHOO.BG.formatLongString}
	];

	var sUrl = '/api/r/v1/list_jobs.php?format=json&<?php echo implode('&', $api_url_requests); ?>&';

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
			{key:"total_nodes", parser:"number"},
			{key:"parallel", parser:"number"},
			{key:"command"},
			{key:"files"}
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

	myDataTable.subscribe("rowClickEvent", function(oArgs) {
		var target = oArgs.target;
		var record = this.getRecord(target);
		var jobid = record.getData('job_id');

		window.location = '/jobs/details.php?job_id=' + jobid;
	});
});
</script>
