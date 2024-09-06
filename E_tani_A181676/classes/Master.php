<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_shop_type(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		
		$check = $this->conn->query("SELECT * FROM `shop_type_list` where `name` = '{$name}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Jenis Kedai sudah wujud.";
		}else{
			if(empty($id)){
				$sql = "INSERT INTO `shop_type_list` set {$data} ";
			}else{
				$sql = "UPDATE `shop_type_list` set {$data} where id = '{$id}' ";
			}
			$save = $this->conn->query($sql);
			if($save){
				$resp['status'] = 'success';
				if(empty($id))
				$resp['msg'] = " Jenis Kedai Baharu berjaya disimpan.";
				else
				$resp['msg'] = " Jenis Kedai berjaya dikemas kini.";
			}else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_shop_type(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `shop_type_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Jenis Kedai berjaya dipadamkan.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' and vendor_id = '{$vendor_id}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = " Kategori sudah wujud.";
		}else{
			if(empty($id)){
				$sql = "INSERT INTO `category_list` set {$data} ";
			}else{
				$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
			}
			$save = $this->conn->query($sql);
			if($save){
				$resp['status'] = 'success';
				if(empty($id))
				$resp['msg'] = " Kategori Baharu berjaya disimpan.";
				else
				$resp['msg'] = " Kategori berjaya dikemas kini.";
			}else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Kategori berjaya dipadamkan.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_product(){
		$_POST['description'] = htmlentities($_POST['description']);
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `product_list` where vendor_id = '{$vendor_id}' and `name` = '{$name}' and delete_flag = 0 ".(!empty($id) ? " and id != '{$id}'" : ""))->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = ' Nama Produk Sudah wujud.';
		}else{
			if(empty($id)){
				$sql = "INSERT INTO `product_list` set {$data} ";
			}else{
				$sql = "UPDATE `product_list` set {$data} where id = '{$id}' ";
			}
			$save = $this->conn->query($sql);
			if($save){
				$pid = empty($id) ? $this->conn->insert_id : $id;
				$resp['pid'] = $pid;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = " Produk Baharu berjaya disimpan.";
				else
					$resp['msg'] = " Produk berjaya dikemas kini.";
				
				if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
					if(!is_dir(base_app."uploads/products"))
					mkdir(base_app."uploads/products");
					$fname = 'uploads/products/'.($pid).'.png';
					$dir_path =base_app. $fname;
					$upload = $_FILES['img']['tmp_name'];
					$type = mime_content_type($upload);
					$allowed = array('image/png','image/jpeg');
					if(!in_array($type,$allowed)){
						$resp['msg']=" Tetapi Imej gagal dimuat naik kerana jenis fail tidak sah.";
					}else{
						
				
						list($width, $height) = getimagesize($upload);
						$new_height = $height; 
						$new_width = $width; 
						$t_image = imagecreatetruecolor($new_width, $new_height);
						imagealphablending( $t_image, false );
						imagesavealpha( $t_image, true );
						$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
						imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						if($gdImg){
								if(is_file($dir_path))
								unlink($dir_path);
								$uploaded_img = imagepng($t_image,$dir_path);
								imagedestroy($gdImg);
								imagedestroy($t_image);
								if(isset($uploaded_img) && $uploaded_img == true){
									$qry = $this->conn->query("UPDATE `product_list` set image_path = concat('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '$pid' ");
								}
						}else{
						$resp['msg']=" Tetapi Imej gagal dimuat naik atas sebab yang tidak diketahui.";
						}
					}
					
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id))
					$resp['msg'] = " Produk gagal disimpan.";
				else
					$resp['msg'] = " Produk gagal dikemas kini.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}

		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_product(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `product_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Produk berjaya dipadamkan.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function add_to_cart(){
		$_POST['client_id'] = $this->settings->userdata('id');
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM cart_list where product_id = '{$product_id}' && client_id = '{$client_id}'")->num_rows;
		if($check > 0){
			$sql = "UPDATE cart_list set quantity = quantity + {$quantity} where product_id = '{$product_id}' && client_id = '{$client_id}' ";
		}else{
			$sql = "INSERT INTO cart_list set {$data}";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$resp['msg'] = " Produk telah ditambahkan pada bakul.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = " Produk telah gagal ditambahkan pada bakul.";
			$resp['error'] = $this->conn->error. "[{$sql}]";
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function update_cart_qty(){
		extract($_POST);
		$update_cart = $this->conn->query("UPDATE `cart_list` set `quantity` = '{$quantity}' where id = '{$cart_id}'");
		if($update_cart){
			$resp['status'] = 'success';
			$resp['msg'] = ' Kuantiti Produk telah berjaya dikemas kini';
		}else{
			$resp['status'] = 'success';
			$resp['msg'] = ' Kuantiti Produk gagal dikemas kini';
			$resp['error'] = $this->conn->error;
		}
		
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_cart(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `cart_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = " Item Bakul telah berjaya dipadamkan.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = " Item Bakul telah gagal dipadamkan.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] =='success'){
			$this->settings->set_flashdata('success',$resp['msg']);
		}
		return json_encode($resp);
	}
	function place_order(){
		extract($_POST);
		$inserted=[];
		$has_failed=false;
		$gtotal = 0;
		$vendors = $this->conn->query("SELECT * FROM `vendor_list` where id in (SELECT vendor_id from product_list where id in (SELECT product_id FROM `cart_list` where client_id ='{$this->settings->userdata('id')}')) order by `shop_name` asc");
		$prefix = date('Ym-');
		$code = sprintf("%'.05d",1);
		while($vrow = $vendors->fetch_assoc()):
			$data = "";
			while(true){
				$check = $this->conn->query("SELECT * FROM order_list where code = '{$prefix}{$code}' ")->num_rows;
				if($check > 0){
					$code = sprintf("%'.05d",ceil($code) + 1);
				}else{
					break;
				}
			}
			$ref_code = $prefix.$code;
			$data = "('{$ref_code}','{$this->settings->userdata('id')}','{$vrow['id']}','{$this->conn->real_escape_string($delivery_address)}')";
			$sql = "INSERT INTO `order_list` (`code`,`client_id`,`vendor_id`,`delivery_address`) VALUES {$data}";
			$save = $this->conn->query($sql);
			if($save){
				$oid = $this->conn->insert_id;
				$inserted[] = $oid;
				$data = "";
				$gtotal = 0 ;
				$products = $this->conn->query("SELECT c.*, p.name as `name`, p.price,p.image_path FROM `cart_list` c inner join product_list p on c.product_id = p.id where c.client_id = '{$this->settings->userdata('id')}' and p.vendor_id = '{$vrow['id']}' order by p.name asc");
				while($prow = $products->fetch_assoc()):
					$total = $prow['price'] * $prow['quantity'];
					$gtotal += $total;
					if(!empty($data)) $data .= ", ";
						$data .= "('{$oid}', '{$prow['product_id']}', '{$prow['quantity']}', '{$prow['price']}')";
				endwhile;
				$sql2 = "INSERT INTO `order_items` (`order_id`,`product_id`,`quantity`,`price`) VALUES {$data}";
				$save2= $this->conn->query($sql2);
				if($save2){
					$this->conn->query("UPDATE `order_list` set `total_amount` = '{$gtotal}' where id = '{$oid}'");
				}else{
					$has_failed = true;
					$resp['sql'] = $sql2;
					break;
				}
			}else{
				$has_failed = true;
				$resp['sql'] = $sql;
				break;
			}
		endwhile;
		if(!$has_failed){
			$resp['status'] = 'success';
			$resp['msg'] = " Tempahan telah dibuat";
			$this->conn->query("DELETE FROM `cart_list` where client_id ='{$this->settings->userdata('id')}'");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = " Tempahan gagal dibuat";
			$resp['error'] = $this->conn->error;
			if(count($inserted) > 0){
				$this->conn->query("DELETE FROM `order_list` where id in (".(implode(',',array_values($inserted))).") ");
			}
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);

		return json_encode($resp);
	}
	function cancel_order(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `order_list` set `status` = 5 where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = " Tempahan telah berjaya dibatalkan.";
		}else{
			$resp['status'] = 'success';
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function update_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `order_list` set `status` = '{$status}' where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = " Status Tempahan telah berjaya dikemas kini.";
		}else{
			$resp['status'] = 'success';
			$resp['msg'] = " Status Tempahan gagal dikemas kini.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}

	function save_feedy() {
		// Sanitize and validate user input
		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$id = isset($_POST['id']) ? $_POST['id'] : '';
		// ...
	
		// Construct the query using prepared statements
		$query = "SELECT * FROM `feedback` WHERE `title` = ? AND delete_flag = 0 " . (!empty($id) ? " AND id != ? " : "");
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("ss", $title, $id);
		$stmt->execute();
		$result = $stmt->get_result();
	
		// Check for duplicate feedback
		if ($result->num_rows > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Maklum balas sudah wujud.";
		} else {
			// Construct the insert or update query
			if (empty($id)) {
				$query = "INSERT INTO `feedback` SET {$data}";
				// ...
			} else {
				$query = "UPDATE `feedback` SET {$data} WHERE id = ?";
				// ...
			}
	
			// Execute the query using prepared statements
			$stmt = $this->conn->prepare($query);
			if (!empty($id)) {
				$stmt->bind_param("s", $id);
			}
			$save = $stmt->execute();
	
			// Check if the query was successful
			if ($save) {
				$resp['status'] = 'success';
				// ...
			} else {
				$resp['status'] = 'failed';
				$resp['err'] = $stmt->error . " [{$query}]";
			}
		}
	
		// Handle success and return response
		if ($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', $resp['msg']);
		}
		return json_encode($resp);
	}
	
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_shop_type':
		echo $Master->save_shop_type();
	break;
	case 'delete_shop_type':
		echo $Master->delete_shop_type();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_product':
		echo $Master->save_product();
	break;
	case 'delete_product':
		echo $Master->delete_product();
	break;
	case 'add_to_cart':
		echo $Master->add_to_cart();
	break;
	case 'update_cart_qty':
		echo $Master->update_cart_qty();
	break;
	case 'delete_cart':
		echo $Master->delete_cart();
	break;
	case 'place_order':
		echo $Master->place_order();
	break;
	case 'cancel_order':
		echo $Master->cancel_order();
	break;
	case 'update_status':
		echo $Master->update_status();
	break;
	case 'save_feedy':
		echo $Master->save_feedy();
	break;
	default:
		// echo $sysset->index();
		break;
}