<?php

include('dbcon/conn.php');

try{
	$curl_handle = curl_init();
	//$url = "http://mag243.local/index.php/rest/V1/eighteentech-order/post";
	$url = "https://mcstaging.offineeds.com/rest/V1/order/ordergrid"; 
    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $token = "tudrmljybp9z7pxmjs6rpek63g1gzd5j";
	$bodydata = '{"days":"20"}';

    $headers[] = "Authorization: Bearer ".$token;
    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
	
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $bodydata); 
    /* set return type json */
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_handle, CURLOPT_URL, $url);
	$curl_data = curl_exec($curl_handle);
	curl_close($curl_handle);

	$jsonData = json_decode(html_entity_decode($curl_data),true);

	$responseArray = json_decode($jsonData,true);
	$status = 0;

	foreach ($responseArray as $data) {
		$entity_id = $data['entity_id'];
		$status = $data['status'];
		$store_id = $data['store_id'];
		$store_name = $data['store_name'];
		$customer_id = $data['customer_id'];
		$base_grand_total = $data['base_grand_total'];
		$base_total_paid = $data['base_total_paid'];
		$grand_total = $data['grand_total'];
		$total_paid = $data['total_paid'];
		$increment_id = $data['increment_id'];
		$base_currency_code = $data['base_currency_code'];
		$order_currency_code = $data['order_currency_code'];
		$shipping_name = $data['shipping_name'];
		$billing_name = $data['billing_name'];
		$created_at = $data['created_at'];
		$updated_at = $data['updated_at'];
		$billing_address = addslashes($data['billing_address']);
		$shipping_address = addslashes($data['shipping_address']);
		$shipping_information = $data['shipping_information'];
		$customer_email = $data['customer_email'];
		$customer_group =$data ['customer_group'];
		$subtotal = $data['subtotal'];
		$shipping_and_handling = $data['shipping_and_handling'];
		$customer_name = $data['customer_name'];
		$payment_method = $data['payment_method'];
		$total_refunded = $data['total_refunded'];
		$pickup_location_code = $data['pickup_location_code'];
		$parent_order_id = $data['parent_order_id'];
		$order_batch_no = $data['order_batch_no'];

		  $entityQry = "SELECT main_entity_id FROM `sales_order_grid` WHERE main_entity_id = $entity_id limit 1";
		 $result = $conn->query($entityQry);
		 $row= mysqli_fetch_row($result);
		
		if((int)$row['0'] != $entity_id) {
			$sql = "INSERT INTO `sales_order_grid` (`main_entity_id`, `status`, `store_id`, `store_name`, `customer_id`, `base_grand_total`, `base_total_paid`, `grand_total`, `total_paid`, `increment_id`, `base_currency_code`, `order_currency_code`, `shipping_name`, `billing_name`, `created_at`, `updated_at`, `billing_address`, `shipping_address`, `shipping_information`, `customer_email`, `customer_group`, `subtotal`, `shipping_and_handling`, `customer_name`, `payment_method`, `total_refunded`, `pickup_location_code`, `parent_order_id`, `order_batch_no`) values
			('".$entity_id."', '".$status."', '".$store_id."', 
			'".$store_name."', '".(int)$customer_id."', '".$base_grand_total."', '".(float) $base_total_paid."', '".$grand_total."', '".(float)$total_paid."', '".$increment_id."', '".$base_currency_code."', '".$order_currency_code."', '".$shipping_name."', '".$billing_name."', '".$created_at."', '".$updated_at."', '".$billing_address."', '".$shipping_address."', '".$shipping_information."', '".$customer_email."', '".$customer_group."', '".$subtotal."' , '".$shipping_and_handling."', '".$customer_name."', '".$payment_method."', '".(float)$total_refunded."', '".$pickup_location_code."', '".$parent_order_id."', '".$order_batch_no."')";

			mysqli_query($conn, $sql) or die('Error: " . $sql . "<br>" . mysqli_error($conn)');
			$status = 1;
			
		}else{
	
			$sqlupdate = "UPDATE `sales_order_grid` SET 
			`main_entity_id`='".$entity_id."',`status`='".$status."',`store_id`='".$store_id."',`store_name`='".$store_name."', `customer_id`='".(int)$customer_id."',`base_grand_total`='".$base_grand_total."',`base_total_paid`='".(float) $base_total_paid."',`grand_total`='".$grand_total."',`total_paid`='".(float)$total_paid."',`increment_id`='".$increment_id."',`base_currency_code`='".$base_currency_code."',`order_currency_code`='".$order_currency_code."',`shipping_name`='".$shipping_name."',`billing_name`='".$billing_name."',`created_at`='".$created_at."',`updated_at`='".$updated_at."',`billing_address`='".$billing_address."',`shipping_address`='".$shipping_address."',`shipping_information`='".$shipping_information."',`customer_email`='".$customer_email."',`customer_group`='".$customer_group."',`subtotal`='".$subtotal."',`shipping_and_handling`='".$shipping_and_handling."',`customer_name`='".$customer_name."',`payment_method`='".$payment_method."',`total_refunded`='".(float)$total_refunded."',`pickup_location_code`='".$pickup_location_code."',`parent_order_id`='".$parent_order_id."',`order_batch_no`='fad' WHERE `main_entity_id` = $entity_id";
			mysqli_query($conn, $sqlupdate) or die('Error: " . $sql . "<br>" . mysqli_error($conn)');
			$status = 1;
		}       
					
	}
	if($status != 0){
		echo "<h2>New record created successfully</h2>";
	}
	mysqli_close($conn);

} catch(Exception $e) {
   echo 'Message: ' .$e->getMessage();
}
