<?php
header('Content-Type: application/json');
ob_start();
error_reporting(0);
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
		if (isset($_POST['description']))
			$_POST['description'] = addslashes(htmlspecialchars($_POST['description']));

		extract($_POST);
		$discount_type = (isset($_POST['discount_type']) && in_array($_POST['discount_type'], ['amount', 'percent'])) ? $_POST['discount_type'] : null;
		$discount_value = (isset($_POST['discount_value']) && $_POST['discount_value'] !== '' && is_numeric($_POST['discount_value'])) ? floatval($_POST['discount_value']) : null;
		$discounted_price = null;

		if ($discount_type !== null && $discount_value !== null) {
			if ($discount_type == 'amount') {
				$discounted_price = max(0, $price - $discount_value);
			} elseif ($discount_type == 'percent') {
				$discounted_price = max(0, $price - ($price * $discount_value / 100));
			}
		} else {
			// ถ้าไม่มีประเภทส่วนลด → ให้ discount_value กับ discounted_price เป็น null ด้วย
			$discount_value = null;
			$discounted_price = null;
		}


		// ให้ส่งเข้า $_POST ทุกครั้ง ไม่ว่าจะลดหรือไม่
		$data = "";
		$_POST['discount_value'] = $discount_value;
		$_POST['discounted_price'] = $discounted_price;
		$_POST['discount_type'] = $discount_type;
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'extra_categories', 'shopee', 'lazada', 'tiktok'))) {
				if (!empty($data)) $data .= ",";
				if (is_null($v)) {
					$data .= " `{$k}`=NULL ";
				} else {
					$v = $this->conn->real_escape_string($v);
					$data .= " `{$k}`='{$v}' ";
				}
			}
		}


		$check = $this->conn->query("SELECT * FROM `product_list` where `brand` = '{$brand}' and `name` = '{$name}' and delete_flag = 0 " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Product already exists.";
			return json_encode($resp);
			exit;
		}

		if (empty($id)) {
			$sql = "INSERT INTO `product_list` set {$data} ";
		} else {
			$sql = "UPDATE `product_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		$product_id = !empty($id) ? $id : $this->conn->insert_id;

		if ($save) {
			$resp['pid'] = $product_id;
			$resp['status'] = 'success';
			$resp['msg'] = empty($id) ? 'Product has been added successfully' : 'Product has been updated successfully';
			//เซฟลิงก์แพลตฟอร์ม
			$this->save_product_link($product_id);
			// จัดการรูปภาพ
			if (!empty($_FILES['img']['tmp_name'])) {
				$img_path = "uploads/products/";
				if (!is_dir(base_app . $img_path)) {
					mkdir(base_app . $img_path);
				}
				$accept = array('image/jpeg', 'image/png', 'image/jpg');
				if (!in_array($_FILES['img']['type'], $accept)) {
					$resp['msg'] .= " Image file type is invalid";
				} else {
					if ($_FILES['img']['type'] == 'image/jpeg')
						$uploadfile = imagecreatefromjpeg($_FILES['img']['tmp_name']);
					elseif ($_FILES['img']['type'] == 'image/png')
						$uploadfile = imagecreatefrompng($_FILES['img']['tmp_name']);
					if (!$uploadfile) {
						$resp['msg'] .= " Image is invalid";
					} else {
						list($width, $height) = getimagesize($_FILES['img']['tmp_name']);
						if ($width > 640 || $height > 480) {
							if ($width > $height) {
								$perc = ($width - 640) / $width;
								$width = 640;
								$height = $height - ($height * $perc);
							} else {
								$perc = ($height - 480) / $height;
								$height = 480;
								$width = $width - ($width * $perc);
							}
						}
						$temp = imagescale($uploadfile, $width, $height);
						$spath = $img_path . '/' . $_FILES['img']['name'];
						$i = 1;
						while (is_file(base_app . $spath)) {
							$spath = $img_path . '/' . ($i++) . '_' . $_FILES['img']['name'];
						}
						if ($_FILES['img']['type'] == 'image/jpeg')
							$upload = imagejpeg($temp, base_app . $spath, 60);
						elseif ($_FILES['img']['type'] == 'image/png')
							$upload = imagepng($temp, base_app . $spath, 6);
						if ($upload) {
							$this->conn->query("UPDATE product_list set image_path = CONCAT('{$spath}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$product_id}' ");
						}
						imagedestroy($temp);
					}
				}
			}
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
			return json_encode($resp);
		}

		//ลบหมวดหมู่เพิ่มเติมเดิม
		$this->conn->query("DELETE FROM product_categories WHERE product_id = {$product_id}");

		//เพิ่มหมวดหมู่เพิ่มเติมใหม่
		if (!empty($_POST['extra_categories'])) {
			foreach ($_POST['extra_categories'] as $cat_id) {
				$cat_id = intval($cat_id); // ป้องกัน SQL Injection
				$sql = "INSERT INTO product_categories (product_id, category_id) VALUES ({$product_id}, {$cat_id})";
				$ins = $this->conn->query($sql);
				if (!$ins) {
					file_put_contents("insert_err_log.txt", "Error: " . $this->conn->error . "\nSQL: " . $sql . "\n", FILE_APPEND);
				}
			}
		}


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
			$this->settings->set_flashdata('success', 'เพิ่มสินค้าในตะกร้าเรียบร้อยแล้ว');
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
			$this->settings->set_flashdata('success', 'Cart Item has been updated.');
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
			if (empty($selected_ids)) throw new Exception('No items selected for checkout.');
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

			if (empty($cart_data)) throw new Exception('No matching cart items found.');
			if (round($total_amount, 2) != round($backend_total, 2)) {
				throw new Exception('Total amount mismatch.');
			}

			$customer = $this->conn->query("SELECT * FROM customer_list WHERE id = '{$customer_id}'")->fetch_assoc();
			$customer_name = trim("{$customer['firstname']} {$customer['middlename']} {$customer['lastname']}");
			$full_address = $this->conn->real_escape_string($delivery_address);

			$insert = $this->conn->query("INSERT INTO `order_list` 
			(`code`, `customer_id`, `delivery_address`, `total_amount`, `status`, `payment_status`, `delivery_status`, `date_created`, `date_updated`) 
			VALUES 
			('{$code}', '{$customer_id}', '{$full_address}', '{$backend_total}', 0, 0, 0, NOW(), NOW())");

			if (!$insert) throw new Exception('Failed to create order: ' . $this->conn->error);

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
			if (!$save) throw new Exception('Failed to save order items: ' . $this->conn->error);

			$this->conn->query("DELETE FROM `cart_list` WHERE customer_id = '{$customer_id}' AND id IN ($ids_str)");

			$this->conn->query("COMMIT");
			$this->settings->set_flashdata('success', 'Order has been placed successfully.');
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

		// ✅ ดึงค่าจาก POST โดยตรง (ไม่ใช้ switch เก่าแล้ว)
		$payment_status = isset($_POST['payment_status']) ? (int)$_POST['payment_status'] : 0;
		$delivery_status = isset($_POST['delivery_status']) ? (int)$_POST['delivery_status'] : 0;

		$update = $this->conn->query("UPDATE `order_list` 
        SET 
            `payment_status` = '{$payment_status}',
            `delivery_status` = '{$delivery_status}' 
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
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'delete_img':
		echo $Master->delete_img();
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
	case 'place_order':
		$result = $Master->place_order();
		ob_end_clean();  // เคลียร์ buffer
		echo $result;
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
	default:
		// echo $sysset->index();
		break;
}
