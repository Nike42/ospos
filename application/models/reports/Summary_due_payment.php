<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Summary_due_payment extends Report
{
	public function getDataColumns()
	{
		return array(
            array('customer_name' => $this->lang->line('reports_supplied_by')),
            array('payment_count' => 'payment_count'),
            // array('customer_name' => $this->lang->line('reports_sold_to')),
            array('payment_types' => "{$this->lang->line('reports_payment_type')}s"),
            array('total_payment_amount' => 'Total Payment Amount')
		);
	}

	public function getData(array $inputs)
	{
		$where_date_between = $this->_where_date_between($inputs);
		$this->db->select('
			customer_id,
			COUNT(payment_amount) AS payment_count,
			GROUP_CONCAT(DISTINCT payment_type SEPARATOR ", ") AS payment_types,
			SUM(payment_amount) AS total_payment_amount,
			MAX(CONCAT(customer.first_name, " ", customer.last_name)) AS customer_name
		');
		$this->db->from('sales_due_payments');
		$this->db->join('people AS customer', 'sales_due_payments.customer_id = customer.person_id');
		$this->db->where($where_date_between);

		if($inputs['payment_type'] != 'all')
		{
			$this->db->where('payment_type', $inputs['payment_type']);
        }
		$this->db->group_by('customer_id');
		return $this->db->get()->result_array();
	}

	public function getSummaryData(array $inputs)
	{
		$where_date_between = $this->_where_date_between($inputs);
		$this->db->select('SUM(payment_amount) AS total_payment_amount');
		$this->db->from('sales_due_payments');
		$this->db->where($where_date_between);
		return $this->db->get()->row_array();
	}

	private function _where_date_between(array $inputs)
	{
		$where = '';
		if(empty($this->config->item('date_or_time_format')))
		{
			$where .= 'DATE(payment_date) BETWEEN ' . $this->db->escape($inputs['start_date']) 
			. ' AND ' . $this->db->escape($inputs['end_date']);
		}
		else
		{
			$where .= 'payment_date BETWEEN ' 
			. $this->db->escape(rawurldecode($inputs['start_date'])) . ' AND ' 
			. $this->db->escape(rawurldecode($inputs['end_date']));
		}
		return $where;
	}
}
?>
