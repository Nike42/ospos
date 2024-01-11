<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
	dialog_support.init("a.modal-dlg");
</script>


<div id="page_title"><?php echo $this->lang->line('reports_report_input'); ?></div>

<?php
if(isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}
?>

<?php echo form_open('#', array('id'=>'item_form', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')); ?>
	<div class="form-group form-group-sm" id="report_specific_input_data">
		<?php echo form_label($specific_input_name, 'specific_input_name_label', array('class'=>'required control-label col-xs-2')); ?>
		<div class="col-xs-3">
			<?php echo form_dropdown(
                'specific_input_data', $specific_input_data, '', 'id="specific_input_data" class="form-control selectpicker" data-live-search="true"'
            ); ?>
		</div>
	</div>

	<div class="form-group form-group-sm">
		<?php echo form_label($this->lang->line('reports_payment_type'), 'reports_payment_type_label', array('class'=>'required control-label col-xs-2')); ?>
		<div class="col-xs-3">
			<?php echo form_dropdown('payment_type', $payment_type, '', 'id="input_payment_type" class="form-control"'); ?>
		</div>
	</div>

    <div class="form-group form-group-sm">
	<?php 
	echo form_button(array(
        'name'=>'submit_due_payment',
        'id'=>'submit_due_payment',
        'content'=>$this->lang->line('common_submit'),
        'class'=>'btn btn-primary btn-sm', 'type'=>'button')
	);
	?>
    </div>
<?php echo form_close(); ?>

<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	$('#submit_due_payment').click(function(){
        let pathInfo = [
            `${window.location}`.replace('_input', ''), $('#specific_input_data').val(), 
            $('#input_payment_type').val()
        ]
        window.location = pathInfo.join('/')
    });
});
</script>
