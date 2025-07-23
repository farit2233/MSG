<?php
header('Content-Type: application/json');
ob_start();
error_reporting(0);

require_once(__DIR__ . '/../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('../config.php');
class Master extends DBConnection
{
	private $settings;
	public function __construct()
	{
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct()
	{
		parent::__destruct();
	}
	function capture_err()
	{
		if (!$this->conn->error)
			return false;
		else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function resize_image($src_path, $dest_path, $max_width, $max_height)
	{
		list($src_w, $src_h, $type) = getimagesize($src_path);

		$scale = min($max_width / $src_w, $max_height / $src_h);
		if ($scale >= 1) { // ภาพเล็กกว่าขนาด max ไม่ต้องย่อ
			return copy($src_path, $dest_path);
		}

		$new_w = floor($src_w * $scale);
		$new_h = floor($src_h * $scale);

		$dst_img = imagecreatetruecolor($new_w, $new_h);

		// สำหรับ PNG ให้รองรับ transparency
		if ($type == IMAGETYPE_PNG) {
			imagealphablending($dst_img, false);
			imagesavealpha($dst_img, true);
		}

		switch ($type) {
			case IMAGETYPE_JPEG:
				$src_img = imagecreatefromjpeg($src_path);
				break;
			case IMAGETYPE_PNG:
				$src_img = imagecreatefrompng($src_path);
				break;
			default:
				return false; // ไฟล์ประเภทอื่นไม่รองรับ
		}

		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);

		if ($type == IMAGETYPE_JPEG) {
			imagejpeg($dst_img, $dest_path, 85);
		} elseif ($type == IMAGETYPE_PNG) {
			imagepng($dst_img, $dest_path);
		}

		imagedestroy($src_img);
		imagedestroy($dst_img);

		return true;
	}
	function delete_img()
	{
		extract($_POST);
		if (is_file($path)) {
			if (unlink($path)) {
				$resp['status'] = 'success';
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete ' . $path;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown ' . $path . ' path';
		}
		return json_encode($resp);
	}

	function save_product_type()
	{
		$_POST['description'] = addslashes(htmlspecialchars($_POST['description']));
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) $data .= ",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `product_type` where `name` = '{$name}' and delete_flag = 0 " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "ประเภท already exists.";
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `product_type` set {$data} ";
		} else {
			$sql = "UPDATE `product_type` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if ($save) {
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = "New ประเภท successfully saved.";
			else
				$resp['msg'] = " ประเภท successfully updated.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function delete_product_type()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `product_type` set `delete_flag` = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " ประเภท successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function save_category()
	{
		$_POST['description'] = addslashes(htmlspecialchars($_POST['description']));
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) $data .= ",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' and delete_flag = 0 " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Category already exists.";
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `category_list` set {$data} ";
		} else {
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if ($save) {
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = "New Category successfully saved.";
			else
				$resp['msg'] = " Category successfully updated.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function delete_category()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set `delete_flag` = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Category successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_product()
	{

		// ตรวจสอบน้ำหนักก่อนบันทึก
		$max_weight_allowed = 25000; // กำหนดขีดจำกัดสูงสุดที่อนุญาต (กรัม)

		if (isset($_POST['product_weight']) && $_POST['product_weight'] > $max_weight_allowed) {
			return json_encode(['status' => 'failed', 'msg' => 'น้ำหนักสินค้าสูงเกินขีดจำกัดที่กำหนด']);
		}

		if (isset($_POST['description']))
			$_POST['description'] = addslashes(htmlspecialchars($_POST['description']));

		extract($_POST);

		$discount_type = (isset($_POST['discount_type']) && in_array($_POST['discount_type'], ['amount', 'percent'])) ? $_POST['discount_type'] : null;
		$discount_value = (isset($_POST['discount_value']) && $_POST['discount_value'] !== '' && is_numeric($_POST['discount_value'])) ? floatval($_POST['discount_value']) : null;
		$discounted_price = null;
		$_POST['slow_prepare'] = isset($_POST['slow_prepare']) ? 1 : 0;
		if ($discount_type !== null && $discount_value !== null) {
			if ($discount_type == 'amount') {
				$discounted_price = max(0, $price - $discount_value);
			} elseif ($discount_type == 'percent') {
				$discounted_price = max(0, $price - ($price * $discount_value / 100));
			}
		} else {
			$discount_value = null;
			$discounted_price = null;
		}

		// ให้แน่ใจว่า POST เหล่านี้ถูกส่งเข้า
		$_POST['discount_value'] = $discount_value;
		$_POST['discounted_price'] = $discounted_price;
		$_POST['discount_type'] = $discount_type;

		// รายการฟิลด์ที่ไม่ควรบันทึกลง product_list
		$skip_keys = ['id', 'extra_categories', 'shopee', 'lazada', 'tiktok'];

		$data = "";
		foreach ($_POST as $k => $v) {
			if (in_array($k, $skip_keys)) continue;
			if (is_array($v)) continue;

			// เช็คเฉพาะขนาดพัสดุ
			if (in_array($k, ['product_width', 'product_length', 'product_height'])) {
				if ($v === '' || $v === null || floatval($v) == 0) {
					$v = null;
				}
			}

			if (!empty($data)) $data .= ",";
			if (is_null($v)) {
				$data .= " `{$k}`=NULL ";
			} else {
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}


		// ตรวจสอบสินค้าซ้ำ
		$check = $this->conn->query("SELECT * FROM `product_list` where `brand` = '{$brand}' and `name` = '{$name}' and delete_flag = 0 " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err()) return $this->capture_err();
		if ($check > 0) {
			return json_encode(['status' => 'failed', 'msg' => "Product already exists."]);
		}

		// SQL บันทึก
		if (empty($id)) {
			$sql = "INSERT INTO `product_list` SET {$data}";
		} else {
			$sql = "UPDATE `product_list` SET {$data} WHERE id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		$product_id = !empty($id) ? $id : $this->conn->insert_id;

		if ($save) {
			$resp['pid'] = $product_id;
			$resp['status'] = 'success';
			$resp['msg'] = empty($id) ? 'Product has been added successfully' : 'Product has been updated successfully';

			// เซฟลิงก์
			$this->save_product_link($product_id);

			// อัปโหลดรูปภาพ
			if (!empty($_FILES['img']['tmp_name'])) {
				$img_path = "uploads/product/";
				if (!is_dir(base_app . $img_path)) {
					mkdir(base_app . $img_path, 0755, true);
				}
				$accept = ['image/jpeg', 'image/png', 'image/jpg'];
				if (!in_array($_FILES['img']['type'], $accept)) {
					$resp['msg'] .= " Image file type is invalid";
				} else {
					$filename = $_FILES['img']['name'];
					$spath = $img_path . $filename;
					$i = 1;
					while (is_file(base_app . $spath)) {
						$spath = $img_path . $i++ . '_' . $filename;
					}
					$success = $this->resize_image($_FILES['img']['tmp_name'], base_app . $spath, 1000, 1000);
					if ($success) {
						$this->conn->query("UPDATE product_list SET image_path = CONCAT('{$spath}', '?v=', UNIX_TIMESTAMP(CURRENT_TIMESTAMP)) WHERE id = '{$product_id}'");
					} else {
						$resp['msg'] .= " Failed to resize image.";
					}
				}
			}
		} else {
			return json_encode(['status' => 'failed', 'err' => $this->conn->error . " [{$sql}]"]);
		}



		// Flash message
		if ($resp['status'] == 'success' && isset($resp['msg']))
			$this->settings->set_flashdata('success', $resp['msg']);

		return json_encode($resp);
	}

	function save_product_link($product_id)
	{
		$shopee = $this->conn->real_escape_string(trim($_POST['shopee'] ?? ''));
		$lazada = $this->conn->real_escape_string(trim($_POST['lazada'] ?? ''));
		$tiktok = $this->conn->real_escape_string(trim($_POST['tiktok'] ?? ''));

		$sql = "INSERT INTO product_links 
				(product_id, shopee_url, lazada_url, tiktok_url)
			VALUES 
				($product_id, '$shopee', '$lazada', '$tiktok')
			ON DUPLICATE KEY UPDATE 
				shopee_url = VALUES(shopee_url),
				lazada_url = VALUES(lazada_url),
				tiktok_url = VALUES(tiktok_url),
				date_updated = CURRENT_TIMESTAMP";

		$this->conn->query($sql);
	}


	function delete_product()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `product_list` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Product successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_stock()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) $data .= ",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `stock_list` where `code` = '{$code}' " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Code is already taken.";
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `stock_list` set {$data} ";
		} else {
			$sql = "UPDATE `stock_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if ($save) {
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = "New Stock successfully saved.";
			else
				$resp['msg'] = " Stock successfully updated.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function delete_stock()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `stock_list` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Stock successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function add_to_cart()
	{
		extract($_POST);
		$qty = isset($qty) && $qty > 0 ? (int)$qty : 1;
		$customer_id = $this->settings->userdata('id');

		// คำนวณ stock ที่เหลือจริง
		$stock_qry = $this->conn->query("SELECT 
		COALESCE((SELECT SUM(quantity) FROM stock_list WHERE product_id = '{$product_id}'), 0) -
		COALESCE((SELECT SUM(quantity) FROM order_items WHERE product_id = '{$product_id}'), 0) 
		AS available");

		$available = 0;
		if ($stock_qry->num_rows > 0) {
			$available = (int)$stock_qry->fetch_assoc()['available'];
		}

		// คำนวณ max_order_qty ตาม available
		if ($available >= 100) {
			$max_order_qty = floor($available / 3);
		} elseif ($available >= 50) {
			$max_order_qty = floor($available / 2);
		} elseif ($available >= 30) {
			$max_order_qty = floor($available / 1.5);
		} else {
			$max_order_qty = $available;
		}

		// ดึงจำนวนที่มีในตะกร้าอยู่แล้ว
		$cart = $this->conn->query("SELECT quantity FROM cart_list WHERE customer_id = '{$customer_id}' AND product_id = '{$product_id}'");
		$cart_qty = 0;
		if ($cart->num_rows > 0) {
			$cart_qty = (int)$cart->fetch_assoc()['quantity'];
		}

		// ตรวจสอบรวมกับของที่มีในตะกร้าแล้ว
		$total_qty = $cart_qty + $qty;
		if ($total_qty > $max_order_qty) {
			echo json_encode([
				'status' => 'failed',
				'msg' => "คุณสามารถสั่งซื้อได้สูงสุด {$max_order_qty} ชิ้น (รวมของในตะกร้าด้วยแล้ว)"
			]);
			exit;
		}

		// ดำเนินการ update หรือ insert
		if ($cart_qty > 0) {
			// มีในตะกร้าอยู่แล้ว → อัปเดต
			$new_qty = $cart_qty + $qty;
			$update = $this->conn->query("UPDATE cart_list SET quantity = '{$new_qty}' WHERE customer_id = '{$customer_id}' AND product_id = '{$product_id}'");
			$resp['status'] = $update ? 'success' : 'failed';
			if (!$update) $resp['error'] = $this->conn->error;
		} else {
			// ยังไม่มี → insert
			$insert = $this->conn->query("INSERT INTO cart_list (customer_id, product_id, quantity) VALUES ('{$customer_id}', '{$product_id}', '{$qty}')");
			$resp['status'] = $insert ? 'success' : 'failed';
			if (!$insert) $resp['error'] = $this->conn->error;
		}

		if ($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', 'เพิ่มสินค้าในตะกร้าแล้ว');
		}

		echo json_encode($resp);
	}

	function update_cart()
	{
		extract($_POST);
		$update = $this->conn->query("UPDATE `cart_list` set quantity = '{$qty}' where id = '{$cart_id}'");
		if ($update) {
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		if ($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', 'ปรับจำนวนสินค้าในตะกร้าแล้ว');
		}
		return json_encode($resp);
	}
	function delete_cart()
	{
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `cart_list` where id = '{$id}'");
		if ($delete) {
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		if ($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', 'ลบสินค้าออกจากตะกร้าสำเร็จ');
		}
		return json_encode($resp);
	}

	function get_shipping_cost()
	{
		extract($_POST);
		$shipping_methods_id = isset($shipping_methods_id) ? intval($shipping_methods_id) : 0;
		$total_weight = isset($total_weight) ? floatval($total_weight) : 0;

		if ($shipping_methods_id <= 0 || $total_weight <= 0) {
			echo json_encode(['status' => 'error', 'msg' => 'ข้อมูลไม่ครบ']);
			return;
		}

		$qry = $this->conn->query("SELECT price FROM shipping_prices 
        WHERE shipping_methods_id = '{$shipping_methods_id}' 
        AND min_weight <= {$total_weight} 
        AND max_weight >= {$total_weight} 
        LIMIT 1");

		if ($qry && $qry->num_rows > 0) {
			$price = $qry->fetch_assoc()['price'];
			echo json_encode(['status' => 'success', 'price' => $price]);
		} else {
			echo json_encode(['status' => 'error', 'msg' => 'ไม่พบค่าขนส่งที่เหมาะสม']);
		}
	}

	function place_order()
	{
		extract($_POST);
		$customer_id = $this->settings->userdata('id');
		$pref = date("Ymd");
		$code = sprintf("%'.05d", 1);

		$this->conn->query("START TRANSACTION");

		try {
			while (true) {
				$check = $this->conn->query("SELECT id FROM `order_list` WHERE `code` = '{$pref}{$code}'")->num_rows;
				if ($check > 0) {
					$code = sprintf("%'.05d", abs($code) + 1);
				} else {
					$code = $pref . $code;
					break;
				}
			}

			$selected_ids = !empty($selected_items) ? array_filter(array_map('intval', explode(',', $selected_items))) : [];
			if (empty($selected_ids)) throw new Exception('ไม่มีรายการสินค้าสำหรับชำระสินค้า');
			$ids_str = implode(',', $selected_ids);

			$cart = $this->conn->query("
			SELECT 
				c.*, 
				p.name as product, 
				p.price,
				p.discount_type,
				p.discount_value,
				p.discounted_price
			FROM `cart_list` c 
			INNER JOIN product_list p ON c.product_id = p.id 
			WHERE c.id IN ($ids_str) AND c.customer_id = '{$customer_id}'
		");

			$backend_total = 0;
			$cart_data = [];
			while ($row = $cart->fetch_assoc()) {
				$original_price = $row['price'];
				if (!is_null($row['discounted_price'])) {
					$final_price = $row['discounted_price'];
				} elseif ($row['discount_type'] === 'amount') {
					$final_price = $original_price - $row['discount_value'];
				} elseif ($row['discount_type'] === 'percent') {
					$final_price = $original_price - ($original_price * $row['discount_value'] / 100);
				} else {
					$final_price = $original_price;
				}

				$row['final_price'] = $final_price;
				$backend_total += $final_price * $row['quantity'];
				$cart_data[] = $row;
			}

			if (empty($cart_data)) throw new Exception('ไม่พบรายการสินค้าที่ตรงกันในตะกร้า');

			$shipping_cost = isset($_POST['shipping_cost']) ? floatval($_POST['shipping_cost']) : 0;
			$shipping_methods_id = isset($_POST['shipping_methods_id']) ? intval($_POST['shipping_methods_id']) : 'NULL';
			$grand_total = $backend_total + $shipping_cost;

			if (round($total_amount, 2) != round($grand_total, 2)) {
				throw new Exception('ยอดรวมสินค้า + ค่าส่งไม่ตรงกัน');
			}

			$customer = $this->conn->query("SELECT * FROM customer_list WHERE id = '{$customer_id}'")->fetch_assoc();
			$customer_name = trim("{$customer['firstname']} {$customer['middlename']} {$customer['lastname']}");
			$delivery_address = $this->conn->real_escape_string($delivery_address);
			$shipping_methods = $this->conn->query("SELECT * FROM shipping_methods WHERE id = '{$shipping_methods_id}'")->fetch_assoc();
			$shipping_methods_name = trim("{$shipping_methods['name']}");
			// 📦 ดึงชื่อขนส่งจาก shipping_methods
			$shipping_methods_name = 'ไม่ระบุ';
			if (!empty($shipping_methods_id)) {
				$res = $this->conn->query("SELECT name, cost FROM shipping_methods WHERE id = {$shipping_methods_id}");
				if ($res->num_rows > 0) {
					$ship = $res->fetch_assoc();
					$shipping_methods_name = $ship['name'] . ' (' . number_format($shipping_cost, 2) . ' บาท)';
				}
			}

			$insert = $this->conn->query("INSERT INTO `order_list` 
		(`code`, `customer_id`, `delivery_address`, `total_amount`, `shipping_methods_id`, `status`, `payment_status`, `delivery_status`, `date_created`, `date_updated`) 
		VALUES 
		('{$code}', '{$customer_id}', '{$delivery_address}', '{$grand_total}', {$shipping_methods_id}, 0, 0, 0, NOW(), NOW())");

			if (!$insert) throw new Exception('ไม่สามารถสร้างคำสั่งซื้อได้: ' . $this->conn->error);

			$oid = $this->conn->insert_id;

			$data = "";
			foreach ($cart_data as $row) {
				if (!empty($data)) $data .= ", ";
				$product_id = intval($row['product_id']);
				$quantity = intval($row['quantity']);
				$price = floatval($row['final_price']);
				$data .= "('{$oid}', '{$product_id}', '{$quantity}', '{$price}')";
			}

			$save = $this->conn->query("INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES {$data}");
			if (!$save) throw new Exception('ไม่สามารถบันทึกรายการสินค้า: ' . $this->conn->error);

			$this->conn->query("DELETE FROM `cart_list` WHERE customer_id = '{$customer_id}' AND id IN ($ids_str)");

			$this->conn->query("COMMIT");

			$items = $this->conn->query("SELECT oi.*, p.name 
		FROM order_items oi 
		INNER JOIN product_list p ON oi.product_id = p.id 
		WHERE oi.order_id = {$oid}");

			$mail = new PHPMailer(true);
			try {
				//SMTP Setting
				$mail->isSMTP();
				//$mail->Host = 'localhost';
				//$mail->Port = 1025;
				//$mail->SMTPAuth = false;


				$mail->Host = 'smtp.gmail.com';
				$mail->Port = 465;
				$mail->SMTPAuth = true;
				$mail->Username = "faritre5566@gmail.com";
				$mail->Password = "bchljhaxoqflmbys";
				$mail->SMTPSecure = "ssl";

				$mail->CharSet = 'UTF-8';
				//Email Setting
				$mail->isHTML(true);
				$mail->Subject = "📦 ยืนยันคำสั่งซื้อ #$code";

				$mail->setFrom('faritre5566@gmail.com', 'MSG.com');
				$mail->addAddress($customer['email'], $customer_name);



				$body = "
				<div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto;'>
				<h2 style='color: #16542b; text-align:center;'>🧾 ยืนยันคำสั่งซื้อ</h2>
				<p>เรียนคุณ <strong>{$customer_name}</strong>,</p>
				<p>ขอบคุณสำหรับการสั่งซื้อกับร้านของเรา</p>
				<p><strong>รหัสคำสั่งซื้อ:</strong> $code</p>
				<p><strong>ขนส่ง:</strong> {$shipping_methods_name}</p>
				<table style='width:100%; border-collapse: collapse; margin-top:10px;'>
				<thead style='background:#16542b; color:white;'>
				<tr>
					<th style='padding:8px; border:1px solid #ddd;'>สินค้า</th>
					<th style='padding:8px; border:1px solid #ddd;'>จำนวน</th>
					<th style='padding:8px; border:1px solid #ddd;'>ราคาต่อชิ้น</th>
					<th style='padding:8px; border:1px solid #ddd;'>รวม</th>
				</tr>
				</thead>
				<tbody>";

				while ($row = $items->fetch_assoc()) {
					$subtotal = $row['price'] * $row['quantity'];
					$body .= "
				<tr>
				<td style='padding:8px; border:1px solid #ddd;'>{$row['name']}</td>
				<td style='padding:8px; border:1px solid #ddd; text-align:center;'>{$row['quantity']}</td>
				<td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($row['price'], 2) . "</td>
				<td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($subtotal, 2) . "</td>
				</tr>";
				}

				$body .= "
				<tr>
					<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'><strong>ค่าส่ง</strong></td>
					<td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($shipping_cost, 2) . "</td>
				</tr>
				<tr>
					<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'><strong>รวมทั้งสิ้น</strong></td>
					<td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($grand_total, 2) . "</td>
				</tr>
				</tbody></table>
			<p style='margin-top:20px;'>📦 จัดส่งไปที่ <br><div style='background:#f9f9f9; padding:10px; border:1px dashed #ccc;'>{$delivery_address}</div></p>
			<p>หากคุณมีคำถามเพิ่มเติม กรุณาติดต่อที่ <a href='mailto:faritre5566@gmail.com'>faritre5566@gmail.com</a></p>
			</div>";

				$mail->Body = $body;
				$mail->send();
			} catch (Exception $e) {
				error_log("❌ ส่งอีเมลไม่สำเร็จ: " . $mail->ErrorInfo);
			}

			// ฟังก์ชันส่งอีเมลให้แอดมิน
			$mail_admin = new PHPMailer(true);
			try {
				// SMTP Setting
				$mail_admin->isSMTP();
				$mail_admin->Host = 'smtp.gmail.com';
				$mail_admin->Port = 465;
				$mail_admin->SMTPAuth = true;
				$mail_admin->Username = "faritre5566@gmail.com";  // ใช้อีเมลของคุณ
				$mail_admin->Password = "bchljhaxoqflmbys";  // รหัสผ่าน SMTP
				$mail_admin->SMTPSecure = "ssl";

				$mail_admin->CharSet = 'UTF-8';
				$mail_admin->isHTML(true);
				$mail_admin->Subject = "📦 คำสั่งซื้อใหม่จากลูกค้า #$code";

				// ตั้งค่าอีเมลผู้ส่งและผู้รับ
				$mail_admin->setFrom('faritre5566@gmail.com', 'MSG.com');
				$mail_admin->addAddress('faritre5566@gmail.com', 'Admin');  // อีเมลแอดมิน
				$mail_admin->addAddress('faritre1@gmail.com', 'Admin');
				$mail_admin->addAddress('faritre4@gmail.com', 'Admin');

				// สร้างเนื้อหาของอีเมล
				$admin_body = "
    <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto;'>
        <h2 style='color: #16542b; text-align:center;'>🧾 คำสั่งซื้อใหม่</h2>
        <p><strong>รหัสคำสั่งซื้อ:</strong> $code</p>
        <p><strong>ลูกค้า:</strong> $customer_name</p>
        <p><strong>ที่อยู่จัดส่ง:</strong> {$delivery_address}</p>
        <p><strong>ยอดรวม:</strong> " . number_format($grand_total, 2) . " บาท</p>
        <p><strong>ขนส่ง:</strong> $shipping_methods_name</p>
        <h3>รายการสินค้า</h3>
        <table style='width:100%; border-collapse: collapse; margin-top:10px;'>
            <thead style='background:#16542b; color:white;'>
                <tr>
                    <th style='padding:8px; border:1px solid #ddd;'>สินค้า</th>
                    <th style='padding:8px; border:1px solid #ddd;'>จำนวน</th>
                    <th style='padding:8px; border:1px solid #ddd;'>ราคาต่อชิ้น</th>
                    <th style='padding:8px; border:1px solid #ddd;'>รวม</th>
                </tr>
            </thead>
            <tbody>";

				// รายการสินค้า
				$items = $this->conn->query("SELECT oi.*, p.name 
                                FROM order_items oi 
                                INNER JOIN product_list p ON oi.product_id = p.id 
                                WHERE oi.order_id = {$oid}");

				while ($row = $items->fetch_assoc()) {
					$subtotal = $row['price'] * $row['quantity'];
					$admin_body .= "
            <tr>
                <td style='padding:8px; border:1px solid #ddd;'>{$row['name']}</td>
                <td style='padding:8px; border:1px solid #ddd; text-align:center;'>{$row['quantity']}</td>
                <td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($row['price'], 2) . "</td>
                <td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($subtotal, 2) . "</td>
            </tr>";
				}

				$admin_body .= "
            <tr>
                <td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'><strong>ค่าส่ง</strong></td>
                <td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($shipping_cost, 2) . "</td>
            </tr>
            <tr>
                <td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'><strong>รวมทั้งสิ้น</strong></td>
                <td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($grand_total, 2) . "</td>
            </tr>
            </tbody></table>
        </div>";

				$mail_admin->Body = $admin_body;
				$mail_admin->send();
			} catch (Exception $e) {
				error_log("❌ ส่งอีเมลแอดมินไม่สำเร็จ: " . $mail_admin->ErrorInfo);
			}

			// ฟังก์ชันส่ง Telegram
			function sendTelegramNotification($message)
			{
				$bot_token = "8060343667:AAEK7rfDeBszjWOFkITO-wC7_YhMmQuILDk";  // ใช้ Bot Token ของคุณ
				$chat_id = "-4869854888";      // ใช้ Chat ID ของแอดมินหรือ Group

				$url = "https://api.telegram.org/bot$bot_token/sendMessage";

				$data = [
					'chat_id' => $chat_id,
					'text' => $message,
					'parse_mode' => 'HTML',  // ถ้าคุณต้องการให้ข้อความในรูปแบบ HTML
				];

				// ส่งข้อความด้วย cURL
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

				$response = curl_exec($ch);
				curl_close($ch);

				return $response;
			}

			// สร้างข้อความแจ้งเตือนสำหรับ Telegram
			$telegram_message = "
			📦 คำสั่งซื้อใหม่จากลูกค้า
			- รหัสคำสั่งซื้อ: $code
			- ลูกค้า: $customer_name
			- ที่อยู่จัดส่ง: $delivery_address
			- ยอดรวม: " . number_format($grand_total, 2) . " บาท
			- ขนส่ง: $shipping_methods_name

			รายละเอียดสินค้า:
			";

			// รายการสินค้า
			$items = $this->conn->query("SELECT oi.*, p.name 
                            FROM order_items oi 
                            INNER JOIN product_list p ON oi.product_id = p.id 
                            WHERE oi.order_id = {$oid}");

			while ($row = $items->fetch_assoc()) {
				$subtotal = $row['price'] * $row['quantity'];
				$telegram_message .= "
			- {$row['name']} x{$row['quantity']} = " . number_format($subtotal, 2) . " บาท
			";
			}
			$telegram_message .= "
			ค่าส่ง: " . number_format($shipping_cost, 2) . " บาท
			รวมทั้งสิ้น: " . number_format($grand_total, 2) . " บาท
			";
			// ส่งข้อความ Telegram
			sendTelegramNotification($telegram_message);

			$this->settings->set_flashdata('success', 'ชำระสินค้าสำเร็จ');
			$resp = ['status' => 'success'];
		} catch (Exception $e) {
			$this->conn->query("ROLLBACK");
			$resp = ['status' => 'failed', 'msg' => $e->getMessage()];
		}
		return json_encode($resp);
	}



	function update_order_status()
	{
		extract($_POST);

		$payment_status = isset($_POST['payment_status']) ? (int)$_POST['payment_status'] : 0;
		$delivery_status = isset($_POST['delivery_status']) ? (int)$_POST['delivery_status'] : 0;

		$update = $this->conn->query("UPDATE `order_list` 
        SET 
            `payment_status` = '{$payment_status}',
            `delivery_status` = '{$delivery_status}',
			`is_seen` = 0  
        WHERE id = '{$id}'");

		if ($update) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "อัปเดตสถานะเรียบร้อยแล้ว");
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error;
		}

		return json_encode($resp);
	}


	function delete_order()
	{
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `order_list` where id = '{$id}'");
		if ($delete) {
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		if ($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', 'Order has been deleted successfully.');
		}
		return json_encode($resp);
	}
	function save_inquiry()
	{
		$_POST['message'] = addslashes(htmlspecialchars($_POST['message']));
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) $data .= ",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if (empty($id)) {
			$sql = "INSERT INTO `inquiry_list` set {$data} ";
		} else {
			$sql = "UPDATE `inquiry_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if ($save) {
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if (empty($id))
				$this->settings->set_flashdata('success', " Your Inquiry has been sent successfully. Thank you!");
			else
				$this->settings->set_flashdata('success', " Inquiry successfully updated");
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_inquiry()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `inquiry_list` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Inquiry has been deleted successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_shipping()
	{
		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']['type'] != 1) {
			http_response_code(403);
			return json_encode(['status' => 'forbidden', 'message' => 'ไม่มีสิทธิ์ใช้งาน']);
		}

		extract($_POST);
		$id = isset($id) ? intval($id) : 0;

		// ป้องกัน SQL injection
		$provider_id = intval($provider_id ?? 0);
		$name = $this->conn->real_escape_string($display_name ?? '');
		$description = $this->conn->real_escape_string($description ?? '');
		$cost = floatval($cost ?? 0);
		$cod_enabled = ($_POST['cod_enabled'] == '1') ? 1 : 0;
		$is_active = ($_POST['is_active'] == '1') ? 1 : 0;

		// ตรวจสอบข้อมูลที่สำคัญ
		if (!$provider_id || !$name) {
			return json_encode(['status' => 'failed', 'msg' => 'กรุณากรอกข้อมูลให้ครบ']);
		}

		// SQL สำหรับการอัปเดตหรือเพิ่มข้อมูลใน shipping_methods
		if ($id > 0) {
			$sql = "UPDATE `shipping_methods` SET 
            provider_id = '{$provider_id}',
            name = '{$name}', 
            description = '{$description}',
            cost = '{$cost}',
            cod_enabled = '{$cod_enabled}',
            is_active = '{$is_active}'
            WHERE id = {$id}";
		} else {
			$sql = "INSERT INTO `shipping_methods` 
        (provider_id, name, description, cost, cod_enabled, is_active)
        VALUES 
        ('{$provider_id}', '{$name}', '{$description}', '{$cost}', '{$cod_enabled}', '{$is_active}')";
		}

		// บันทึกข้อมูลใน shipping_methods
		if ($this->conn->query($sql)) {
			$shipping_methods_id = ($id > 0) ? $id : $this->conn->insert_id; // กรณีเพิ่มใหม่หรือแก้ไข

			// ลบข้อมูลราคาจัดส่งเก่าในกรณีที่มีการแก้ไขข้อมูล
			if ($id > 0) {
				$this->conn->query("DELETE FROM `shipping_prices` WHERE shipping_methods_id = {$shipping_methods_id}");
			}

			// บันทึกข้อมูลราคาตามน้ำหนักใน shipping_prices
			if (isset($_POST['weight_from']) && isset($_POST['weight_to']) && isset($_POST['price'])) {
				$weight_from = $_POST['weight_from'];
				$weight_to = $_POST['weight_to'];
				$price = $_POST['price'];

				for ($i = 0; $i < count($weight_from); $i++) {
					$w_from = intval($weight_from[$i]);
					$w_to = intval($weight_to[$i]);
					$p = floatval($price[$i]);

					// ตรวจสอบความถูกต้องของน้ำหนักและราคา
					if ($w_from >= $w_to) {
						return json_encode(['status' => 'failed', 'msg' => 'น้ำหนักเริ่มต้นต้องน้อยกว่าหน้ำหนักสูงสุด']);
					}

					if ($p < 0) {
						return json_encode(['status' => 'failed', 'msg' => 'ราคาต้องไม่ติดลบ']);
					}

					// SQL สำหรับการบันทึกข้อมูลราคาตามน้ำหนัก
					$sql_price = "INSERT INTO shipping_prices (shipping_methods_id, min_weight, max_weight, price)
                              VALUES (?, ?, ?, ?)";
					$stmt_price = $this->conn->prepare($sql_price);
					$stmt_price->bind_param('iiid', $shipping_methods_id, $w_from, $w_to, $p);
					if (!$stmt_price->execute()) {
						return json_encode(['status' => 'failed', 'msg' => 'ไม่สามารถบันทึกข้อมูลราคาจัดส่งตามน้ำหนัก']);
					}
				}
			}

			// หากบันทึกสำเร็จ
			return json_encode([
				'status' => 'success',
				'provider_id' => $provider_id,
				'display_name' => $name,
				'description' => $description,
				'cost' => $cost,
				'cod_enabled' => $cod_enabled,
				'is_active' => $is_active
			]);
		} else {
			return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
		}
	}


	function delete_shipping()
	{
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `shipping_methods` where id = '{$id}'");
		if ($delete) {
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		if ($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', 'Order has been deleted successfully.');
		}
		return json_encode($resp);
	}
	function migrate_guest_cart()
	{
		$data = json_decode(file_get_contents('php://input'), true);
		$user_id = $this->settings->userdata('id') ?? 0;

		if ($user_id <= 0 || !isset($data['cart']) || !is_array($data['cart'])) {
			return json_encode([
				'status' => 'failed',
				'msg' => 'ข้อมูลไม่ถูกต้อง หรือไม่ได้เข้าสู่ระบบ'
			]);
		}

		foreach ($data['cart'] as $item) {
			$product_id = $item['id'];
			$qty = $item['qty'];

			// ตรวจสอบว่ามีอยู่ใน cart_list อยู่แล้วไหม
			$check = $this->conn->query("SELECT id FROM cart_list WHERE customer_id = '{$user_id}' AND product_id = '{$product_id}'");
			if ($check && $check->num_rows > 0) {
				// ถ้ามีแล้ว เพิ่มจำนวน
				$this->conn->query("UPDATE cart_list SET quantity = quantity + '{$qty}' WHERE customer_id = '{$user_id}' AND product_id = '{$product_id}'");
			} else {
				// ถ้ายังไม่มี เพิ่มใหม่
				$this->conn->query("INSERT INTO cart_list (customer_id, product_id, quantity) VALUES ('{$user_id}', '{$product_id}', '{$qty}')");
			}
		}

		return json_encode(['status' => 'success']);
	}
	function save_promotion()
	{
		extract($_POST);
		$_POST['description'] = addslashes(htmlspecialchars($_POST['description']));
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) $data .= ",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}

		// ตรวจชื่อซ้ำ (option)
		$check = $this->conn->query("SELECT * FROM `promotions` WHERE `name` = '{$name}' " . (!empty($id) ? " AND id != {$id} " : ""))->num_rows;
		if ($this->capture_err())
			return $this->capture_err();

		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "มีโปรโมชั่นชื่อนี้อยู่แล้ว";
			return json_encode($resp);
		}

		if (empty($id)) {
			$sql = "INSERT INTO `promotions` SET {$data}";
		} else {
			$sql = "UPDATE `promotions` SET {$data} WHERE id = '{$id}'";
		}

		$save = $this->conn->query($sql);
		if ($save) {
			$pid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['id'] = $pid;
			$resp['status'] = 'success';
			$resp['msg'] = empty($id) ? "บันทึกโปรโมชั่นใหม่แล้ว" : "อัปเดตโปรโมชั่นสำเร็จ";
			$this->settings->set_flashdata('success', $resp['msg']);
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_promotion()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `promotions` WHERE id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "ลบโปรโมชั่นแล้ว");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'delete_img':
		echo $Master->delete_img();
		break;
	case 'save_product_type':
		echo $Master->save_product_type();
		break;
	case 'delete_product_type':
		echo $Master->delete_product_type();
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
	case 'save_stock':
		echo $Master->save_stock();
		break;
	case 'delete_stock':
		echo $Master->delete_stock();
		break;
	case 'add_to_cart':
		echo $Master->add_to_cart();
		break;
	case 'update_cart':
		echo $Master->update_cart();
		break;
	case 'delete_cart':
		echo $Master->delete_cart();
		break;
	case 'get_shipping_cost':
		$Master->get_shipping_cost();
		break;

	case 'place_order':
		$result = $Master->place_order();
		ob_end_clean();  // เคลียร์ buffer
		echo $result;
		break;
	case 'save_shipping':
		echo $Master->save_shipping();
		break;
	case 'delete_order':
		echo $Master->delete_order();
		break;
	case 'update_order_status':
		echo $Master->update_order_status();
		break;
	case 'save_inquiry':
		echo $Master->save_inquiry();
		break;
	case 'delete_inquiry':
		echo $Master->delete_inquiry();
		break;
	case 'delete_shipping':
		echo $Master->delete_shipping();
		break;
	case 'migrate_guest_cart':
		echo $Master->migrate_guest_cart();
		break;
	case 'save_promotion':
		echo $Master->save_promotion();
		break;
	case 'delete_inquiry':
		echo $Master->delete_promotion();
		break;
	default:
		// echo $sysset->index();
		break;
}
