<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
	dialog_support.init("a.modal-dlg");
</script>

<div id="page_title"><?php echo $title ?></div>

<div id="page_subtitle"><?php echo $subtitle ?></div>

<div id="table_holder">
	<table id="table"></table>
</div>
<?php if (isset($payment_type_headers) && isset($payment_type_summary_data)) { ?>
	<div id="table_holder2">
		<table id="table2"></table>
	</div>
<?php }?> 

<div id="report_summary">
	<?php
	foreach($summary_data as $name => $value)
	{ 
		if($name == "total_quantity")
		{
	?>
			<div class="summary_row"><?php echo $this->lang->line('reports_'.$name) . ': ' .$value; ?></div>
	<?php
		}
		else
		{
	?>
			<div class="summary_row"><?php echo $this->lang->line('reports_'.$name) . ': ' . to_currency($value); ?></div>
	<?php
		}
	}
	?>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

		$('#table')
			.addClass("table-striped")
			.addClass("table-bordered")
			.bootstrapTable({
				columns: <?php echo transform_headers($headers, TRUE, FALSE); ?>,
				stickyHeader: true,
				stickyHeaderOffsetLeft: $('#table').offset().left + 'px',
				stickyHeaderOffsetRight: $('#table').offset().right + 'px',
				pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
				sortable: true,
				showExport: true,
				exportDataType: 'all',
				exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
				pagination: true,
				showColumns: true,
				data: <?php echo json_encode($data); ?>,
				iconSize: 'sm',
				paginationVAlign: 'bottom',
				escape: false
		});

		<?php if (isset($payment_type_headers) && isset($payment_type_summary_data)) { ?>
			$('#table2')
				.addClass("table-striped")
				.addClass("table-bordered")
				.bootstrapTable({
					columns: <?php echo transform_headers($payment_type_headers, TRUE, FALSE); ?>,
					stickyHeader: true,
					stickyHeaderOffsetLeft: $('#table2').offset().left + 'px',
					stickyHeaderOffsetRight: $('#table2').offset().right + 'px',
					pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
					sortable: true,
					showExport: true,
					exportDataType: 'all',
					exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
					pagination: true,
					showColumns: true,
					data: <?php echo json_encode($payment_type_summary_data); ?>,
					iconSize: 'sm',
					paginationVAlign: 'bottom',
					escape: false
			});
		<?php }?> 
	});
</script>

<?php $this->load->view("partial/footer"); ?>
