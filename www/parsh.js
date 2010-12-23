formatRunUser = function (elCell, oRecord, oColumn, oData) {
	var run_as_user = oRecord.getData('run_as_user');

	if(oData != run_as_user) {
		elCell.innerHTML = oData + ' (as ' + run_as_user + ')';
	} else {
		elCell.innerHTML = oData;
	}
};
