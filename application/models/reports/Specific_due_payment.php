<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Specific_due_payment extends Report
{
	public function getDataColumns()
	{
		return array(
			array('id' => $this->lang->line('reports_sale_id')),
            array('payment_date' => $this->lang->line('reports_date')),
            array('employee_name' => $this->lang->line('reports_received_by')),
            array('payment_type' => $this->lang->line('reports_payment_type')),
            array('payment_amount' => $this->lang->line('reports_cost')),
            array('comment' => $this->lang->line('reports_comments'))
		);
	}

	public function getData(array $inputs)
	{
		$this->db->select(
            'sales_due_payments.*,
			CONCAT(employee.first_name, " ", employee.last_name) AS employee_name'
        );
		$this->db->from('sales_due_payments');
		$this->db->join('people AS employee', 'sales_due_payments.employee_id = employee.person_id');
		$this->db->where('customer_id', $inputs['customer_id']);

		if($inputs['payment_type'] != 'all')
		{
			$this->db->where('payment_type', $inputs['payment_type']);
        }

		return $this->db->get()->result_array();
	}

	public function getSummaryData(array $inputs)
	{
		$this->db->select('SUM(payment_amount) AS payment_amount');
		$this->db->from('sales_due_payments');
		$this->db->where('customer_id', $inputs['customer_id']);
		return $this->db->get()->row_array();
	}

	public function get_info($customer_id)
	{
		$this->db->select('*');
		$this->db->from('people');
		$this->db->where('person_id', $customer_id);
		return $this->db->get()->row();
	}
}
?>
