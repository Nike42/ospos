<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
	dialog_support.init("a.modal-dlg");
</script>


<div id="page_title">Due Payment</div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('sales/save_due_payment', array('method'=>'post', 'id'=>'item_form', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')); ?>
	<div class="form-group form-group-sm">
		<?php echo form_label('Due Payment Amount', 'due_payment_amount', array('class'=>'control-label col-xs-2 required')); ?>
		<div class="col-xs-3">
			<?php echo form_input(
                array(
                    'name'=>'input_payment_amount', 
                    'id'=>'input_payment_amount',
                    'class'=>'form-control input-sm'
                )
            ); ?>
		</div>
	</div>

	<div class="form-group form-group-sm" id="report_specific_input_data">
		<?php echo form_label($customer_title, 'specific_input_name_label', array('class'=>'required control-label col-xs-2')); ?>
		<div class="col-xs-3">
			<?php echo form_dropdown(
                'specific_input_data',
                 $customers, '', 
                 'id="specific_input_data" class="form-control selectpicker" data-live-search="true"'
            ); ?>
		</div>
	</div>

	<div class="form-group form-group-sm">
		<?php echo form_label($this->lang->line('reports_payment_type'), 'reports_payment_type_label', array('class'=>'required control-label col-xs-2')); ?>
		<div class="col-xs-3">
			<?php echo form_dropdown(
                'payment_type', $payment_type, '', 
                'id="input_payment_type" class="form-control"'
            ); ?>
		</div>
	</div>

    <div class="form-group form-group-sm">
        <?php echo form_label('Comments', 'description', array('class'=>'control-label col-xs-2')); ?>
        <div class='col-xs-3'>
            <?php echo form_textarea(array(
                'name'=>'due_payment_comment',
                'id'=>'due_payment_comment',
                'class'=>'form-control input-sm')
            );?>
        </div>
    </div>
	<div class="form-group form-group-sm">
		<?php echo form_label('Employee', 'employee', array('class'=>'control-label col-xs-2 required')); ?>
		<div class="col-xs-3">
            <?php echo form_input(
                array(
                    'name'=>'employee', 'class'=>'form-control input-sm', 
                    'id'=>'employee', 'disabled'=>'', 'value'=>$employee_name
                )
            ); ?>
		</div>
	</div>
    <div class="form-group form-group-sm">
	<?php 
	echo form_button(array(
        'name'=>'submit_due_payment',
        'id'=>'submit_due_payment',
        'content'=>$this->lang->line('common_submit'),
        'class'=>'btn btn-primary btn-sm', 'type'=>'submit')
	);
	?>
	<?php 
	echo form_button(array(
        'name'=>'submit_due_payment',
        'id'=>'next_due_payment',
        'content'=>'Next',
        'class'=>'btn btn-primary btn-sm', 'type'=>'button')
	);
	?>
    </div>
<?php echo form_close(); ?>

<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript">
	$(document).ready(function()
	{
		$('#next_due_payment').click(function(){
			if($('#item_form').valid()){
				$('#item_form').attr('action', 'sales/save_due_payment/true')
				$('#item_form').submit()
			}
		});

		$('#item_form').validate($.extend({
			// submitHandler: function(form) { 
			// 	$(form).submit()
			// },
			errorLabelContainer: '#error_message_box',
			rules: {input_payment_amount:{required: true, number: true}},
			messages: {
				input_payment_amount:{
					required: 'Due payment amount is required', number: 'Due payment must be number'
				}
			}
		}, form_support.error));
	});
</script>
