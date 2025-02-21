<?php
namespace Opencart\Admin\Model\Extension\tmdaffiliateimportexport\tool;
use \Opencart\System\Helper as Helper;

/**
 * TMD(http://opencartextensions.in/)
 *
 * Copyright (c) 2016 - 2017 TMD
 * This package is Copyright so please us only one domain 
 * 
 */
class affimport extends \Opencart\System\Engine\Model {

	public function getCustomerByEmail(string $email): array {

		if(VERSION>='4.0.2.0'){	
			$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "customer` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");
		}
		else{
			$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "customer` WHERE LCASE(`email`) = '" . $this->db->escape(Helper\Utf8\strtolower($email)) . "'");
		}
		return $query->row;
	}
	
	public function addAffiliate(array $data): int {
		$customer_group_id = (int)$this->config->get('config_customer_group_id');
		$status = 0;
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer` SET  `firstname` = '" . $this->db->escape((string)$data['firstname']) . "', `lastname` = '" . $this->db->escape((string)$data['lastname']) . "',`customer_group_id` = '" . (int)$customer_group_id . "', `email` = '" . $this->db->escape((string)$data['email']) . "',`telephone` = '" . $this->db->escape((string)$data['telephone']) . "',`status` = '" . (bool)(isset($data['status']) ? $data['status'] : 0) . "', `password` = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', `date_added` = NOW()");
		

		$customer_id = $this->db->getLastId();
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "address` SET `customer_id` = '" . (int)$customer_id . "', `firstname` = '" . $this->db->escape($data['firstname']) . "', `lastname` = '" . $this->db->escape($data['lastname']) . "', `company` = '" . $this->db->escape($data['company']) . "', `address_1` = '" . $this->db->escape($data['address_1']) . "', `address_2` = '" . $this->db->escape($data['address_2']) . "', `city` = '" . $this->db->escape($data['city']) . "'");

		
		if(VERSION>='4.0.2.0'){
			$tracking = oc_token(10);
		}
		else{
			$tracking = Helper\General\token(10);
		}
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_affiliate SET `customer_id` = '" . (int)$customer_id . "',`company` = '" . $this->db->escape((string)$data['company']) . "',`commission` = '" . (float)$data['commission'] . "', `tracking` = '" . $this->db->escape((string)$tracking) . "', `tax` = '" . $this->db->escape((string)$data['tax']) . "', `payment` = '" . $this->db->escape((string)$data['payment']) . "',`cheque` = '" . $this->db->escape((string)$data['cheque']) . "', `paypal` = '" . $this->db->escape((string)$data['paypal']) . "', `bank_name` = '" . $this->db->escape((string)$data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape((string)$data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape((string)$data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape((string)$data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape((string)$data['bank_account_number']) . "',`approved` = '" . $this->db->escape($data['approved']) . "', `status` = '" . (bool)(isset($data['status']) ? $data['status'] : 0) . "',`date_added` = '" . $this->db->escape((string)$data['date_added']) . "'");

		return $customer_id;

	}


	public function editAffiliate($data, $customer_id) {

		$status = 0;
		
		$this->db->query("UPDATE " . DB_PREFIX . "customer_affiliate SET `customer_id` = '" . (int)$customer_id . "',`company` = '" . $this->db->escape((string)$data['company']) . "',`commission` = '" . (float)$data['commission'] . "', `tax` = '" . $this->db->escape((string)$data['tax']) . "', `payment` = '" . $this->db->escape((string)$data['payment']) . "',`cheque` = '" . $this->db->escape((string)$data['cheque']) . "', `paypal` = '" . $this->db->escape((string)$data['paypal']) . "', `bank_name` = '" . $this->db->escape((string)$data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape((string)$data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape((string)$data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape((string)$data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape((string)$data['bank_account_number']) . "',`approved` = '" . $this->db->escape($data['approved']) . "', `status` = '" . (bool)(isset($data['status']) ? $data['status'] : 0) . "', date_added = NOW()");

		$customer_id = $this->db->getLastId();
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_affiliate` WHERE `customer_id` = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer` WHERE `customer_id` = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "address` WHERE `customer_id` = '" . (int)$customer_id . "'");

		$this->db->query("INSERT INTO `" . DB_PREFIX . "address` SET `customer_id` = '" . (int)$customer_id . "', `firstname` = '" . $this->db->escape($data['firstname']) . "', `lastname` = '" . $this->db->escape($data['lastname']) . "', `company` = '" . $this->db->escape($data['company']) . "', `address_1` = '" . $this->db->escape($data['address_1']) . "', `address_2` = '" . $this->db->escape($data['address_2']) . "', `city` = '" . $this->db->escape($data['city']) . "'");

		$customer_group_id = (int)$this->config->get('config_customer_group_id');
		
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET  `firstname` = '" . $this->db->escape((string)$data['firstname']) . "', `lastname` = '" . $this->db->escape((string)$data['lastname']) . "',`customer_group_id` = '" . (int)$customer_group_id . "', `email` = '" . $this->db->escape((string)$data['email']) . "',`telephone` = '" . $this->db->escape((string)$data['telephone']) . "',`status` = '" . (bool)(isset($data['status']) ? $data['status'] : 0) . "', `password` = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', `date_added` = NOW()");
	}
	
	public function getselectedAffiliates($customer_id) {
		$sql = "SELECT *, CONCAT(a.firstname, ' ', a.lastname) AS name, (SELECT SUM(at.amount) FROM " . DB_PREFIX . "customer_transaction at WHERE a.customer_id = '" . (int)$customer_id . "' and at.customer_id = a.customer_id GROUP BY at.customer_id) AS balance FROM " . DB_PREFIX . "customer a";


		$query=$this->db->query($sql);
		return $query->row;

	}

	public function getselectedAddress($customer_id) {
		$sql = "SELECT *, CONCAT(a.firstname, ' ', a.lastname) AS name, (SELECT SUM(at.amount) FROM " . DB_PREFIX . "customer_transaction at WHERE a.customer_id = '" . (int)$customer_id . "' and at.customer_id = a.customer_id GROUP BY at.customer_id) AS balance FROM " . DB_PREFIX . "address a";


		$query=$this->db->query($sql);
		return $query->row;

	}

	public function getAffiliate(int $customer_id): array {
		$query = $this->db->query("SELECT DISTINCT *, CONCAT(c.`firstname`, ' ', c.`lastname`) AS `customer`, ca.`custom_field` FROM `" . DB_PREFIX . "customer_affiliate` ca LEFT JOIN `" . DB_PREFIX . "customer` c ON (ca.`customer_id` = c.`customer_id`) WHERE ca.`customer_id` = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function disapprove($customer_id) {
		$affiliate_info = $this->getAffiliate($customer_id);

		if ($affiliate_info) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer_affiliate SET approved = '0' WHERE customer_id = '" . (int)$customer_id . "'");
		}	
	}
		
	public function getCountrybyname($country_id) {
		$query = $this->db->query("SELECT  COUNT(name) As name FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['name'];
	}
				
	public function getZonebyname($zone_id) {
		$query = $this->db->query("SELECT COUNT(name) As `name` FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['name'];
	}

	public function approve($customer_id){
		
		$this->db->query("UPDATE " . DB_PREFIX . "customer_affiliate SET approved = '1' WHERE customer_id = '" . (int)$customer_id . "'");
		
	}


	public function getAffiliates(array $data = []): array {
		$sql = "SELECT *, CONCAT(c.`firstname`, ' ', c.`lastname`) AS `name`, ca.`status` FROM `" . DB_PREFIX . "customer_affiliate` ca LEFT JOIN `" . DB_PREFIX . "customer` c ON (ca.`customer_id` = c.`customer_id`)";

		$implode = [];

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.`firstname`, ' ', c.`lastname`) LIKE '" . $this->db->escape((string)$data['filter_name'] . '%') . "'";
		}

		if (!empty($data['filter_tracking'])) {
			$implode[] = "ca.`tracking` = '" . $this->db->escape((string)$data['filter_tracking']) . "'";
		}

		if (!empty($data['filter_commission'])) {
			$implode[] = "ca.`commission` = '" . (float)$data['filter_commission'] . "'";
		}

		// xml
		if (isset($data['filter_approved']) && $data['filter_approved'] !== '') {
			$implode[] = "ca.`approved` = '" . (int)$data['filter_approved'] . "'";
		}
		// xml

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$implode[] = "ca.`status` = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_from'])) {
			$implode[] = "DATE(ca.`date_added`) >= DATE('" . $this->db->escape((string)$data['filter_date_from']) . "')";
		}

		if (!empty($data['filter_date_to'])) {
			$implode[] = "DATE(ca.`date_added`) <= DATE('" . $this->db->escape((string)$data['filter_date_to']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = [
			'name',
			'ca.tracking',
			'ca.commission',
			'ca.status',
			'ca.date_added'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `name`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}


		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalAffiliates(array $data = []): int {
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "customer_affiliate` ca LEFT JOIN `" . DB_PREFIX . "customer` c ON (ca.`customer_id` = c.`customer_id`)";

		$implode = [];

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.`firstname`, ' ', c.`lastname`) LIKE '" . $this->db->escape((string)$data['filter_name'] . '%') . "'";
		}

		if (!empty($data['filter_tracking'])) {
			$implode[] = "ca.`tracking` = '" . $this->db->escape((string)$data['filter_tracking']) . "'";
		}

		if (!empty($data['filter_commission'])) {
			$implode[] = "ca.`commission` = '" . (float)$data['filter_commission'] . "'";
		}

		if (isset($data['filter_approved']) && $data['filter_approved'] !== '') {
			$implode[] = "ca.`approved` = '" . (int)$data['filter_approved'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$implode[] = "ca.`status` = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_from'])) {
			$implode[] = "DATE(ca.`date_added`) >= DATE('" . $this->db->escape((string)$data['filter_date_from']) . "')";
		}

		if (!empty($data['filter_date_to'])) {
			$implode[] = "DATE(ca.`date_added`) <= DATE('" . $this->db->escape((string)$data['filter_date_to']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}
		
	
}
?>