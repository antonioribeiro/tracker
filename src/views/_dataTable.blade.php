<script type='text/javascript' src='https://www.google.com/jsapi'></script>

<style>
	.google-visualization-table-td {
		padding-right: 8px !important;
		padding-left: 8px !important;
		padding-top: 6px !important;
		padding-bottom: 6px !important;
	}

	.google-visualization-table-page-numbers .page-number {
		border: 1px outset !important;
		padding: .65em .7em .5em .5em !important;
		font-size: .85em !important;
	}
</style>

<script type='text/javascript'>
	google.load('visualization', '1', {packages:['table']});

	jQuery.ajax({
		type: "GET",
		url: "{{ $route }}",
		data: { }
	})
	.done(function( data ) {
		drawTable(data);
	});

	function drawTable(data)
	{
		var tableData = new google.visualization.DataTable();

		var cssClassNames = {
			'headerCell': 'not-one',
		};

		for (var i=0; i<data.columns.length; i++)
		{
			tableData.addColumn(data.columns[i].type, data.columns[i].label);
		}

		for (var x=0; x<data.data.length; x++)
		{
			for (var i=0; i<data.columns.length; i++)
			{
				if (data.columns[i].type == 'datetime')
				{
					data.data[x][i] = new Date(data.data[x][i]);
				}
			}
		}

		tableData.addRows(data.data);

		var table = new google.visualization.Table(document.getElementById('table_div'));

		table.draw(tableData, {
			showRowNumber: false,
			page: 'enable',
			allowHtml: true,
			pageSize: 15,
			'cssClassNames': cssClassNames
		});
	}
</script>
