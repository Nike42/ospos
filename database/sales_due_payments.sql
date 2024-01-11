CREATE TABLE IF NOT EXISTS `ospos_sales_due_payments` (
  `sales_due_payments_id` int(10) NOT NULL AUTO_INCREMENT,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `employee_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `comments` text DEFAULT NULL,
  PRIMARY KEY (`sales_due_payments_id`),
  KEY `employee_id` (`employee_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;