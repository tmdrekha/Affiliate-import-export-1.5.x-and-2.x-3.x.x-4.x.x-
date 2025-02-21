<?php
class ModelExtensionAffimpexp extends Model {  
    
    public function importCustomer($data) {
        $customer_id = (int)$data['customer_id'];
        $address_id = (int)$data['address_id'];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $customer_id . "'");

        if ($query->num_rows > 0) {
            $this->db->query("UPDATE " . DB_PREFIX . "customer SET 
                customer_id = '" . (int)$data['customer_id'] . "', 
                customer_group_id = '" . (int)$data['customer_group_id'] . "', 
                firstname = '" . $this->db->escape($data['firstname']) . "', 
                lastname = '" . $this->db->escape($data['lastname']) . "', 
                email = '" . $this->db->escape($data['email']) . "', 
                telephone = '" . $this->db->escape($data['telephone']) . "', 
                newsletter = '" . $this->db->escape($data['newsletter']) . "', 
                status = '" . (int)$data['status'] . "',
                safe = '" . (int)$data['safe'] . "',
                date_added = '" . $this->db->escape($data['date_added']) . "'  
                WHERE customer_id = '" . (int)$customer_id . "'"); 

            if ($address_id) {
                $this->db->query("UPDATE " . DB_PREFIX . "address SET 
                    customer_id = '" . (int)$data['customer_id'] . "', 
                    company = '" . $this->db->escape($data['company']) . "', 
                    address_1 = '" . $this->db->escape($data['address_1']) . "', 
                    address_2 = '" . $this->db->escape($data['address_2']) . "', 
                    city = '" . $this->db->escape($data['city']) . "', 
                    postcode = '" . $this->db->escape($data['postcode']) . "', 
                    country_id = '" . $this->db->escape($data['country_id']) . "', 
                    zone_id = '" . $this->db->escape($data['zone_id']) . "' 
                    WHERE address_id = '" . (int)$address_id . "'"); 
            }

            return true; // Indicate success
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET 
                customer_id = '" . (int)$data['customer_id'] . "',
                customer_group_id = '" . (int)$data['customer_group_id'] . "', 
                firstname = '" . $this->db->escape($data['firstname']) . "', 
                lastname = '" . $this->db->escape($data['lastname']) . "', 
                email = '" . $this->db->escape($data['email']) . "', 
                telephone = '" . $this->db->escape($data['telephone']) . "', 
                newsletter = '" . $this->db->escape($data['newsletter']) . "', 
                ip = '" . $this->db->escape($data['ip']) . "', 
                status = '" . (int)$data['status'] . "',
                safe = '" . (int)$data['safe'] . "',
                date_added = '" . $this->db->escape($data['date_added']) . "'"); 

            $new_customer_id = $this->db->getLastId();
            if ($address_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "address SET 
                    customer_id = '" . (int)$new_customer_id . "', 
                    firstname = '" . $this->db->escape($data['firstname']) . "', 
                    lastname = '" . $this->db->escape($data['lastname']) . "', 
                    company = '" . $this->db->escape($data['company']) . "', 
                    address_1 = '" . $this->db->escape($data['address_1']) . "', 
                    address_2 = '" . $this->db->escape($data['address_2']) . "', 
                    city = '" . $this->db->escape($data['city']) . "', 
                    postcode = '" . $this->db->escape($data['postcode']) . "', 
                    country_id = '" . $this->db->escape($data['country_id']) . "', 
                    zone_id = '" . $this->db->escape($data['zone_id']) . "'");
            }

            return true;
        }

        return false;   
    }

    public function importCustomerAffiliate($affiliateDataArray) {
        $customer_id = (int)$affiliateDataArray['customer_id'];

        // Check if customer affiliate exists
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_affiliate WHERE customer_id = '" . $customer_id . "'");

        if ($query->num_rows > 0) {
            // Update existing customer affiliate
            $this->db->query("UPDATE " . DB_PREFIX . "customer_affiliate SET 
                company = '" . $this->db->escape($affiliateDataArray['company']) . "', 
                website = '" . $this->db->escape($affiliateDataArray['website']) . "', 
                tracking = '" . $this->db->escape($affiliateDataArray['tracking']) . "', 
                commission = '" . (float)$affiliateDataArray['commission'] . "', 
                tax = '" . (float)$affiliateDataArray['tax'] . "', 
                payment = '" . $this->db->escape($affiliateDataArray['payment']) . "', 
                cheque = '" . $this->db->escape($affiliateDataArray['cheque']) . "', 
                status = '" . (int)$affiliateDataArray['status'] . "'
                WHERE customer_id = '" . $customer_id . "'"); 

            return true; 
        }
        else {
            // Insert new customer affiliate
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_affiliate SET 
                customer_id = '" . $customer_id . "', 
                company = '" . $this->db->escape($affiliateDataArray['company']) . "', 
                website = '" . $this->db->escape($affiliateDataArray['website']) . "', 
                tracking = '" . $this->db->escape($affiliateDataArray['tracking']) . "', 
                commission = '" . (float)$affiliateDataArray['commission'] . "', 
                tax = '" . (float)$affiliateDataArray['tax'] . "', 
                payment = '" . $this->db->escape($affiliateDataArray['payment']) . "', 
                cheque = '" . $this->db->escape($affiliateDataArray['cheque']) . "', 
                status = '" . (int)$affiliateDataArray['status'] . "', 
                date_added = '" . $this->db->escape($affiliateDataArray['date_added']) . "'");

            return true; 
        }

        return false; 
    }
    public function importhistory($historyDataArray) {
        // Ensure customer_id and customer_history_id are set
        $customer_id = (int)$historyDataArray['customer_id'];
        $customer_history_id = (int)$historyDataArray['customer_history_id']; // Assuming this is passed in the array

        // Check if customer history exists
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_history WHERE customer_history_id = '" . $customer_history_id . "'");

        if ($query->num_rows > 0) {
            // Update existing customer history
            $this->db->query("UPDATE " . DB_PREFIX . "customer_history SET 
                customer_id = '" . $customer_id . "', 
                comment = '" . $this->db->escape($historyDataArray['comment']) . "', 
                date_added = '" . $this->db->escape($historyDataArray['date_added']) . "' 
                WHERE customer_history_id = '" . $customer_history_id . "'"); 

            return true; 
        } else {
            // Insert new customer history
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_history SET 
                customer_id = '" . $customer_id . "', 
                comment = '" . $this->db->escape($historyDataArray['comment']) . "', 
                date_added = '" . $this->db->escape($historyDataArray['date_added']) . "'");

            return true; 
        }

        return false; 
    }

    public function importTransaction($transactionDataArray) {
        $customer_id = (int)$transactionDataArray['customer_id'];
        $customer_transaction_id = (int)$transactionDataArray['customer_transaction_id'];

        // Check if customer_transaction exists
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_transaction WHERE customer_transaction_id = '" . $customer_transaction_id . "'");

        if ($query->num_rows > 0) {
            // Update existing customer_transaction
            $this->db->query("UPDATE " . DB_PREFIX . "customer_transaction SET 
                description = '" . $this->db->escape($transactionDataArray['description']) . "', 
                amount = '" . (int)$transactionDataArray['amount'] . "', 
                date_added = '" . $this->db->escape($transactionDataArray['date_added']) . "' 
                WHERE customer_transaction_id = '" . $customer_transaction_id . "'"); 

           return true; 

        } else {
            // Insert new customer_transaction
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET 
                customer_id = '" . $customer_id . "', 
                description = '" . $this->db->escape($transactionDataArray['description']) . "', 
                amount = '" . (int)$transactionDataArray['amount'] . "', 
                date_added = '" . $this->db->escape($transactionDataArray['date_added']) . "'");

            return true; 

        }

        return false;
    }

    public function importReward($rewardDataArray) {
        $customer_id = (int)$rewardDataArray['customer_id'];
        $customer_reward_id = (int)$rewardDataArray['customer_reward_id']; // Corrected variable name

        // Check if customer reward exists
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_reward WHERE customer_reward_id = '" . $customer_reward_id . "'");

        if ($query->num_rows > 0) {
            // Update existing customer reward
            $this->db->query("UPDATE " . DB_PREFIX . "customer_reward SET 
                description = '" . $this->db->escape($rewardDataArray['description']) . "', 
                points = '" . (int)$rewardDataArray['points'] . "', 
                date_added = '" . $this->db->escape($rewardDataArray['date_added']) . "' 
                WHERE customer_reward_id = '" . $customer_reward_id . "'"); // Corrected condition

            return true;
        } else {
            // Insert new customer reward
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET 
                customer_id = '" . $customer_id . "', 
                description = '" . $this->db->escape($rewardDataArray['description']) . "', 
                points = '" . (int)$rewardDataArray['points'] . "', 
                date_added = '" . $this->db->escape($rewardDataArray['date_added']) . "'");

            return true;
        }

        return false;

    }
   public function importIp($ipDataArray) {
        $customer_id = (int)$ipDataArray['customer_id'];
        
        // Check if the customer already has an IP entry
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . $customer_id . "'");
        if ($query->num_rows == 0) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET 
                customer_id = '" . $customer_id . "', 
                ip = '" . $this->db->escape($ipDataArray['ip']) . "', 
                date_added = '" . $this->db->escape($ipDataArray['date_added']) . "'");
            return true; 
        }

        // If an entry already exists, do not insert and return false
        return false; 
    }
    
    
    public function getAllCustomers() {
        $query = $this->db->query("
            SELECT c.*, a.address_id, a.company, a.address_1, a.address_2, a.city, a.postcode, a.country_id, a.zone_id
            FROM " . DB_PREFIX . "customer c
            LEFT JOIN " . DB_PREFIX . "address a ON c.customer_id = a.customer_id
        ");
        
        return $query->rows;
    }

    public function getAllAffiliates() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_affiliate");
        return $query->rows;
    }
    public function getAllHistory() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_history");
        return $query->rows;
        var_dump($query);DIE();
    }
    public function getAlltransaction() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_transaction");
        return $query->rows;
    }
    public function getAllreward() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_reward");
        return $query->rows;
    }
    public function getAllip() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip");
        return $query->rows;
    }
 
    
    public function getZonebyname($zone) {
        $query = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE name = '" . $this->db->escape($zone) . "'");
        return $query->row['zone_id'];
    }
    
    public function getCountrybyname($country) {
        $query = $this->db->query("SELECT country_id FROM " . DB_PREFIX . "country WHERE name = '" . $this->db->escape($country) . "'");
        return $query->row['country_id'];
    }
}