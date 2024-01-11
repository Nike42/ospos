<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Summary_report.php");

class Summary_employees extends Summary_report
{
	protected function _get_data_columns()
	{
		return array(
			array('employee_name' => $this->lang->line('reports_employee')),
			array('sales' => $this->lang->line('reports_sales'), 'sorter' => 'number_sorter'),
			array('quantity' => $this->lang->line('reports_quantity'), 'sorter' => 'number_sorter'),
			array('subtotal' => $this->lang->line('reports_subtotal'), 'sorter' => 'number_sorter'),
			array('tax' => $this->lang->line('reports_tax'), 'sorter' => 'number_sorter'),
			array('total' => $this->lang->line('reports_total'), 'sorter' => 'number_sorter'),
			array('cost' => $this->lang->line('reports_cost'), 'sorter' => 'number_sorter'),
			array('profit' => $this->lang->line('reports_profit'), 'sorter' => 'number_sorter'));
	}

	protected function _select(array $inputs)
	{
		parent::_select($inputs);

		$this->db->select('
				MAX(CONCAT(employee_p.first_name, " ", employee_p.last_name)) AS employee,
				SUM(sales_items.quantity_purchased) AS quantity_purchased,
				COUNT(DISTINCT sales.sale_id) AS sales, sales.employee_id AS employee_id
		');
	}

	protected function _from()
	{
		parent::_from();

		$this->db->join('people AS employee_p', 'sales.employee_id = employee_p.person_id');
	}

	protected function _group_order()
	{
		$this->db->group_by('sales.employee_id');
		$this->db->order_by('employee_p.last_name');
	}

	 
	public function getPaymentTypeSummaryDataColumns()
	{
		return array(
			array('employee_name' => $this->lang->line('reports_employee')),
			array('cash' => $this->lang->line('sales_cash')),
			array('debit_card' => $this->lang->line('sales_debit')),
			array('bank_transfer' => $this->lang->line('sales_credit'))
		);
	}

	public function prepareEmployeePaymentTypeSummary(array $inputs, $employee_id)
	{
		$employee_payment_type_summary = $this->getEmployeePaymentTypeSummary($inputs, $employee_id);
		$employee_payment_type_data = [
			'debit_card'=>to_currency("0.00"), 'cash'=>to_currency("0.00"), 
			'bank_transfer'=>to_currency("0.00") //'credit_card'=>0,
		];
		$supported_types = ['Debit Card', 'Cash', 'Bank Transfer'];
		foreach($employee_payment_type_summary as $key=>$value)
		{	
			if(in_array($value['payment_type'], $supported_types)){
				$payment_type = strtolower(implode('_', explode(' ', $value['payment_type'])));
				$employee_payment_type_data[$payment_type] = to_currency($value['type_total']);
			}
		}
		return $employee_payment_type_data;
	}

	public function getEmployeePaymentTypeSummary(array $inputs, $employee_id)
	{
		$where_sales_item_exits = '';
		if($inputs['location_id'] != 'all')
			$where_sales_item_exits = $this->_get_where_sales_item_exits($inputs);
		$where_sale_exits = $this->_get_where_sale_exits($inputs);
		$this->db->select('payment_type, SUM(payment_amount - cash_refund) AS type_total');
		$this->db->from('sales_payments');
		$this->db->where('employee_id', $employee_id);
		$this->db->where($where_sale_exits);
		if($inputs['location_id'] != 'all')
			$this->db->where($where_sales_item_exits);
		$this->db->group_by('payment_type');
		return $this->db->get()->result_array();
	}

	protected function _get_where_date_exits(array $inputs){
		$where = '';
		if(empty($this->config->item('date_or_time_format')))
		{
			$where .= 'DATE(sales.sale_time) BETWEEN ' . $this->db->escape($inputs['start_date']); 
			$where .= ' AND ' . $this->db->escape($inputs['end_date']);
		}
		else
		{
			$where .= 'sales.sale_time BETWEEN ' . $this->db->escape(rawurldecode($inputs['start_date']));
			$where .= ' AND ' . $this->db->escape(rawurldecode($inputs['end_date']));
		}
		return $where;
	}

	protected function _get_where_sale_exits(array $inputs)
	{
		$where_date_exits = $this->_get_where_date_exits($inputs);
		$this->db->select(1);
		$this->db->from('sales AS sales');
		$this->db->where("sales.sale_id = ".$this->db->dbprefix('sales_payments').".sale_id");
		$this->db->where($where_date_exits);
		if($inputs['sale_type'] == 'complete')
		{
			$this->db->where('sale_status', COMPLETED);
			$this->db->group_start();
				$this->db->where('sale_type', SALE_TYPE_POS);
				$this->db->or_where('sale_type', SALE_TYPE_INVOICE);
				$this->db->or_where('sale_type', SALE_TYPE_RETURN);
			$this->db->group_end();
		}
		elseif($inputs['sale_type'] == 'sales')
		{
			$this->db->where('sale_status', COMPLETED);
			$this->db->group_start();
				$this->db->where('sale_type', SALE_TYPE_POS);
				$this->db->or_where('sale_type', SALE_TYPE_INVOICE);
			$this->db->group_end();
		}
		elseif($inputs['sale_type'] == 'quotes')
		{
			$this->db->where('sale_status', SUSPENDED);
			$this->db->where('sale_type', SALE_TYPE_QUOTE);
		}
		elseif($inputs['sale_type'] == 'work_orders')
		{
			$this->db->where('sale_status', SUSPENDED);
			$this->db->where('sale_type', SALE_TYPE_WORK_ORDER);
		}
		elseif($inputs['sale_type'] == 'canceled')
		{
			$this->db->where('sale_status', CANCELED);
		}
		elseif($inputs['sale_type'] == 'returns')
		{
			$this->db->where('sale_status', COMPLETED);
			$this->db->where('sale_type', SALE_TYPE_RETURN);
		}	
		$sub_query = $this->db->get_compiled_select();
		return "EXISTS($sub_query)";
	}

	protected function _get_where_sales_item_exits(array $inputs)
	{
		$this->db->select(1);
		$this->db->from('sales_items AS sales_items');
		$this->db->where("sales_items.sale_id = ".$this->db->dbprefix('sales_payments').".sale_id");	
		$this->db->where('item_location', $inputs['location_id']);
		$sub_query = $this->db->get_compiled_select();
		return "EXISTS($sub_query)";
	}
}
?>
