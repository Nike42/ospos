<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Summary_report.php");

class Summary_customers extends Summary_report
{
	protected function _get_data_columns()
	{
		return array(
			array('customer_name' => $this->lang->line('reports_customer')),
			array('sales' => $this->lang->line('reports_sales'), 'sorter' => 'number_sorter'),
			array('quantity' => $this->lang->line('reports_quantity'), 'sorter' => 'number_sorter'),
			array('subtotal' => $this->lang->line('reports_subtotal'), 'sorter' => 'number_sorter'),
			array('tax' => $this->lang->line('reports_tax'), 'sorter' => 'number_sorter'),
			array('total' => $this->lang->line('reports_total'), 'sorter' => 'number_sorter'),
			array('cost' => $this->lang->line('reports_cost'), 'sorter' => 'number_sorter'),
			array('profit' => $this->lang->line('reports_profit'), 'sorter' => 'number_sorter'),
			array('customer_point' => 'Reward', 'sorter' => 'number_sorter')
		);
	}

	protected function _select(array $inputs)
	{
		parent::_select($inputs);

		$this->db->select('
				MAX(CONCAT(customer_p.first_name, " ", customer_p.last_name)) AS customer,
				SUM(sales_items.quantity_purchased) AS quantity_purchased,
				COUNT(DISTINCT sales.sale_id) AS sales,
				MAX(customers_info.points) AS customer_point
		');
	}

	protected function _from()
	{
		parent::_from();

		$this->db->join('people AS customer_p', 'sales.customer_id = customer_p.person_id');
		$this->db->join('customers AS customers_info', 'customer_p.person_id = customers_info.person_id', 'left outer');
	}

	protected function _group_order()
	{
		$this->db->group_by('sales.customer_id');
		$this->db->order_by('customer_p.last_name');
	}
}
?>
