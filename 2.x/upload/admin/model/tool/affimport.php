<?php
/**
 * TMD(http://opencartextensions.in/)
 *
 * Copyright (c) 2016 - 2017 TMD
 * This package is Copyright so please us only one domain 
 * 
 */
class ModelToolAffImport extends Model {
	
	public function addAffiliate($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "affiliate SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', code = '" . $this->db->escape($data['code']) . "', commission = '" . (float)$data['commission'] . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', cheque = '" . $this->db->escape($data['cheque']) . "', paypal = '" . $this->db->escape($data['paypal']) . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
		
		
		if ($data['password_status']) {
				$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
			}
		else
		{
			if(!empty($data['password']))
			{
				$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET salt = '" . $this->db->escape($data['salt']). "', password = '" .$this->db->escape($data['password']). "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
			}
		}
	}

	public function editAffiliate($data, $affiliate_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', code = '" . $this->db->escape($data['code']) . "', commission = '" . (float)$data['commission'] . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', cheque = '" . $this->db->escape($data['cheque']) . "', paypal = '" . $this->db->escape($data['paypal']) . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "', status = '" . (int)$data['status'] . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		if ($data['password_status']) {
				$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		}
		else
		{
			if(!empty($data['password']))
			{
				$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET salt = '" . $this->db->escape($data['salt']). "', password = '" .$this->db->escape($data['password']). "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
			}
		}
	}
	
	public function getZonebyname($zone) {
		$query = $this->db->query("SELECT zone_id  FROM " . DB_PREFIX . "zone WHERE name = '" .$zone . "'");

		return $query->row['zone_id'];
	}
	
	public function getCountrybyname($country) {
		$query = $this->db->query("SELECT country_id FROM " . DB_PREFIX . "country WHERE name = '" . $country . "'");

		return $query->row['country_id'];
	}
	
		
	
}
?>