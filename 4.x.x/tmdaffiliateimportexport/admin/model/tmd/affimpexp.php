<?php
namespace Opencart\Admin\Model\Extension\tmdaffiliateimportexport\Tmd;
    use \Opencart\System\Helper as Helper;
    class Affimpexp extends \Opencart\System\Engine\Model {   

        public function importCustomer($data) {
            $customer_id = (int)$data['customer_id'];

            // Check if customer exists
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $customer_id . "'");

            if ($query->num_rows > 0) {
                // Prepare customer update query
                $this->db->query("UPDATE " . DB_PREFIX . "customer SET 
                    customer_group_id = '" . (int)$data['customer_group_id'] . "', 
                    firstname = '" . $this->db->escape($data['firstname']?? '') . "', 
                    lastname = '" . $this->db->escape($data['lastname']?? '') . "', 
                    email = '" . $this->db->escape($data['email']?? '') . "', 
                    telephone = '" . $this->db->escape($data['telephone']?? '') . "', 
                    newsletter = '" . $this->db->escape($data['newsletter']?? '') . "', 
                    status = '" . (int)$data['status'] . "',
                    safe = '" . (int)$data['safe'] . "', 
                    date_added = '" . $this->db->escape($data['date_added']?? '') . "' 
                    WHERE customer_id = '" . $customer_id . "'");

                return true; // Indicate success
            } else {
                // Customer does not exist, insert new customer
                $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET 
                    customer_id = '" . (int)$data['customer_id'] . "',
                    customer_group_id = '" . (int)$data['customer_group_id'] . "', 
                    firstname = '" . $this->db->escape($data['firstname']?? '') . "', 
                    lastname = '" . $this->db->escape($data['lastname']?? '') . "', 
                    email = '" . $this->db->escape($data['email']?? '') . "', 
                    telephone = '" . $this->db->escape($data['telephone']?? '') . "', 
                    newsletter = '" . $this->db->escape($data['newsletter']?? '') . "', 
                    ip = '" . $this->db->escape($data['ip']?? '') . "', 
                    status = '" . (int)$data['status'] . "',
                    safe = '" . (int)$data['safe'] . "', 
                    date_added = '" . $this->db->escape($data['date_added']?? '') . "'");

                return true; // Indicate success
            }

            return false; 
        }
    public function importCustomerAddress($addressdataArray) {


        $customer_id = (int)$addressdataArray['customer_id'];
        $address_id = (int)$addressdataArray['address_id'];

        // Check if the customer already has an address
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . $customer_id . "'");

        if ($query->num_rows > 0) {
            // Update existing address
            $this->db->query("UPDATE " . DB_PREFIX . "address SET 
                    firstname = '" . $this->db->escape($addressdataArray['firstname']?? '') . "', 
                    lastname = '" . $this->db->escape($addressdataArray['lastname']?? '') . "', 
                    company = '" . $this->db->escape($addressdataArray['company']?? '') . "', 
                    address_1 = '" . $this->db->escape($addressdataArray['address_1']?? '') . "', 
                    address_2 = '" . $this->db->escape($addressdataArray['address_2']?? '') . "', 
                    city = '" . $this->db->escape($addressdataArray['city']?? '') . "', 
                    postcode = '" . $this->db->escape($addressdataArray['postcode']?? '') . "', 
                    country_id = '" . $this->db->escape($addressdataArray['country_id']?? '') . "', 
                    zone_id = '" . $this->db->escape($addressdataArray['zone_id']?? '') . "'
                    WHERE address_id = '" . $address_id . "'");

            return $this->db->countAffected(); // Return number of affected rows
        } else {
            // Insert new address
            $this->db->query("INSERT INTO " . DB_PREFIX . "address SET 
                customer_id = '" . $customer_id . "', 
                    firstname = '" . $this->db->escape($addressdataArray['firstname']?? '') . "', 
                    lastname = '" . $this->db->escape($addressdataArray['lastname']?? '') . "', 
                    company = '" . $this->db->escape($addressdataArray['company']?? '') . "', 
                    address_1 = '" . $this->db->escape($addressdataArray['address_1']?? '') . "', 
                    address_2 = '" . $this->db->escape($addressdataArray['address_2']?? '') . "', 
                    city = '" . $this->db->escape($addressdataArray['city']?? '') . "', 
                    postcode = '" . $this->db->escape($addressdataArray['postcode']?? '') . "', 
                    country_id = '" . $this->db->escape($addressdataArray['country_id']?? '') . "', 
                    zone_id = '" . $this->db->escape($addressdataArray['zone_id']?? '') . "'");

            return $this->db->countAffected();
        }

        return false; // Fallback return
    }
    public function importCustomerAffiliate($affiliateDataArray) {


    if(!empty($affiliateDataArray['payment'])){
           $payment = $affiliateDataArray['payment'];

       }else{
           $payment ='cheque';
       }

        $customer_id = (int)$affiliateDataArray['customer_id'];

        // Check if customer affiliate exists
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_affiliate WHERE customer_id = '" . $customer_id . "'");



        if ($query->num_rows > 0) {
            if(!empty($affiliateDataArray['date_added'])){
                 $date_added = $affiliateDataArray['date_added'];
            }else{
                $date_added = date("Y-m-d");
            }
       

       if(!empty($affiliateDataArray['payment'])){
           $payment = $affiliateDataArray['payment'];

       }else{
           $payment ='cheque';
       }

            $this->db->query("UPDATE " . DB_PREFIX . "customer_affiliate SET 
            company = '" . $affiliateDataArray['company'] . "', 
            website = '" . $affiliateDataArray['website'] . "', 
            tracking = '" . $this->db->escape($affiliateDataArray['tracking']?? '') . "', 
            commission = '" . (float)$affiliateDataArray['commission'] . "', 
            tax = '" . (float)$affiliateDataArray['tax'] . "', 
            payment_method = '" .$payment . "', 
            cheque = '" . $this->db->escape($affiliateDataArray['cheque']?? '') . "', 
            status = '" . (int)$affiliateDataArray['status'] . "', 
            date_added = '" . $this->db->escape($date_added) . "' 
            WHERE customer_id = '" . $customer_id . "'"); 
            return true; 
        }else {
            // Insert new customer affiliate
            if(!empty($affiliateDataArray['payment'])){
               $payment = $affiliateDataArray['payment'];
            }else{
               $payment ='cheque';
            }


        if(!empty($affiliateDataArray['date_added'])){
                 $date_added = $affiliateDataArray['date_added'];
            }else{
                $date_added = date("Y-m-d");
            }
       
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_affiliate SET 
                customer_id = '" . $customer_id . "', 
                company = '" . $this->db->escape($affiliateDataArray['company']?? '') . "', 
                website = '" . $this->db->escape($affiliateDataArray['website']?? '') . "', 
                tracking = '" . $this->db->escape($affiliateDataArray['tracking']?? '') . "', 
                commission = '" . (float)$affiliateDataArray['commission'] . "', 
                tax = '" . (float)$affiliateDataArray['tax'] . "', 
                payment_method = '" . $this->db->escape($payment) . "', 
                cheque = '" . $this->db->escape($affiliateDataArray['cheque']?? '') . "', 
                status = '" . (int)$affiliateDataArray['status'] . "', 
                date_added = '" . $this->db->escape($date_added) . "'");

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
                comment = '" . $this->db->escape($historyDataArray['comment']?? '') . "', 
                date_added = '" . $this->db->escape($historyDataArray['date_added']?? '') . "' 
                WHERE customer_history_id = '" . $customer_history_id . "'"); 

            return true; 
        } else {
            // Insert new customer history
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_history SET 
                customer_id = '" . $customer_id . "', 
                comment = '" . $this->db->escape($historyDataArray['comment']?? '') . "', 
                date_added = '" . $this->db->escape($historyDataArray['date_added']?? '') . "'");

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
                description = '" . $this->db->escape($transactionDataArray['description']?? '') . "', 
                amount = '" . (int)$transactionDataArray['amount'] . "', 
                date_added = '" . $this->db->escape($transactionDataArray['date_added']?? '') . "' 
                WHERE customer_transaction_id = '" . $customer_transaction_id . "'"); 

           return true; 

        } else {
            // Insert new customer_transaction
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET 
                customer_id = '" . $customer_id . "', 
                description = '" . $this->db->escape($transactionDataArray['description']?? '') . "', 
                amount = '" . (int)$transactionDataArray['amount'] . "', 
                date_added = '" . $this->db->escape($transactionDataArray['date_added']?? '') . "'");

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
                description = '" . $this->db->escape($rewardDataArray['description']?? '') . "', 
                points = '" . (int)$rewardDataArray['points'] . "', 
                date_added = '" . $this->db->escape($rewardDataArray['date_added']?? '') . "' 
                WHERE customer_reward_id = '" . $customer_reward_id . "'"); // Corrected condition

            return true;
        } else {
            // Insert new customer reward
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET 
                customer_id = '" . $customer_id . "', 
                description = '" . $this->db->escape($rewardDataArray['description']?? '') . "', 
                points = '" . (int)$rewardDataArray['points'] . "', 
                date_added = '" . $this->db->escape($rewardDataArray['date_added']?? '') . "'");

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
        $query = $this->db->query("SELECT  c.*, a.* FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "address a ON c.customer_id = a.customer_id");
        if(empty($query->rows['customer_id'])){
          $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id <> 0");
        }
        return $query->rows;
    }

    public function getAllAffiliates() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_affiliate");
        return $query->rows;
    }

    public function getAllAddress() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address");
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