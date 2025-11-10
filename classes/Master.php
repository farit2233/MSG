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
	function resize_image_to_webp($src_path, $dest_path_webp, $max_width, $max_height, $quality = 80)
	{
		list($src_w, $src_h, $type) = getimagesize($src_path);

		if ($type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG) {
			return false; // ไม่รองรับไฟล์ประเภทอื่น
		}

		// 1. คำนวณขนาดใหม่ (ไม่ขยายภาพที่เล็กกว่า max)
		$scale = min($max_width / $src_w, $max_height / $src_h);
		if ($scale >= 1) { // ถ้าภาพต้นฉบับเล็กกว่าหรือเท่ากับ max
			$new_w = $src_w;
			$new_h = $src_h;
		} else { // ถ้าภาพใหญ่กว่า max ให้ย่อลง
			$new_w = floor($src_w * $scale);
			$new_h = floor($src_h * $scale);
		}

		// 2. สร้าง Resource จากไฟล์ต้นทาง
		switch ($type) {
			case IMAGETYPE_JPEG:
				$src_img = imagecreatefromjpeg($src_path);
				break;
			case IMAGETYPE_PNG:
				$src_img = imagecreatefrompng($src_path);
				break;
			default:
				return false;
		}

		// 3. สร้าง Canvas ปลายทาง
		$dst_img = imagecreatetruecolor($new_w, $new_h);

		// 4. (ข้อ 2) จัดการความโปร่งใสสำหรับ PNG
		if ($type == IMAGETYPE_PNG) {
			imagealphablending($dst_img, false);
			imagesavealpha($dst_img, true);
			$transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
			imagefilledrectangle($dst_img, 0, 0, $new_w, $new_h, $transparent);
		}

		// 5. (ข้อ 1) ย่อ/ขยายภาพ
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);

		// 6. (ข้อ 2) บันทึกเป็น WebP
		$success = imagewebp($dst_img, $dest_path_webp, $quality);

		// 7. เคลียร์หน่วยความจำ
		imagedestroy($src_img);
		imagedestroy($dst_img);

		return $success;
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
			$resp['msg'] = "มีประเภทสินค้านี้อยู่แล้ว";
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
				$resp['msg'] = " สร้างประเภทสินค้าใหม่เรียบร้อย";
			else
				$resp['msg'] = " อัปเดตประเภทสินค้าเรียบร้อย";
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
			$this->settings->set_flashdata('success', " ลบประเภทสินค้าเรียบร้อย");
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
			$resp['msg'] = "มีหมวดหมู่นี้อยู่แล้ว";
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
				$resp['msg'] = " สร้างหมวดหมู่สินค้าใหม่เรียบร้อย";
			else
				$resp['msg'] = " อัปเดตหมวดหมู่สินค้าเรียบร้อย";
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
			$this->settings->set_flashdata('success', " ลบหมวดหมู่สินค้าเรียบร้อย");
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
				$discounted_price = max(0, $vat_price - $discount_value);
			} elseif ($discount_type == 'percent') {
				$discounted_price = max(0, $vat_price - ($vat_price * $discount_value / 100));
			}

			// ปัดเป็นจำนวนเต็ม
			$discounted_price = round($discounted_price);
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
			return json_encode(['status' => 'failed', 'msg' => "มีสินค้านี้อยู่แล้ว"]);
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
			$resp['msg'] = empty($id) ? 'เพิ่มสินค้าใหม่เรียบร้อย' : 'อัปเดตสินค้าเรียบร้อย';

			// เซฟลิงก์
			$this->save_product_link($product_id);

			// ==================================================================
			// START: ส่วนแก้ไขการอัปโหลดรูปภาพหลัก
			// ==================================================================
			if (!empty($_FILES['img']['tmp_name'])) {

				// ==================================================================
				// START: [แก้ไข] ลบรูปเก่า (ย้ายมาไว้ตรงนี้ และแก้ไขตรรกะ)
				// ==================================================================
				if (!empty($id)) { // ตรวจสอบว่าเป็นการอัปเดต (ไม่ใช่การสร้างใหม่)
					// 1. ค้นหา path รูปเก่าจาก DB
					$old_image_query = $this->conn->query("SELECT image_path FROM `product_list` WHERE id = '{$id}'");
					if ($old_image_query && $old_image_query->num_rows > 0) {
						$old_image_data = $old_image_query->fetch_assoc();
						$old_image_path_with_query = $old_image_data['image_path'];

						if (!empty($old_image_path_with_query)) {
							// 2. เอารส่วน query string (?v=...) ออก
							$path_parts = explode('?', $old_image_path_with_query);
							$clean_old_path = $path_parts[0]; // (e.g., uploads/product/prod_xyz.webp)

							// 3. แยกส่วนประกอบของ Path
							$file_info = pathinfo($clean_old_path);
							$dir = $file_info['dirname'];       // (e.g., uploads/product)
							$filename = $file_info['filename']; // (e.g., prod_xyz)

							// 4. สร้างรายการไฟล์ที่จะลบ (ครบ 3 ขนาด)
							$files_to_delete = [
								base_app . $dir . '/' . $filename . '.webp',       // .../prod_xyz.webp
								base_app . $dir . '/' . $filename . '_medium.webp', // .../prod_xyz_medium.webp
								base_app . $dir . '/' . $filename . '_thumb.webp'  // .../prod_xyz_thumb.webp
							];

							// 5. วนลูปเพื่อลบไฟล์
							foreach ($files_to_delete as $file) {
								if (is_file($file)) {
									@unlink($file);
								}
							}
						}
					}
				}
				// ==================================================================
				// END: ลบรูปเก่า
				// ==================================================================

				$img_path = "uploads/product/"; // โฟลเดอร์รูปหลัก

				// (ข้อ 0) สร้างโฟลเดอร์ด้วยสิทธิ์ 0755 ที่ปลอดภัย
				if (!is_dir(base_app . $img_path)) {
					mkdir(base_app . $img_path, 0755, true);
				}

				$accept = ['image/jpeg', 'image/png', 'image/jpg'];
				if (!in_array($_FILES['img']['type'], $accept)) {
					$resp['msg'] .= " | ประเภทไฟล์รูปภาพหลักไม่ถูกต้อง";
				} else {
					// (ข้อ 0) สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกัน
					$base_filename = uniqid('prod_', true);

					// กำหนดขนาดและ Path (คุณสามารถปรับขนาด 400 และ 150 ได้ตามต้องการ)
					$paths = [
						'main' => ['path' => $img_path . $base_filename . '.webp', 'w' => 1000, 'h' => 1000],
						'medium' => ['path' => $img_path . $base_filename . '_medium.webp', 'w' => 400, 'h' => 400], // (ข้อ 3)
						'thumb' => ['path' => $img_path . $base_filename . '_thumb.webp', 'w' => 150, 'h' => 150]  // (ข้อ 3)
					];

					// (ข้อ 1, 2, 3) ประมวลผลและบันทึกทุกขนาด
					foreach ($paths as $key => $p) {
						$success = $this->resize_image_to_webp(
							$_FILES['img']['tmp_name'], // Source
							base_app . $p['path'], // Destination
							$p['w'], // Max Width
							$p['h']  // Max Height
						);

						if ($key == 'main') {
							if ($success) {
								// บันทึก Path รูปหลัก (1000px) ลง DB
								$db_path = $p['path'];
								$this->conn->query("UPDATE product_list SET image_path = CONCAT('{$db_path}', '?v=', UNIX_TIMESTAMP(CURRENT_TIMESTAMP)) WHERE id = '{$product_id}'");
							} else {
								$resp['msg'] .= " | ไม่สามารถบันทึกรูปภาพหลักได้";
							}
						}
						if (!$success) {
							$resp['msg'] .= " | ไม่สามารถสร้างรูปภาพขนาด {$key} ได้";
						}
					}
				}
			}
			// ==================================================================
			// END: ส่วนแก้ไขการอัปโหลดรูปภาพหลัก
			// ==================================================================

			// *** บล็อก "ลบรูปเก่า" เดิมที่เคยอยู่ตรงนี้ ถูกลบออกไปแล้ว ***

			// ==================================================================
			// START: ส่วนแก้ไขการอัปโหลดรูปแกลเลอรี
			// ==================================================================
			if (isset($_FILES['gallery_imgs']) && is_array($_FILES['gallery_imgs']['name'])) {
				$gallery_path = "uploads/products/"; // โฟลเดอร์รูปแกลเลอรี

				// (ข้อ 0) สร้างโฟลเดอร์ด้วยสิทธิ์ 0755 ที่ปลอดภัย
				if (!is_dir(base_app . $gallery_path)) {
					mkdir(base_app . $gallery_path, 0755, true);
				}

				$accept = ['image/jpeg', 'image/png', 'image/jpg'];

				foreach ($_FILES['gallery_imgs']['name'] as $key => $filename) {
					if (!empty($_FILES['gallery_imgs']['tmp_name'][$key])) {
						$file_type = $_FILES['gallery_imgs']['type'][$key];

						if (!in_array($file_type, $accept)) {
							$resp['msg'] .= " | ไฟล์แกลเลอรี '{$filename}' มีประเภทไม่ถูกต้อง";
							continue;
						}

						// (ข้อ 0) สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกัน
						$base_filename = uniqid('gallery_', true);

						// กำหนดขนาดและ Path (ใช้ขนาดเดียวกับรูปหลัก)
						$paths = [
							'main' => ['path' => $gallery_path . $base_filename . '.webp', 'w' => 1000, 'h' => 1000],
							'medium' => ['path' => $gallery_path . $base_filename . '_medium.webp', 'w' => 400, 'h' => 400], // (ข้อ 3)
							'thumb' => ['path' => $gallery_path . $base_filename . '_thumb.webp', 'w' => 150, 'h' => 150]  // (ข้อ 3)
						];

						// (ข้อ 1, 2, 3) ประมวลผลและบันทึกทุกขนาด
						foreach ($paths as $size_key => $p) {
							$success = $this->resize_image_to_webp(
								$_FILES['gallery_imgs']['tmp_name'][$key], // Source
								base_app . $p['path'], // Destination
								$p['w'], // Max Width
								$p['h']  // Max Height
							);

							if ($size_key == 'main') {
								if ($success) {
									// บันทึก Path รูปหลัก (1000px) ลง DB
									$db_path = $p['path'];
									$escaped_path = $this->conn->real_escape_string($db_path);
									$this->conn->query("INSERT INTO `product_image_path` (product_id, image_path) VALUES ('{$product_id}', '{$escaped_path}')");
								} else {
									$resp['msg'] .= " | ไม่สามารถอัปโหลดไฟล์แกลเลอรี '{$filename}' (main) ได้";
								}
							}
							if (!$success) {
								$resp['msg'] .= " | ไม่สามารถสร้างไฟล์แกลเลอรี '{$filename}' ({$size_key}) ได้";
							}
						}
					}
				}
			}
			// ==================================================================
			// END: ส่วนแก้ไขการอัปโหลดรูปแกลเลอรี
			// ==================================================================
		} else {
			return json_encode(['status' => 'failed', 'err' => $this->conn->error . " [{$sql}]"]);
		}

		// Flash message
		if ($resp['status'] == 'success' && isset($resp['msg']))
			$this->settings->set_flashdata('success', $resp['msg']);

		return json_encode($resp);
	}

	function delete_gallery_image()
	{
		extract($_POST); // $id จะถูกดึงมาจาก $_POST

		if (empty($id)) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'ไม่พบ ID ของรูปภาพ';
			return json_encode($resp);
		}

		// 1. ค้นหา path ของไฟล์ก่อน
		$stmt_select = $this->conn->prepare("SELECT `image_path` FROM `product_image_path` WHERE `id` = ?");
		$stmt_select->bind_param("i", $id);
		$stmt_select->execute();
		$result = $stmt_select->get_result();

		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$file_path = $row['image_path'];

			// 2. สร้าง path จริงของไฟล์บน server
			$absolute_path = __DIR__ . '/../' . $file_path;

			// 3. ลบไฟล์จริง (ถ้ามีอยู่)
			if (is_file($absolute_path)) {
				@unlink($absolute_path); // ลบไฟล์ต้นฉบับ (jpg, png)
			}

			// 4. (Bonus) ลบไฟล์ .webp ที่เกี่ยวข้องทั้งหมด (normal, medium, thumb)
			$dir = pathinfo($absolute_path, PATHINFO_DIRNAME);
			$filename = pathinfo($absolute_path, PATHINFO_FILENAME);

			// สร้าง List ของไฟล์ .webp ทั้ง 3 ขนาด
			$webp_files_to_delete = [
				$dir . '/' . $filename . '.webp',     // ไฟล์ปกติ
				$dir . '/' . $filename . '_medium.webp', // ไฟล์ medium
				$dir . '/' . $filename . '_thumb.webp'  // ไฟล์ thumb
			];

			// วนลูปเช็คและลบไฟล์
			foreach ($webp_files_to_delete as $file) {
				if (is_file($file)) {
					@unlink($file); // ใช้ @ เพื่อ suppress warning กรณีไฟล์ลบไม่ได้
				}
			}

			// 5. ลบข้อมูลออกจากฐานข้อมูล
			$stmt_delete = $this->conn->prepare("DELETE FROM `product_image_path` WHERE `id` = ?");
			$stmt_delete->bind_param("i", $id);

			if ($stmt_delete->execute()) {
				$resp['status'] = 'success';
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = 'ลบข้อมูลในฐานข้อมูลไม่สำเร็จ: ' . $this->conn->error;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'ไม่พบรูปภาพ ID นี้ในฐานข้อมูล';
		}

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

		// เปลี่ยนค่า delete_flag เป็น 1 และ status เป็น 0 แทนการลบจริง
		$update = $this->conn->query("UPDATE `product_list` 
                                  SET `delete_flag` = 1, `status` = 0 
                                  WHERE `id` = '{$id}'");

		if ($update) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "ลบสินค้าเรียบร้อยแล้ว");
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
				$resp['msg'] = "เพิ่มสต๊อกสินค้าใหม่เรียบร้อย";
			else
				$resp['msg'] = "อัปเดตสต๊อกสินค้าเรียบร้อย";
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
			$this->settings->set_flashdata('success', "ลบสต๊อกสินค้าเรียบร้อย");
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

	function get_shipping_details()
	{

		// 1. รับค่า ID ขนส่ง และ ID สินค้า จาก AJAX
		$shipping_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$selected_items_str = isset($_POST['selected_items']) ? $this->conn->real_escape_string($_POST['selected_items']) : '';

		// เตรียมข้อมูลตอบกลับ (Default คือล้มเหลว)
		$response = ['success' => false, 'error' => 'เกิดข้อผิดพลาดที่ไม่รู้จัก'];

		if ($shipping_id == 0) {
			$response['error'] = 'ไม่ได้เลือกขนส่ง';
			echo json_encode($response);
			exit;
		}
		if (empty($selected_items_str)) {
			$response['error'] = 'ไม่พบสินค้าในตะกร้า';
			echo json_encode($response);
			exit;
		}

		// 2. คำนวณน้ำหนักรวม (Total Weight) จาก selected_items
		$total_weight = 0;
		$ids_array = array_map('intval', explode(',', $selected_items_str));
		$safe_ids = implode(',', $ids_array);

		if (!empty($safe_ids)) {
			$weight_qry = $this->conn->query("
                SELECT 
                    c.quantity, 
                    p.product_weight 
                FROM cart_list c
                INNER JOIN product_list p ON c.product_id = p.id
                WHERE c.id IN ({$safe_ids}) AND c.customer_id = '{$this->settings->userdata('id')}'
            ");

			if ($weight_qry) {
				while ($row = $weight_qry->fetch_assoc()) {
					$total_weight += ($row['product_weight'] ?? 0) * $row['quantity'];
				}
			}
		}

		// 3. Query หาค่าส่ง, ชื่อ, และสถานะ COD ของขนส่งที่เลือก
		//    โดยอิงจากน้ำหนักที่คำนวณได้
		$shipping_query_string = "
            SELECT 
                sm.id, 
                sm.name, 
                sm.cod_enabled, 
                sp.price as cost
            FROM 
                shipping_methods sm
            LEFT JOIN 
                shipping_prices sp ON sm.id = sp.shipping_methods_id
            WHERE 
                sm.id = {$shipping_id}  -- 1. กรองเฉพาะ ID ขนส่งที่เลือก
                AND sm.status = 1 
                AND sm.delete_flag = 0
                AND ('{$total_weight}' >= sp.min_weight AND '{$total_weight}' <= sp.max_weight) -- 2. หาน้ำหนักที่ตรงช่วง
            LIMIT 1
        ";

		$shipping_qry = $this->conn->query($shipping_query_string);

		// 4. สร้าง JSON ตอบกลับ
		if ($shipping_qry && $shipping_qry->num_rows > 0) {
			$row = $shipping_qry->fetch_assoc();

			$response['success'] = true;
			$response['shipping_info'] = [
				'id' => (int)$row['id'],
				'name' => $row['name'],
				'cost' => (float)$row['cost'],
				'cod_enabled' => (int)$row['cod_enabled']
			];
			// ลบ error message เริ่มต้นทิ้งไป
			unset($response['error']);
		} else {
			// !! จุดสำคัญที่แก้ปัญหา !!
			// ถ้าไม่เจอ (num_rows = 0) ให้ส่ง JSON บอกว่าไม่เจอ
			// ไม่ใช่ส่งค่าว่าง ("")
			$response['error'] = 'ไม่พบค่าจัดส่งสำหรับน้ำหนักนี้ (' . $total_weight . ' kg)';
		}

		// 5. ส่ง JSON กลับไปให้ JavaScript
		echo json_encode($response);
		exit; // จบการทำงานทันที
	}

	function place_order()
	{
		extract($_POST);
		$customer_id = $this->settings->userdata('id');
		$pref = date("Ymd");
		$code = sprintf("%'.05d", 1);

		$resp = [];

		$this->conn->query("START TRANSACTION");

		try {
			// --- สร้างรหัส Order Code ---
			while (true) {
				$check = $this->conn->query("SELECT id FROM `order_list` WHERE `code` = '{$pref}{$code}'")->num_rows;
				if ($check > 0) {
					$code = sprintf("%'.05d", abs($code) + 1);
				} else {
					$code = $pref . $code;
					break;
				}
			}

			// --- ดึงข้อมูลสินค้าในตะกร้า ---
			$selected_ids = !empty($selected_items) ? array_filter(array_map('intval', explode(',', $selected_items))) : [];
			if (empty($selected_ids)) throw new Exception('ไม่มีรายการสินค้าสำหรับชำระสินค้า');
			$ids_str = implode(',', $selected_ids);
			$cart = $this->conn->query("
            SELECT c.*, p.name as product, p.price, p.vat_price, p.discount_type, p.discount_value, p.discounted_price, p.product_weight
            FROM `cart_list` c 
            INNER JOIN product_list p ON c.product_id = p.id 
            WHERE c.id IN ($ids_str) AND c.customer_id = '{$customer_id}'
        ");

			// --- คำนวณยอดรวมราคาสินค้า (ยังไม่รวมโปรโมชัน/คูปอง) ---
			$backend_subtotal = 0;
			$total_weight = 0;
			$cart_data = [];
			while ($row = $cart->fetch_assoc()) {
				$original_price = $row['vat_price'];
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
				$backend_subtotal += $final_price * $row['quantity'];
				$total_weight += ($row['product_weight'] ?? 0) * $row['quantity'];
				$cart_data[] = $row;
			}

			if (empty($cart_data)) throw new Exception('ไม่พบรายการสินค้าที่ตรงกันในตะกร้า');

			// --- คำนวณค่าขนส่งตามน้ำหนักรวม (Backend) ---
			$shipping_prices_id = 0;
			$shipping_cost = 0;
			$selected_shipping_method_id = isset($_POST['shipping_methods_id']) ? intval($_POST['shipping_methods_id']) : 0;
			if ($selected_shipping_method_id <= 0) {
				throw new Exception('กรุณาเลือกวิธีการจัดส่ง');
			}
			if ($total_weight > 0) {
				$shipping_qry = $this->conn->query("
					SELECT id, price 
					FROM `shipping_prices` 
					WHERE `shipping_methods_id` = '{$selected_shipping_method_id}' 
					AND '{$total_weight}' >= min_weight 
					AND '{$total_weight}' <= max_weight
					LIMIT 1
				");
				if ($shipping_qry->num_rows > 0) {
					$shipping_data = $shipping_qry->fetch_assoc();
					$shipping_cost = floatval($shipping_data['price']);
					$shipping_prices_id = $shipping_data['id'];  // เก็บ ID ของ shipping_prices
				} else {
					throw new Exception("ไม่สามารถคำนวณค่าจัดส่งได้สำหรับน้ำหนักรวม {$total_weight} กรัม กรุณาติดต่อร้านค้า");
				}
			}

			// ✨ INITIALIZE: ตัวแปรสำหรับส่วนลดและค่าส่งสุดท้าย
			$promotion_discount_amount = 0; // ส่วนลดจากราคาสินค้า
			$coupon_discount_amount = 0; // ส่วนลดจากราคาสินค้า
			$shipping_discount = 0; // ส่วนลดค่าจัดส่งโดยเฉพาะ
			$final_shipping_cost = $shipping_cost; // ค่าส่งเริ่มต้น
			$promo_data = null;
			$coupon_data = null;


			// ======================= START: ส่วนจัดการโปรโมชัน =======================
			$promotion_id = isset($_POST['promotion_id']) ? intval($_POST['promotion_id']) : 0;
			if ($promotion_id > 0) {
				$promo_qry = $this->conn->query("SELECT * FROM `promotions_list` WHERE id = {$promotion_id} AND status = 1 AND delete_flag = 0");
				if ($promo_qry->num_rows > 0) {
					$promo_data = $promo_qry->fetch_assoc();
					if ($backend_subtotal >= $promo_data['minimum_order']) {
						switch ($promo_data['type']) {
							case 'fixed':
								$promotion_discount_amount  = floatval($promo_data['discount_value']);
								break;
							case 'percent':
								$promotion_discount_amount  = $backend_subtotal * (floatval($promo_data['discount_value']) / 100);
								break;
							case 'free_shipping':
								// ✅ แก้ไข: บันทึกค่าส่งที่ถูกยกเว้นให้เป็นยอดส่วนลด
								$shipping_discount  = $shipping_cost;
								$final_shipping_cost = 0; // โปรโมชันส่งฟรี
								break;
						}
					} else {
						throw new Exception('ยอดสั่งซื้อไม่ถึงเกณฑ์ขั้นต่ำสำหรับโปรโมชันนี้');
					}
				} else {
					throw new Exception('ไม่พบโปรโมชันที่ส่งมา หรือโปรโมชันไม่พร้อมใช้งาน');
				}
			}
			// ======================= END: ส่วนจัดการโปรโมชัน =========================

			// ======================= ✨ START: ส่วนจัดการคูปอง (เพิ่มใหม่) =======================
			$coupon_code_id = isset($_POST['coupon_code_id']) ? intval($_POST['coupon_code_id']) : 0;
			if ($coupon_code_id > 0) {
				$coupon_qry = $this->conn->query("SELECT * FROM `coupon_code_list` WHERE id = {$coupon_code_id} AND status = 1 AND delete_flag = 0");
				if ($coupon_qry->num_rows > 0) {
					$coupon_data = $coupon_qry->fetch_assoc();

					// ตรวจสอบวันหมดอายุ
					$current_date = date('Y-m-d H:i:s');
					if ($coupon_data['start_date'] > $current_date || $coupon_data['end_date'] < $current_date) {
						throw new Exception('คูปองที่คุณใช้หมดอายุแล้ว');
					}

					// --- START: NEW LOGIC ---
					// 1. ดึงรายการ ID ของสินค้าที่ร่วมรายการกับคูปองนี้
					$eligible_product_ids = [];
					$prod_qry = $this->conn->query("
						SELECT product_id 
						FROM `coupon_code_products` 
						WHERE coupon_code_id = {$coupon_code_id} AND status = 1 AND delete_flag = 0
					");
					while ($p_row = $prod_qry->fetch_assoc()) {
						$eligible_product_ids[] = $p_row['product_id'];
					}

					// 2. กำหนดฐานของยอดเงินที่จะใช้คำนวณส่วนลด
					$discount_base_amount = 0;
					if (!empty($eligible_product_ids)) {
						// ถ้าคูปองถูกจำกัดไว้สำหรับสินค้าบางรายการ ให้คำนวณยอดรวมจากสินค้าเหล่านั้นเท่านั้น
						$eligible_subtotal = 0;
						foreach ($cart_data as $item) {
							if (in_array($item['product_id'], $eligible_product_ids)) {
								$eligible_subtotal += $item['final_price'] * $item['quantity'];
							}
						}
						$discount_base_amount = $eligible_subtotal;
					} else {
						// ถ้าคูปองไม่ได้จำกัดสินค้า (ใช้ได้ทั้งร้าน) ให้ใช้ยอดรวมทั้งหมด
						$discount_base_amount = $backend_subtotal;
					}
					// --- END: NEW LOGIC ---

					// ตรวจสอบยอดสั่งซื้อขั้นต่ำ (ยังคงเช็คจากยอดรวมทั้งตะกร้า)
					if ($backend_subtotal >= $coupon_data['minimum_order']) {

						// คำนวณส่วนลดจากฐานยอดเงินที่กำหนดไว้ ($discount_base_amount)
						if ($discount_base_amount > 0) { // คำนวณเมื่อมีสินค้าที่ร่วมรายการในตะกร้าเท่านั้น
							switch ($coupon_data['type']) {
								case 'fixed':
									$coupon_discount_amount = floatval($coupon_data['discount_value']);
									// ป้องกันไม่ให้ส่วนลดมากกว่าราคาสินค้าที่ร่วมรายการ
									if ($coupon_discount_amount > $discount_base_amount) {
										$coupon_discount_amount = $discount_base_amount;
									}
									break;
								case 'percent':
									// คำนวณส่วนลดจากยอดรวมของสินค้าที่ร่วมรายการเท่านั้น
									$coupon_discount_amount = $discount_base_amount * (floatval($coupon_data['discount_value']) / 100);
									break;
								case 'free_shipping':
									// ส่วนลดค่าส่งจะทำงานเหมือนเดิม ไม่เกี่ยวกับราคาสินค้า
									$shipping_discount += $shipping_cost;
									$final_shipping_cost = 0;
									break;
							}
						}
					} else {
						throw new Exception('ยอดสั่งซื้อไม่ถึงเกณฑ์ขั้นต่ำสำหรับคูปองนี้');
					}
				} else {
					throw new Exception('ไม่พบคูปองที่ส่งมา หรือคูปองไม่พร้อมใช้งาน');
				}
			}
			// ======================= END: ส่วนจัดการคูปอง =========================


			// --- ✨ คำนวณยอดรวมสุดท้าย (ปรับปรุงใหม่) ---
			$grand_total = ($backend_subtotal - $promotion_discount_amount - $coupon_discount_amount) + $final_shipping_cost;

			// --- เตรียมข้อมูลสำหรับบันทึก ---
			$delivery_address = $this->conn->real_escape_string($delivery_address);
			$applied_promo_id = ($promotion_id > 0) ? "'{$promotion_id}'" : "NULL";
			$applied_coupon_id = ($coupon_code_id > 0) ? "'{$coupon_code_id}'" : "NULL";


			$customer = $this->conn->query("SELECT * FROM customer_list WHERE id = '{$customer_id}'")->fetch_assoc();
			$customer_name = trim("{$customer['firstname']} {$customer['middlename']} {$customer['lastname']}");
			$customer_email = trim("{$customer['email']}");

			// --- ดึงข้อมูลจาก customer_addresses โดยใช้ customer_id และ is_primary = 1 ---
			$customer_addresses = $this->conn->query("SELECT * FROM customer_addresses WHERE customer_id = '{$customer_id}' AND is_primary = 1")->fetch_assoc();

			if (!$customer) {
				throw new Exception('ไม่พบที่อยู่หลักของลูกค้า');
			}

			$name = trim("{$customer_addresses['name']}");
			$contact = trim("{$customer_addresses['contact']}");
			$delivery_address = trim("{$customer_addresses['address']} {$customer_addresses['sub_district']} {$customer_addresses['district']} {$customer_addresses['province']} {$customer_addresses['postal_code']}");


			$shipping_methods_name = 'ไม่ระบุ';
			if (!empty($selected_shipping_method_id)) {
				$res = $this->conn->query("SELECT name FROM shipping_methods WHERE id = {$selected_shipping_method_id}");
				if ($res->num_rows > 0) {
					$ship = $res->fetch_assoc();
					$shipping_methods_name = $ship['name'];
				}
			}
			$order_status = 0;   // 0 = Pending (รอตรวจสอบสลิป)
			$payment_status = 0; // 0 = Unpaid (ยังไม่จ่ายเงิน)
			$is_cod = 0;         // ✨ 1. กำหนดค่าเริ่มต้นสำหรับ cod เป็น 0 (โอนเงิน)

			if (isset($payment_method) && $payment_method == 'cod') {
				// ถ้าเป็น COD, ให้อัปเดตสถานะออเดอร์ทันที
				$order_status = 1;     // 1 = Packed (พร้อมจัดส่ง)
				$is_cod = 1;         // ✨ 2. ถ้าเป็น COD ให้เปลี่ยนค่าเป็น 1
			}


			// --- ✨ บันทึกข้อมูลลง order_list (แก้ไข Query) ---
			$insert = $this->conn->query("INSERT INTO `order_list` 
			(`code`, `customer_id`, `name`, `contact`, `delivery_address`, `total_amount`, `promotion_discount`, `coupon_discount`, `shipping_methods_id`, `shipping_prices_id`, `promotion_id`, `coupon_code_id`, `status`, `payment_status`, `delivery_status`, `cod`) 
			VALUES 
			('{$code}', '{$customer_id}', '{$name}', '{$contact}', '{$delivery_address}', '{$grand_total}', '{$promotion_discount_amount}', '{$coupon_discount_amount}', {$selected_shipping_method_id}, {$shipping_prices_id}, {$applied_promo_id}, {$applied_coupon_id}, '{$order_status}', '{$payment_status}', 0, '{$is_cod}')");

			if (!$insert) throw new Exception('ไม่สามารถสร้างคำสั่งซื้อได้: ' . $this->conn->error);
			$oid = $this->conn->insert_id;

			if (isset($payment_method) && $payment_method == 'transfer') {

				if (isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] == 0) {
					// 1. กำหนดตำแหน่งที่จะเก็บไฟล์
					$upload_path = base_app . 'uploads/slips/';
					if (!is_dir($upload_path)) {
						mkdir($upload_path, 0777, true);
					}

					// 2. สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกัน เพื่อความปลอดภัย
					$file_ext = pathinfo($_FILES['payment_slip']['name'], PATHINFO_EXTENSION);
					$allowed_exts = ['jpg', 'jpeg', 'png'];
					if (!in_array(strtolower($file_ext), $allowed_exts)) {
						throw new Exception('รูปแบบไฟล์สลิปไม่ถูกต้อง ต้องเป็น JPG, JPEG, หรือ PNG เท่านั้น');
					}
					$file_name = "slip_" . $oid . "_" . time() . "." . uniqid() . "." . $file_ext;
					$target_file = $upload_path . $file_name;

					// 3. ย้ายไฟล์ไปยังโฟลเดอร์ที่กำหนด
					if (move_uploaded_file($_FILES['payment_slip']['tmp_name'], $target_file)) {

						// 4. ถ้าสำเร็จ บันทึก path ลงฐานข้อมูล payment_slips
						$image_db_path = 'uploads/slips/' . $file_name;
						$slip_insert_sql = "INSERT INTO `payment_slips` (`customer_id`, `order_id`, `image_path`, `approve`) 
                                        VALUES ('{$customer_id}', '{$oid}', '{$image_db_path}', 0)";

						if (!$this->conn->query($slip_insert_sql)) {
							// ถ้าบันทึกลง DB ไม่สำเร็จ ให้ลบไฟล์ที่อัปโหลดทิ้ง
							if (file_exists($target_file)) unlink($target_file);
							throw new Exception('ไม่สามารถบันทึกข้อมูลสลิปได้: ' . $this->conn->error);
						}

						$this->conn->query("UPDATE `order_list` SET `payment_status` = 1 WHERE id = '{$oid}'");
					} else {
						throw new Exception('ไม่สามารถอัปโหลดไฟล์สลิปได้');
					}
				} else {
					// ถ้าไม่มีไฟล์แนบมาหรือไฟล์มีปัญหา ให้ยกเลิก
					throw new Exception('กรุณาแนบไฟล์สลิปการโอนเงิน');
				}
			}
			if ($promotion_id > 0) {
				// ถ้าเป็นโปรส่งฟรี ให้ส่งค่า shipping_discount ไปบันทึก, ถ้าไม่ใช่ส่ง promotion_discount_amount
				$logged_promo_discount = ($promo_data['type'] === 'free_shipping') ? $shipping_discount : $promotion_discount_amount;
				$this->log_promotion_usage($promotion_id, $customer_id, $oid, $logged_promo_discount, count($cart_data));
			}
			if ($coupon_code_id > 0) {
				// ถ้าเป็นคูปองส่งฟรี ให้ส่งค่า shipping_discount ไปบันทึก
				$logged_coupon_discount = ($coupon_data['type'] === 'free_shipping') ? $shipping_discount : $coupon_discount_amount;
				$this->log_coupon_usage($coupon_code_id, $customer_id, $oid, $logged_coupon_discount, count($cart_data));
			}

			// --- ✨ บันทึกข้อมูลลง order_items (แก้ไข Query) ---
			$data = "";
			foreach ($cart_data as $row) {
				if (!empty($data)) $data .= ", ";
				$product_id = intval($row['product_id']);
				$quantity = intval($row['quantity']);
				$price = floatval($row['final_price']);
				$data .= "('{$oid}', '{$product_id}', '{$quantity}', '{$price}', {$applied_promo_id}, {$applied_coupon_id})";
			}
			$stock_out_data = "";
			foreach ($cart_data as $item) {
				$product_id = intval($item['product_id']);
				$quantity_out = intval($item['quantity']);

				// ค้นหา stock_id จาก product_id
				// สมมติว่า 1 product มี 1 stock หลัก
				$stock_qry = $this->conn->query("SELECT id FROM `stock_list` WHERE product_id = {$product_id} LIMIT 1");
				if ($stock_qry->num_rows > 0) {
					$stock = $stock_qry->fetch_assoc();
					$stock_id = $stock['id'];

					if (!empty($stock_out_data)) $stock_out_data .= ", ";
					$stock_out_data .= "('{$oid}', '{$stock_id}', '{$quantity_out}')";
				}
			}

			if (!empty($stock_out_data)) {
				$insert_stock_out = $this->conn->query("INSERT INTO `stock_out` (`order_id`, `stock_id`, `quantity`) VALUES {$stock_out_data}");
				if (!$insert_stock_out) {
					throw new Exception('ไม่สามารถบันทึกข้อมูลการตัดสต็อกได้: ' . $this->conn->error);
				}
			}

			$save = $this->conn->query("INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`, `promotion_id`, `coupon_code_id`) VALUES {$data}");
			if (!$save) throw new Exception('ไม่สามารถบันทึกรายการสินค้า: ' . $this->conn->error);

			// --- ลบสินค้าออกจากตะกร้าและ Commit ---
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
				$mail->addAddress($customer_email, $customer_name);
				$body = "
						<div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px;'>
							<h2 style='color: #16542b; text-align:center;'>🧾 ยืนยันคำสั่งซื้อ</h2>
							<p>เรียนคุณ <strong>{$customer_name}</strong></p>
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

				// สร้างรายการสินค้า
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

				// แสดงค่าส่งและยอดรวม
				$body .= "
									<tr>
										<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'><strong>ค่าส่ง</strong></td>
										<td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($shipping_cost, 2) . "</td>
									</tr>";
				// เพิ่มส่วนลดโปรโมชัน (ถ้ามี)
				if (isset($promo_data) && $promotion_id > 0) {
					if ($promo_data['type'] === 'free_shipping') {
						$discount_display = "<span style='color: green;'>ส่งฟรี</span>";
						$discount_label = "<strong>ส่วนลดโปรโมชัน</strong>";
					} else {
						$discount_display = "<span style='color: red;'>- " . number_format($promotion_discount_amount, 2) . "</span>";
						$discount_label = "<strong>ส่วนลดโปรโมชัน</strong>";
					}

					$promo_row_html = "
									<tr>
										<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'>{$discount_label}</td>
										<td style='padding:8px; border:1px solid #ddd; text-align:right;'>{$discount_display}</td>
									</tr>";
					$body .= $promo_row_html;
				}

				if (isset($coupon_data) && $coupon_code_id > 0) {
					if ($coupon_data['type'] === 'free_shipping') {
						$coupon_display = "<span style='color: green;'>ส่งฟรี</span>";
						$coupon_label = "<strong>ส่วนลดโค้ดส่วนลด</strong>";
					} else {
						$coupon_display = "<span style='color: red;'>- " . number_format($coupon_discount_amount, 2) . "</span>";
						$coupon_label = "<strong>ส่วนลดโค้ดส่วนลด</strong>";
					}

					$coupon_row_html = "
									<tr>
										<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'>{$coupon_label}</td>
										<td style='padding:8px; border:1px solid #ddd; text-align:right;'>{$coupon_display}</td>
									</tr>";
					$body .= $coupon_row_html;
				}

				$body .= "
									<tr>
										<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'><strong>ยอดสั่งซื้อ<small> รวม VAT</small></strong></td>
										<td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($grand_total, 2) . "</td>
									</tr>
									<tr>
										<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'><strong>รวมทั้งสิ้น</strong></td>
										<td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($grand_total, 2) . "</td>
									</tr>
								</tbody>
							</table>
							<p style='margin-top:20px;'>📦 จัดส่งไปที่ <br><div style='background:#f9f9f9; padding:10px; border:1px dashed #ccc;'>{$name}, {$contact}, {$delivery_address}</div></p>
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
				<div  style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px;'>
					<h2 style='color: #16542b; text-align:center;'>🧾 คำสั่งซื้อใหม่</h2>
					<p><strong>รหัสคำสั่งซื้อ:</strong> $code</p>
					<p><strong>ชื่อผู้ใช้:</strong> $customer_name</p>
					<p><stron>เบอร์โทร:</strong> $contact</p>
					<p><strong>ที่อยู่จัดส่ง:</strong>{$name}, {$contact}, {$delivery_address}</p>
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
							<tr>";

				// เพิ่มส่วนลดโปรโมชัน (ถ้ามี)
				if (isset($promo_data) && $promotion_id > 0) {
					if ($promo_data['type'] === 'free_shipping') {
						$discount_display = "<span style='color: green;'>ส่งฟรี</span>";
						$discount_label = "<strong>ส่วนลดโปรโมชัน</strong>";
					} else {
						$discount_display = "<span style='color: red;'>- " . number_format($promotion_discount_amount, 2) . "</span>";
						$discount_label = "<strong>ส่วนลดโปรโมชัน</strong>";
					}

					$promo_row_html = "
							<tr>
								<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'>{$discount_label}</td>
								<td style='padding:8px; border:1px solid #ddd; text-align:right;'>{$discount_display}</td>
							</tr>";
					$admin_body .= $promo_row_html;
				}

				if (isset($coupon_data) && $coupon_code_id > 0) {
					if ($coupon_data['type'] === 'free_shipping') {
						$coupon_display = "<span style='color: green;'>ส่งฟรี</span>";
						$coupon_label = "<strong>ส่วนลดโค้ดส่วนลด</strong>";
					} else {
						$coupon_display = "<span style='color: red;'>- " . number_format($coupon_discount_amount, 2) . "</span>";
						$coupon_label = "<strong>ส่วนลดโค้ดส่วนลด</strong>";
					}

					$coupon_row_html = "
							<tr>
								<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'>{$coupon_label}</td>
								<td style='padding:8px; border:1px solid #ddd; text-align:right;'>{$coupon_display}</td>
							</tr>";
					$admin_body .= $coupon_row_html;
				}

				$admin_body .= "
								<tr>
									<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'><strong>ยอดสั่งซื้อ<small> รวม VAT</small></strong></td>
									<td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($grand_total, 2) . "</td>
								</tr>
								<td colspan='3' style='padding:8px; border:1px solid #ddd; text-align:right;'><strong>รวมทั้งสิ้น</strong></td>
								<td style='padding:8px; border:1px solid #ddd; text-align:right;'>" . number_format($grand_total, 2) . "</td>
							</tr>
						</tbody>
					</table>
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
			- ชื่อผู้ใช้: $customer_name
			- ที่อยู่จัดส่ง: $name, $contact,
			  $delivery_address
			- ยอดรวม: " . number_format($grand_total, 2) . " บาท
			- ขนส่ง: $shipping_methods_name
			
			รายละเอียดสินค้า:";
			// รายการสินค้า
			$items = $this->conn->query("SELECT oi.*, p.name 
                            FROM order_items oi 
                            INNER JOIN product_list p ON oi.product_id = p.id 
                            WHERE oi.order_id = {$oid}");

			while ($row = $items->fetch_assoc()) {
				$subtotal = $row['price'] * $row['quantity'];
				$telegram_message .= "
			- {$row['name']} x{$row['quantity']} = " . number_format($subtotal, 2) . " บาท";
			}
			$telegram_message .= "
			
			ค่าส่ง: " . number_format($shipping_cost, 2) . " บาท";
			// เพิ่มส่วนลดโปรโมชัน (ถ้ามี)
			if (isset($promo_data) && $promotion_id > 0) {
				if ($promo_data['type'] === 'free_shipping') {
					// ในกรณีส่งฟรี ค่าส่งด้านบน ($final_shipping_cost) จะเป็น 0 อยู่แล้ว
					// เราอาจจะแสดงให้ชัดเจนว่าค่าส่งเดิมคือเท่าไหร่ และถูกลดไป
					$promo_text = "\nส่วนลดโปรโมชัน: ส่งฟรี (ประหยัด " . number_format($shipping_cost, 2) . " บาท)";
				} else {
					$promo_text = "\nส่วนลดโปรโมชัน: -" . number_format($promotion_discount_amount, 2) . " บาท";
				}
				$telegram_message .= $promo_text;
			}

			// ✨ เพิ่มใหม่: ส่วนลดคูปอง (ถ้ามี)
			if (isset($coupon_data) && $coupon_code_id > 0) {
				if ($coupon_data['type'] === 'free_shipping') {
					$coupon_text = "\nส่วนลดคูปอง: ส่งฟรี";
				} else {
					$coupon_text = "\nส่วนลดคูปอง: -" . number_format($coupon_discount_amount, 2) . " บาท";
				}
				$telegram_message .= $coupon_text;
			}
			$telegram_message .= "
			ยอดสั่งซื้อรวม VAT " . number_format($grand_total, 2) . " บาท
			รวมทั้งสิ้น: " . number_format($grand_total, 2) . " บาท
			";
			// ส่งข้อความ Telegram
			sendTelegramNotification($telegram_message);

			if ($promotion_discount > 0) {
				// บันทึกการใช้งานโปรโมชัน
				$this->log_promotion_usage($promotion_id, $customer_id, $oid, $promotion_discount, count($cart_data));
			}
			if ($coupon_discount > 0) {
				$this->log_coupon_usage($coupon_code_id, $customer_id, $oid, $coupon_discount, count($cart_data));
			}

			$this->settings->set_flashdata('success', 'ชำระสินค้าสำเร็จ');
			$resp = ['status' => 'success'];
		} catch (Exception $e) {
			$this->conn->query("ROLLBACK");
			$resp = ['status' => 'failed', 'msg' => $e->getMessage()];
		}
		return json_encode($resp);
	}

	// เพิ่มฟังก์ชันนี้เข้าไปในไฟล์ classes/Master.php
	function approve_slip()
	{
		// ดึงค่า 'id' (ของสลิป) และ 'approve' (สถานะที่เลือก) จากฟอร์ม
		extract($_POST);

		// อัปเดตตาราง payment_slips ตามสถานะที่เลือก
		$update_slip = $this->conn->query("UPDATE `payment_slips` SET approve = '{$approve}' WHERE id = '{$id}'");

		if (!$update_slip) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'ไม่สามารถอัปเดตสถานะสลิปได้';
			return json_encode($resp);
		}

		// --- Logic เพิ่มเติม: อัปเดตสถานะในตาราง order_list ด้วย ---

		// 1. ค้นหา order_id จาก slip_id
		$slip_qry = $this->conn->query("SELECT order_id FROM `payment_slips` WHERE id = '{$id}'");
		$order_id = $slip_qry->fetch_assoc()['order_id'];

		$payment_status = 1; // สถานะเริ่มต้น = รอตรวจสอบ

		if ($approve == 1) { // ถ้าอนุมัติ
			$payment_status = 2; // เปลี่ยนเป็น "ชำระเงินแล้ว"
		} else if ($approve == 2) { // ถ้าไม่อนุมัติ
			$payment_status = 3; // เปลี่ยนเป็น "ชำระเงินล้มเหลว"
		}

		// 2. อัปเดตสถานะในตาราง order_list
		$update_order = $this->conn->query("UPDATE `order_list` SET payment_status = '{$payment_status}' WHERE id = '{$order_id}'");

		if ($update_order) {
			$this->settings->set_flashdata('success', 'อัปเดตสถานะการชำระเงินเรียบร้อยแล้ว');
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'อัปเดตสถานะสลิปสำเร็จ แต่ไม่สามารถอัปเดตสถานะคำสั่งซื้อได้';
		}

		$order_id = '';
		$code = '';
		$customer_email = '';
		$customer_name = '';

		// ค้นหา order_id จาก slip_id
		$slip_qry = $this->conn->query("SELECT order_id FROM `payment_slips` WHERE id = '{$id}'");
		if ($slip_qry->num_rows > 0) {
			$order_id = $slip_qry->fetch_assoc()['order_id'];

			// ค้นหาข้อมูลออเดอร์และลูกค้าจาก order_id
			$order_qry = $this->conn->query("
            SELECT o.code, c.email, CONCAT(c.firstname, ' ', c.lastname) as fullname
            FROM `order_list` o
            INNER JOIN `customer_list` c ON o.customer_id = c.id
            WHERE o.id = '{$order_id}'
        ");
			if ($order_qry->num_rows > 0) {
				$data = $order_qry->fetch_assoc();
				$code = $data['code'];
				$customer_email = $data['email'];
				$customer_name = $data['fullname'];
			}
		}

		// --- อัปเดตฐานข้อมูล ---
		// อัปเดตสถานะในตาราง payment_slips
		$update_slip = $this->conn->query("UPDATE `payment_slips` SET approve = '{$approve}' WHERE id = '{$id}'");

		// กำหนดค่า payment_status สำหรับตาราง order_list
		$payment_status = 1; // 1 = รอตรวจสอบ (Default)
		if ($approve == 1) { // ถ้าอนุมัติ
			$payment_status = 2; // 2 = ชำระเงินแล้ว
		} else if ($approve == 2) { // ถ้าไม่อนุมัติ
			$payment_status = 3; // 3 = ชำระเงินล้มเหลว
		}

		// อัปเดตสถานะในตาราง order_list
		$update_order = $this->conn->query("UPDATE `order_list` SET payment_status = '{$payment_status}' WHERE id = '{$order_id}'");

		// --- ตรวจสอบผลลัพธ์และส่งอีเมล ---
		if ($update_slip && $update_order) {

			// ✨ --- เริ่มส่วนการส่งอีเมล --- ✨
			$mail = new PHPMailer(true);
			try {
				// --- กำหนดค่า SMTP (ควรเก็บเป็นตัวแปรภายนอกเพื่อความปลอดภัย) ---
				$mail->isSMTP();
				$mail->Host = 'smtp.gmail.com';
				$mail->Port = 465;
				$mail->SMTPAuth = true;
				$mail->Username = "faritre5566@gmail.com"; // ⚠️ ควรใช้ตัวแปรจาก config
				$mail->Password = "bchljhaxoqflmbys";      // ⚠️ ควรใช้ตัวแปรจาก config
				$mail->SMTPSecure = "ssl";
				$mail->CharSet = 'UTF-8';

				// --- เตรียมเนื้อหาอีเมลตามสถานะ ---
				$subject = '';
				$email_heading = '';
				$email_body_text = '';

				if ($approve == 1) { // กรณี "อนุมัติ"
					$subject = "✅ การชำระเงินของคุณได้รับการอนุมัติแล้ว";
					$email_heading = "การชำระเงินได้รับการยืนยัน";
					$email_body_text = "เราได้ตรวจสอบและยืนยันการชำระเงินสำหรับคำสั่งซื้อของคุณเรียบร้อยแล้ว และกำลังดำเนินการจัดเตรียมสินค้าเพื่อจัดส่งต่อไป";
				} else if ($approve == 2) { // กรณี "ไม่อนุมัติ"
					$subject = "❌ การชำระเงินของคุณไม่ได้รับการอนุมัติ";
					$email_heading = "การชำระเงินไม่สำเร็จ";
					$email_body_text = "เราต้องขออภัยที่ต้องแจ้งให้ทราบว่า การชำระเงินของคุณไม่ได้รับการอนุมัติ อาจเนื่องมาจากปัญหาบางประการ เช่น ภาพสลิปไม่ชัดเจน หรือยอดเงินไม่ถูกต้อง กรุณาติดต่อเราเพื่อสอบถามข้อมูลเพิ่มเติม หรือทำการอัปโหลดสลิปอีกครั้ง";
				}

				// --- ส่งอีเมลถ้ามีหัวข้อ (คือเป็นสถานะ อนุมัติ หรือ ไม่อนุมัติ เท่านั้น) ---
				if (!empty($subject)) {
					$mail->isHTML(true);
					$mail->Subject = $subject . " [#{$code}]";
					$mail->setFrom('faritre5566@gmail.com', 'MSG.com');
					$mail->addAddress($customer_email, $customer_name);

					$body = "
                    <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px;'>
                        <h2 style='color: #16542b; text-align:center;'>{$email_heading}</h2>
                        <p>เรียนคุณ <strong>{$customer_name}</strong>,</p>
                        <p>{$email_body_text}</p>
                        <hr>
                        <p><strong>รหัสคำสั่งซื้อ:</strong> {$code}</p>
                        <p style='text-align:center; margin-top:20px;'>
                            <a href='" . base_url . "?p=user/orders' style='background-color: #16542b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>ดูรายละเอียดคำสั่งซื้อ</a>
                        </p>
                      <p>หากคุณมีคำถามเพิ่มเติม กรุณาติดต่อที่ <a href='mailto:faritre5566@gmail.com'>faritre5566@gmail.com</a></p>
                    </div>";

					$mail->Body = $body;
					$mail->send();
				}
			} catch (Exception $e) {
				// บันทึก log หากส่งอีเมลไม่สำเร็จ (แต่ไม่ต้องแจ้งผู้ใช้)
				error_log("❌ ส่งอีเมลแจ้งเตือนสถานะสลิปไม่สำเร็จ: " . $mail->ErrorInfo);
			}
			// ✨ --- สิ้นสุดส่วนการส่งอีเมล --- ✨

			$this->settings->set_flashdata('success', 'อัปเดตสถานะการชำระเงินเรียบร้อยแล้ว');
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'เกิดข้อผิดพลาดในการอัปเดตฐานข้อมูล';
		}

		return json_encode($resp);
	}
	function cancel_order()
	{
		// ใช้ extract เพื่อรับค่า 'order_id' จาก AJAX POST request
		extract($_POST);

		try {
			$order_id = intval($order_id);
			$customer_id = $this->settings->userdata('id');

			// 1. ตรวจสอบว่าคำสั่งซื้อเป็นของลูกค้าที่ล็อกอินอยู่จริงหรือไม่ (เพื่อความปลอดภัย)
			$qry = $this->conn->query("SELECT o.*, c.email, CONCAT(c.firstname, ' ', c.lastname) as customer_name 
            FROM order_list o 
            INNER JOIN customer_list c ON o.customer_id = c.id 
            WHERE o.id = {$order_id} AND o.customer_id = {$customer_id}");

			if ($qry->num_rows <= 0) {
				throw new Exception("ไม่พบคำสั่งซื้อ หรือคุณไม่มีสิทธิ์ยกเลิกคำสั่งซื้อนี้");
			}

			$order = $qry->fetch_assoc();
			$order_code = $order['code'];
			$customer_name = $order['customer_name'];

			// 2. อัปเดตสถานะ payment_status เป็น 4 และ delivery_status เป็น 6
			$update = $this->conn->query("UPDATE order_list 
			SET payment_status = 4, delivery_status = 6, is_seen = 0, date_updated = NOW() 
			WHERE id = {$order_id}");

			if (!$update) {
				throw new Exception("ไม่สามารถอัปเดตสถานะคำสั่งซื้อได้: " . $this->conn->error);
			}

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
				$mail->Subject = "คำสั่งซื้อกำลังดำเนินการยกเลิก #{$order_code}";

				$mail->setFrom('faritre5566@gmail.com', 'MSG.com');
				$mail->addAddress($order['email'], $customer_name);

				$body = "
					<div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin:auto;'>
						<h2 style='color: #c0392b; text-align:center;'>คำสั่งซื้อกำลังดำเนินการยกเลิก</h2>
						<p>เรียนคุณลูกค้า <strong>{$customer_name}</strong>,</p>
						<p>หมายเลขคำสั่งซื้อ <strong>#{$order_code}</strong> กำลังดำเนินการยกเลิก</p>
						<p>📦 ที่อยู่จัดส่ง: {$order['delivery_address']}</p>
						<p>💵 ยอดรวม: " . number_format($order['total_amount'], 2) . " บาท</p>
						<hr>
						<p style='font-size:13px; color:#555;'>หากมีข้อสงสัย กรุณาติดต่อ <a href='mailto:faritre5566@gmail.com'>faritre5566@gmail.com</a></p>
					</div>
				";

				$mail->Body = $body;
				$mail->send();
			} catch (Exception $e) {
				// หากส่งอีเมลไม่สำเร็จ ให้บันทึก error ไว้ แต่ไม่ต้องหยุดการทำงาน
				error_log("❌ ส่งอีเมลแจ้งยกเลิกไม่สำเร็จ: " . $mail->ErrorInfo);
			}

			// 3. ส่วนของการส่งอีเมล (คงเดิม)
			$mail_admin = new PHPMailer(true);
			try {
				$mail_admin->isSMTP();
				$mail_admin->Host = 'smtp.gmail.com';
				$mail_admin->Port = 465;
				$mail_admin->SMTPAuth = true;
				$mail_admin->Username = "faritre5566@gmail.com";
				$mail_admin->Password = "bchljhaxoqflmbys";
				$mail_admin->SMTPSecure = "ssl";
				$mail_admin->CharSet = 'UTF-8';
				$mail_admin->isHTML(true);
				$mail_admin->Subject = "ลูกค้าได้ทำการยกเลิกคำสั่งซื้อหมายเลข #{$order_code}";

				$mail_admin->setFrom('faritre5566@gmail.com', 'MSG.com');
				$mail_admin->addAddress('faritre5566@gmail.com', 'Admin');  // อีเมลแอดมิน
				$mail_admin->addAddress('faritre1@gmail.com', 'Admin');
				$mail_admin->addAddress('faritre4@gmail.com', 'Admin');
				$admin_body = "
					<div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin:auto;'>
						<h2 style='color: #c0392b; text-align:center;'>คำสั่งซื้อกำลังรอดำเนินการยกเลิก</h2>
						<p>ลูกค้า <strong>{$customer_name}</strong>,{$contact}</p>
						<p>หมายเลขคำสั่งซื้อ <strong>#{$order_code}</strong> กำลังรอดำเนินการยกเลิก</p>
						<p>📦 ที่อยู่จัดส่ง: {$order['delivery_address']}</p>
						<p>💵 ยอดรวม: " . number_format($order['total_amount'], 2) . " บาท</p>
					</div>
				";

				$mail_admin->Body = $admin_body;
				$mail_admin->send();
			} catch (Exception $e) {
				// หากส่งอีเมลไม่สำเร็จ ให้บันทึก error ไว้ แต่ไม่ต้องหยุดการทำงาน
				error_log("❌ ส่งอีเมลแจ้งยกเลิกไม่สำเร็จ: " . $mail_admin->ErrorInfo);
			}

			// 4. ส่งค่า 1 กลับไปให้ AJAX เพื่อยืนยันว่าทำรายการสำเร็จ
			echo 1;

			// ฟังก์ชันส่ง Telegram
			function sendTelegramNotificationCancelOrder($message)
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

			$telegram_message = "
			ลูกค้าได้ทำการยกเลิกคำสั่งซื้อ
			- หมายเลขคำสั่งซื้อ: {$order_code}
			- ลูกค้า: {$customer_name}
			- เบอร์โทร : {$contact}
			- ที่อยู่จัดส่ง: {$order['delivery_address']}
			- ยอดรวม: " . number_format($order['total_amount'], 2) . " บาท";
			// ส่งข้อความ Telegram
			sendTelegramNotificationCancelOrder($telegram_message);
		} catch (Exception $e) {
			// หากเกิดข้อผิดพลาดใดๆ ใน try block ให้ส่งข้อความ error กลับไปให้ AJAX
			echo $e->getMessage();
		}
	}

	function return_order()
	{
		// ใช้ extract เพื่อรับค่า 'order_id' จาก AJAX POST request
		extract($_POST);

		try {
			$order_id = intval($order_id);
			$customer_id = $this->settings->userdata('id');

			// 1. ตรวจสอบว่าคำสั่งซื้อเป็นของลูกค้าที่ล็อกอินอยู่จริงหรือไม่ (เพื่อความปลอดภัย)
			$qry = $this->conn->query("SELECT o.*, c.email, CONCAT(c.firstname, ' ', c.lastname) as customer_name 
            FROM order_list o 
            INNER JOIN customer_list c ON o.customer_id = c.id 
            WHERE o.id = {$order_id} AND o.customer_id = {$customer_id}");

			if ($qry->num_rows <= 0) {
				throw new Exception("ไม่พบคำสั่งซื้อ หรือคุณไม่มีสิทธิ์ขอ คืนเงิน/คืนสินค้า คำสั่งซื้อนี้");
			}

			$order = $qry->fetch_assoc();
			$order_code = $order['code'];
			$customer_name = $order['customer_name'];

			$update = $this->conn->query("UPDATE order_list 
			SET payment_status = 5, delivery_status = 8, is_seen = 0, date_updated = NOW() 
			WHERE id = {$order_id}");


			if (!$update) {
				throw new Exception("ไม่สามารถอัปเดตสถานะคำสั่งซื้อได้: " . $this->conn->error);
			}

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
				$mail->Subject = "คำสั่งซื้อกำลังดำเนินการขอ คืนเงิน/คืนสินค้า #{$order_code}";

				$mail->setFrom('faritre5566@gmail.com', 'MSG.com');
				$mail->addAddress($order['email'], $customer_name);

				$body = "
					<div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin:auto;'>
						<h2 style='color: #c0392b; text-align:center;'>คำสั่งซื้อกำลังดำเนินการขอ คืนเงิน/คืนสินค้า</h2>
						<p>เรียนคุณลูกค้า <strong>{$customer_name}</strong>,</p>
						<p>หมายเลขคำสั่งซื้อ <strong>#{$order_code}</strong> กำลังดำเนินการขอ คืนเงิน/คืนสินค้า</p>
						<p>📦 ที่อยู่จัดส่ง: {$order['delivery_address']}</p>
						<p>💵 ยอดรวม: " . number_format($order['total_amount'], 2) . " บาท</p>
						<hr>
						<p style='font-size:13px; color:#555;'>หากมีข้อสงสัย กรุณาติดต่อ <a href='mailto:faritre5566@gmail.com'>faritre5566@gmail.com</a></p>
					</div>
				";

				$mail->Body = $body;
				$mail->send();
			} catch (Exception $e) {
				// หากส่งอีเมลไม่สำเร็จ ให้บันทึก error ไว้ แต่ไม่ต้องหยุดการทำงาน
				error_log("❌ ส่งอีเมลแจ้งยกเลิกไม่สำเร็จ: " . $mail->ErrorInfo);
			}

			// 3. ส่วนของการส่งอีเมล (คงเดิม)
			$mail_admin = new PHPMailer(true);
			try {
				$mail_admin->isSMTP();
				$mail_admin->Host = 'smtp.gmail.com';
				$mail_admin->Port = 465;
				$mail_admin->SMTPAuth = true;
				$mail_admin->Username = "faritre5566@gmail.com";
				$mail_admin->Password = "bchljhaxoqflmbys";
				$mail_admin->SMTPSecure = "ssl";
				$mail_admin->CharSet = 'UTF-8';
				$mail_admin->isHTML(true);
				$mail_admin->Subject = "ลูกค้าได้ทำการขอ คืนเงิน/คืนสินค้า #{$order_code}";

				$mail_admin->setFrom('faritre5566@gmail.com', 'MSG.com');
				$mail_admin->addAddress('faritre5566@gmail.com', 'Admin');  // อีเมลแอดมิน
				$mail_admin->addAddress('faritre1@gmail.com', 'Admin');
				$mail_admin->addAddress('faritre4@gmail.com', 'Admin');
				$admin_body = "
					<div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin:auto;'>
						<h2 style='color: #c0392b; text-align:center;'>คำสั่งซื้อกำลังรอดำเนินการ คืนเงิน/คืนสินค้า</h2>
						<p>ลูกค้า <strong>{$customer_name}</strong>,</p>
						<p>หมายเลขคำสั่งซื้อ <strong>#{$order_code}</strong> กำลังรอดำเนินการ คืนเงิน/คืนสินค้า</p>
						<p>📦 ที่อยู่จัดส่ง: {$order['delivery_address']}</p>
						<p>💵 ยอดรวม: " . number_format($order['total_amount'], 2) . " บาท</p>
					</div>
				";

				$mail_admin->Body = $admin_body;
				$mail_admin->send();
			} catch (Exception $e) {
				// หากส่งอีเมลไม่สำเร็จ ให้บันทึก error ไว้ แต่ไม่ต้องหยุดการทำงาน
				error_log("❌ ส่งอีเมลแจ้งคำขอ คืนเงิน/คืนสินค้า ไม่สำเร็จ: " . $mail_admin->ErrorInfo);
			}

			// 4. ส่งค่า 1 กลับไปให้ AJAX เพื่อยืนยันว่าทำรายการสำเร็จ
			echo 1;

			// ฟังก์ชันส่ง Telegram
			function sendTelegramNotificationReturnOrder($message)
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

			$telegram_message = "
			ลูกค้าได้ทำการขอ คืนเงิน/คืนสินค้า
			- หมายเลขคำสั่งซื้อ: {$order_code}
			- ลูกค้า: {$customer_name}
			- ที่อยู่จัดส่ง: {$order['delivery_address']}
			- ยอดรวม: " . number_format($order['total_amount'], 2) . " บาท";
			// ส่งข้อความ Telegram
			sendTelegramNotificationReturnOrder($telegram_message);
		} catch (Exception $e) {
			// หากเกิดข้อผิดพลาดใดๆ ใน try block ให้ส่งข้อความ error กลับไปให้ AJAX
			echo $e->getMessage();
		}
	}

	function log_promotion_usage($promotion_id, $customer_id, $order_id, $discount_amount, $items_in_order)
	{
		$query = "
        INSERT INTO `promotion_usage_logs` (`promotion_id`, `customer_id`, `order_id`, `discount_amount`, `items_in_order`, `used_at`)
        VALUES ('{$promotion_id}', '{$customer_id}', '{$order_id}', '{$discount_amount}', '{$items_in_order}', NOW())
    ";

		// บันทึกข้อมูลการใช้งานโปรโมชัน
		if ($this->conn->query($query)) {
			return true;
		} else {
			throw new Exception("ไม่สามารถบันทึกข้อมูลการใช้โปรโมชันได้: " . $this->conn->error);
		}
	}
	function log_coupon_usage($coupon_code_id, $customer_id, $order_id, $discount_amount, $items_in_order)
	{
		$query = "
		INSERT INTO `coupon_code_usage_logs` (`coupon_code_id`, `customer_id`, `order_id`, `discount_amount`, `items_in_order`, `used_at`)
		VALUES ('{$coupon_code_id}', '{$customer_id}', '{$order_id}', '{$discount_amount}', '{$items_in_order}', NOW())
		";

		// บันทึกข้อมูลการใช้งานคูปอง
		if ($this->conn->query($query)) {
			return true;
		} else {
			throw new Exception("ไม่สามารถบันทึกข้อมูลการใช้คูปองได้: " . $this->conn->error);
		}
	}


	function update_tracking_id()
	{
		// 1. รับค่าจาก POST โดยตรง (ปลอดภัยกว่า extract)
		//    และตรวจสอบว่ามีค่าส่งมาหรือไม่
		$tracking_id = $_POST['tracking_id'] ?? null;
		$id = $_POST['id'] ?? null;

		// 2. ตรวจสอบว่ามี ID ของออเดอร์ส่งมาหรือไม่
		if (empty($id)) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'ไม่พบ ID ของออเดอร์';
			return json_encode($resp);
		}

		// 3. เตรียมคำสั่ง SQL (แก้ไข Syntax ที่ผิด และใช้ ? แทนค่า)
		//    (ลบ , หน้า WHERE ออกแล้ว)
		$sql = "UPDATE `order_list` 
            SET `tracking_id` = ? 
            WHERE `id` = ?";

		// 4. ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
		$stmt = $this->conn->prepare($sql);

		// 5. ผูกตัวแปรเข้ากับ ?
		// "s" = string (สำหรับ tracking_id)
		// "i" = integer (สำหรับ id) 
		// (ถ้า id ของคุณเป็นตัวอักษร ให้ใช้ "s" แทน "i" -> "ss")
		$stmt->bind_param("si", $tracking_id, $id);

		// 6. สั่งทำงาน
		$update = $stmt->execute();

		// 7. ส่วนการแจ้งเตือน (คงไว้เหมือนเดิม)
		if ($update) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "อัปเดตเลขขนส่งเรียบร้อยแล้ว");
		} else {
			$resp['status'] = 'failed';
			// ใช้ $stmt->error แทน $this->conn->error จะให้ข้อมูลที่ดีกว่า
			$resp['msg'] = $stmt->error;
		}

		// 8. ปิด statement
		$stmt->close();

		return json_encode($resp);
	}


	function update_order_status()
	{
		extract($_POST);

		$payment_status = isset($_POST['payment_status']) ? (int)$_POST['payment_status'] : 0;
		$delivery_status = isset($_POST['delivery_status']) ? (int)$_POST['delivery_status'] : 0;

		// อัปเดตสถานะและตั้ง is_seen เป็น 0
		$update = $this->conn->query("UPDATE `order_list` 
        SET 
            `payment_status` = '{$payment_status}',
            `delivery_status` = '{$delivery_status}',
            `is_seen` = 0
        WHERE id = '{$id}'");

		if ($update) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "อัปเดตสถานะเรียบร้อยแล้ว");
			// ส่งอีเมลแจ้งลูกค้า
			$this->send_order_status_email($id, $payment_status, $delivery_status);
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error;
		}

		return json_encode($resp);
	}


	function send_order_status_email($order_id, $payment_status, $delivery_status)
	{
		// ดึงข้อมูลคำสั่งซื้อ
		$qry = $this->conn->query("SELECT o.*, c.email, CONCAT(c.firstname, ' ', c.lastname) as customer_name 
        FROM order_list o 
        INNER JOIN customer_list c ON o.customer_id = c.id 
        WHERE o.id = {$order_id}");

		if ($qry->num_rows > 0) {
			$order = $qry->fetch_assoc();
			$order_code = $order['code'];
			$customer_email = $order['email'];
			$customer_name = $order['customer_name'];

			$payment_status_text_map = [
				0 => 'ยังไม่ชำระเงิน',
				1 => 'รอตรวจสอบ',
				2 => 'ชำระเงินแล้ว',
				3 => 'ชำระเงินล้มเหลว',
				4 => 'รอการยกเลิกคำสั่งซื้อ',
				5 => 'กำลังคืนเงิน',
				6 => 'คืนเงินแล้ว',
			];

			$delivery_status_text_map = [
				0 => 'ตรวจสอบคำสั่งซื้อ',
				1 => 'กำลังเตรียมของ',
				2 => 'แพ๊กของแล้ว',
				3 => 'กำลังจัดส่ง',
				4 => 'จัดส่งสำเร็จ',
				5 => 'ส่งไม่สำเร็จ',
				6 => 'รอการยกเลิกคำสั่งซื้อ',
				7 => 'คืนของระหว่างทาง',
				8 => 'กำลังคืนสินค้า',
				9 => 'คืนของสำเร็จ',
				10 => 'ยกเลิกแล้ว',
			];

			// แปลงค่าตัวเลขเป็นข้อความ
			$payment_text = $payment_status_text_map[$payment_status] ?? 'N/A';
			$delivery_text = $delivery_status_text_map[$delivery_status] ?? 'N/A';

			// ตั้งค่าการส่งอีเมล
			$mail = new PHPMailer(true);
			try {
				$mail->isSMTP();
				$mail->Host = 'smtp.gmail.com';
				$mail->Port = 465;
				$mail->SMTPAuth = true;
				$mail->Username = "faritre5566@gmail.com"; // ใส่อีเมลของคุณ
				$mail->Password = "bchljhaxoqflmbys"; // ใส่รหัสอีเมลของคุณ
				$mail->SMTPSecure = "ssl";
				$mail->CharSet = 'UTF-8';
				$mail->isHTML(true);
				$mail->Subject = "สถานะคำสั่งซื้อ #{$order_code}";

				$mail->setFrom('faritre5566@gmail.com', 'MSG.com');
				$mail->addAddress($customer_email, $customer_name);

				// สร้างเนื้อหาอีเมล
				$body = "
					<div style='font-family: Arial, sans-serif; max-width: 600px; margin:auto;'>
						<h2 style='text-align:center;'>สถานะคำสั่งซื้อ #{$order_code}</h2>
						<p>เรียน คุณ <strong>{$customer_name}</strong></p>
						<p><strong>รหัสคำสั่งซื้อ: </strong>{$order_code}</p>
						<p>ขณะนี้สถานะคำสั่งซื้อของคุณได้ถูกอัปเดตแล้ว</p>
						<p>สถานะการชำระเงิน: <strong>{$payment_text}</strong></p>
						<p>สถานะการจัดส่ง: <strong>{$delivery_text}</strong></p>
						<p>📦 ที่อยู่จัดส่ง: {$order['delivery_address']}</p>
						<p>💵 ยอดรวม: " . number_format($order['total_amount'], 2) . " บาท</p>
						<hr>
						<p style='font-size:13px; color:#555;'>หากมีข้อสงสัย กรุณาติดต่อ <a href='mailto:faritre5566@gmail.com'>faritre5566@gmail.com</a></p>
					</div>
           		";

				$mail->Body = $body;
				$mail->send();
			} catch (Exception $e) {
				// หากส่งอีเมลไม่สำเร็จ ให้บันทึก error ไว้ แต่ไม่ต้องหยุดการทำงาน
				error_log("❌ ส่งอีเมลสถานะคำสั่งซื้อไม่สำเร็จ: " . $mail->ErrorInfo);
			}

			$mail_admin = new PHPMailer(true);
			try {
				$mail_admin->isSMTP();
				$mail_admin->Host = 'smtp.gmail.com';
				$mail_admin->Port = 465;
				$mail_admin->SMTPAuth = true;
				$mail_admin->Username = "faritre5566@gmail.com";
				$mail_admin->Password = "bchljhaxoqflmbys";
				$mail_admin->SMTPSecure = "ssl";
				$mail_admin->CharSet = 'UTF-8';
				$mail_admin->isHTML(true);
				$mail_admin->Subject = "อัปเดตสถานะคำสั่งซื้อ #{$order_code}";

				$mail_admin->setFrom('faritre5566@gmail.com', 'MSG.com');
				$mail_admin->addAddress('faritre5566@gmail.com', 'Admin');  // อีเมลแอดมิน
				$mail_admin->addAddress('faritre1@gmail.com', 'Admin');
				$mail_admin->addAddress('faritre4@gmail.com', 'Admin');
				$admin_body = "
					<div style='font-family: Arial, sans-serif; max-width: 600px; margin:auto;'>
						<h2 style='text-align:center;'>อัปเดตสถานะคำสั่งซื้อ #{$order_code}</h2>
						<p>ลูกค้า <strong>{$customer_name}</strong></p>
						<p><strong>รหัสคำสั่งซื้อ: </strong>{$order_code}</p>
						<p>ขณะนี้สถานะคำสั่งซื้อได้ถูกอัปเดตแล้ว</p>
						<p>สถานะการชำระเงิน: <strong>{$payment_text}</strong></p>
						<p>สถานะการจัดส่ง: <strong>{$delivery_text}</strong></p>
						<p>📦 ที่อยู่จัดส่ง: {$order['delivery_address']}</p>
                 		<p>💵 ยอดรวม: " . number_format($order['total_amount'], 2) . " บาท</p>
					</div>
				";

				$mail_admin->Body = $admin_body;
				$mail_admin->send();
			} catch (Exception $e) {
				// หากส่งอีเมลไม่สำเร็จ ให้บันทึก error ไว้ แต่ไม่ต้องหยุดการทำงาน
				error_log("❌ ส่งอีเมลแจ้งยกเลิกไม่สำเร็จ: " . $mail_admin->ErrorInfo);
			}

			function sendTelegramNotificationUpdateOder($message)
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

			$telegram_message = "
			คำสั่งซื้อถูกอัปเดต
			- รหัสคำสั่งซื้อ: {$order_code}
			- ลูกค้า: {$customer_name}
			- ขณะนี้สถานะคำสั่งซื้อได้ถูกอัปเดตแล้ว
			- สถานะการชำระเงิน: {$payment_text}
			- สถานะการจัดส่ง: {$delivery_text}
			- ที่อยู่จัดส่ง: {$order['delivery_address']}
			- ยอดรวม: " . number_format($order['total_amount'], 2) . " บาท
			";
			// ส่งข้อความ Telegram
			sendTelegramNotificationUpdateOder($telegram_message);
		}
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
			$this->settings->set_flashdata('success', 'ลบคำสั่งซื้อเรียบร้อย');
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

	function save_shipping_methods()
	{
		header('Content-Type: application/json; charset=utf-8');

		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']['type'] != 1) {
			http_response_code(403);
			echo json_encode(['status' => 'forbidden', 'message' => 'ไม่มีสิทธิ์ใช้งาน']);
			exit;
		}

		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

		$provider_id = intval($_POST['provider_id'] ?? 0);
		$name = $this->conn->real_escape_string($_POST['display_name'] ?? '');
		$description = $this->conn->real_escape_string($_POST['description'] ?? '');
		$cost = floatval($_POST['cost'] ?? 0);
		$cod_enabled = ($_POST['cod_enabled'] == '1') ? 1 : 0;
		$status = ($_POST['status'] == '1') ? 1 : 0;

		if (!$provider_id || !$name) {
			echo json_encode(['status' => 'failed', 'msg' => 'กรุณากรอกข้อมูลให้ครบ']);
			exit;
		}

		$this->conn->begin_transaction();
		try {
			// ส่วนนี้เหมือนเดิม: บันทึกข้อมูลหลักของ shipping_methods
			if ($id > 0) {
				$sql = "UPDATE `shipping_methods` SET 
                provider_id = '{$provider_id}',
                name = '{$name}', 
                description = '{$description}',
                cost = '{$cost}',
                cod_enabled = '{$cod_enabled}',
                status = '{$status}'
                WHERE id = {$id}";
			} else {
				$sql = "INSERT INTO `shipping_methods` 
                (provider_id, name, description, cost, cod_enabled, status)
                VALUES 
                ('{$provider_id}', '{$name}', '{$description}', '{$cost}', '{$cod_enabled}', '{$status}')";
			}

			if (!$this->conn->query($sql)) {
				throw new Exception($this->conn->error);
			}

			$shipping_methods_id = ($id > 0) ? $id : $this->conn->insert_id;

			// --- [START] แก้ไขส่วนการบันทึกราคาตามน้ำหนัก (UPSERT Logic) ---
			// ข้อควรระวัง: โค้ดนี้ต้องการคอลัมน์ `status` ในตาราง `shipping_prices`

			if ($id > 0) {
				// 1. ถ้าเป็นการแก้ไข (id > 0) ให้ตั้งค่าราคาทั้งหมดเป็น 'inactive' (status = 0) ก่อน
				// เราจะทำการอัปเดต/เปิดใช้งาน (status = 1) เฉพาะรายการที่ส่งมาในฟอร์ม
				$this->conn->query("UPDATE `shipping_prices` SET status = 0 WHERE shipping_methods_id = {$shipping_methods_id}");
			}

			// 2. เตรียม Prepared Statements สำหรับการ UPSERT
			// ใช้เพื่อตรวจสอบว่ามีแถว (ตามช่วงน้ำหนัก) นี้อยู่แล้วหรือไม่
			$stmt_check = $this->conn->prepare("SELECT id FROM shipping_prices WHERE shipping_methods_id = ? AND min_weight = ? AND max_weight = ?");

			// ใช้เพื่ออัปเดตแถวที่มีอยู่ (เปลี่ยนราคา และตั้ง status = 1)
			$stmt_update = $this->conn->prepare("UPDATE shipping_prices SET price = ?, status = 1 WHERE id = ?");

			// ใช้เพื่อเพิ่มแถวใหม่ (พร้อม status = 1)
			// (ต้องเพิ่มคอลัมน์ status ในคำสั่ง INSERT)
			$stmt_insert = $this->conn->prepare("INSERT INTO shipping_prices (shipping_methods_id, min_weight, max_weight, price, status)
                                                VALUES (?, ?, ?, ?, 1)");


			// 3. วนลูปข้อมูลที่ส่งมา
			if (!empty($_POST['weight_from']) && !empty($_POST['weight_to']) && !empty($_POST['price'])) {
				$weight_from = $_POST['weight_from'];
				$weight_to = $_POST['weight_to'];
				$price = $_POST['price'];

				for ($i = 0; $i < count($weight_from); $i++) {
					$w_from = intval($weight_from[$i]);
					$w_to = intval($weight_to[$i]);
					$p = floatval($price[$i]);

					// Validation (เหมือนเดิม)
					if ($w_from >= $w_to) {
						throw new Exception('น้ำหนักเริ่มต้นต้องน้อยกว่าน้ำหนักสูงสุด');
					}
					if ($p < 0) {
						throw new Exception('ราคาต้องไม่ติดลบ');
					}

					// 4. ตรวจสอบว่ามีแถวนี้อยู่แล้วหรือไม่ (เฉพาะกรณีแก้ไข)
					$existing_id = null;
					if ($id > 0) {
						$stmt_check->bind_param('iii', $shipping_methods_id, $w_from, $w_to);
						$stmt_check->execute();
						$result = $stmt_check->get_result();
						if ($result->num_rows > 0) {
							$existing_id = $result->fetch_assoc()['id'];
						}
					}

					if ($existing_id) {
						// 5a. ถ้ามี: อัปเดตราคา และตั้ง status = 1 (เปิดใช้งาน)
						$stmt_update->bind_param('di', $p, $existing_id);
						if (!$stmt_update->execute()) {
							throw new Exception('ไม่สามารถอัปเดตข้อมูลราคาจัดส่งเดิม: ' . $stmt_update->error);
						}
					} else {
						// 5b. ถ้าไม่มี (หรือเป็นการสร้างใหม่): เพิ่มแถวใหม่ด้วย status = 1
						$stmt_insert->bind_param('iiid', $shipping_methods_id, $w_from, $w_to, $p);
						if (!$stmt_insert->execute()) {
							throw new Exception('ไม่สามารถบันทึกข้อมูลราคาจัดส่งใหม่: ' . $stmt_insert->error);
						}
					}
				}
			}
			// (ถ้า $id > 0 และไม่มีการส่ง 'weight_from' มาเลย หมายถึงผู้ใช้ลบออกทั้งหมด
			// รายการทั้งหมดจะถูกตั้งค่าเป็น status = 0 จากขั้นตอนที่ 1 โดยอัตโนมัติ)

			// 6. ปิด statements
			$stmt_check->close();
			$stmt_update->close();
			$stmt_insert->close();

			// --- [END] แก้ไขส่วนการบันทึกราคาตามน้ำหนัก ---


			$this->conn->commit();
			echo json_encode(['status' => 'success']);
		} catch (Exception $e) {
			$this->conn->rollback();
			echo json_encode(['status' => 'failed', 'msg' => $e->getMessage()]);
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
			$this->settings->set_flashdata('success', 'ลบขนส่งเรียบร้อย');
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
	function save_promotions()
	{
		// ใช้ real_escape_string กับ description เพื่อความปลอดภัย
		$_POST['description'] = $this->conn->real_escape_string(htmlspecialchars($_POST['description']));
		extract($_POST);

		$data = "";
		// วนลูปสร้าง string สำหรับ SQL query จากข้อมูลที่ส่งมา
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'img'))) { // ไม่ต้องนำ id และ img เข้าไปใน data string
				if (!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}

		// --- ส่วนจัดการรูปภาพที่ย้ายขึ้นมาและแก้ไขใหม่ ---
		$image_path_sql = ""; // เตรียมตัวแปรสำหรับเก็บ path รูป
		if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name'])) {
			// ตรวจสอบว่ามีไฟล์ถูกอัปโหลดมาหรือไม่
			if ($_FILES['img']['error'] != UPLOAD_ERR_OK) {
				$resp['status'] = 'failed';
				$resp['msg'] = "ไม่สามารถอัปโหลดไฟล์ได้";
				return json_encode($resp);
			}

			$upload_dir = "uploads/promotions/";
			if (!is_dir(base_app . $upload_dir)) {
				mkdir(base_app . $upload_dir, 0777, true); // ใช้ 0777 เพื่อให้แน่ใจว่าเขียนไฟล์ได้
			}

			$accept = ['image/jpeg', 'image/png'];
			if (!in_array($_FILES['img']['type'], $accept)) {
				$resp['status'] = 'failed';
				$resp['msg'] = "อัปโหลดไฟล์ได้แค่ .JPG / .PNG";
				return json_encode($resp);
			}

			// สร้างชื่อไฟล์ใหม่เพื่อป้องกันการซ้ำ และบังคับเป็น .webp
			// $file_extension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION); // <-- ไม่ใช้ตัวนี้
			$filename = uniqid('promo_') . '_' . time() . '.webp'; // <-- บังคับนามสกุล .webp
			$full_path = base_app . $upload_dir . $filename;

			// ใช้ move_uploaded_file หรือฟังก์ชัน resize ของคุณ
			// สมมติว่า resize_image จะย้ายไฟล์และคืนค่า true/false
			// 1. กำหนดขนาดและคุณภาพ (ตามที่คุณต้องการ)
			$max_width = 1000;  // ขนาดเดียว (กว้างไม่เกิน 1000)
			$max_height = 600; // ขนาดเดียว (สูงไม่เกิน 600)
			$quality = 80;     // คุณภาพ (80% คือ กลางค่อนข้างชัด)

			// 2. เรียกใช้ฟังก์ชัน resize พร้อมระบุคุณภาพ
			$success = $this->resize_image_to_webp($_FILES['img']['tmp_name'], $full_path, $max_width, $max_height, $quality);

			if ($success) {
				// ถ้าย้าย/resize รูปสำเร็จ ให้เตรียม SQL สำหรับคอลัมน์ image_path
				$db_path = $this->conn->real_escape_string($upload_dir . $filename);
				$image_path_sql = ", `image_path` = '{$db_path}?v=" . time() . "'";
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "ไม่สามารถอัปโหลดรูปภาพ หรือปรับขนาดรูปภาพได้";
				return json_encode($resp);
			}
		}
		// --- จบส่วนจัดการรูปภาพ ---

		// ต่อสตริงของ path รูปภาพเข้าไปในข้อมูลหลัก
		if (!empty($image_path_sql)) {
			$data .= $image_path_sql;
		}

		// ตรวจสอบข้อมูลซ้ำ
		$check = $this->conn->query("SELECT * FROM `promotions_list` where `name` = '{$name}' and delete_flag = 0 " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "มีโปรโมชันนี้อยู่แล้ว";
			return json_encode($resp);
		}

		// สร้างหรืออัปเดตข้อมูลในครั้งเดียว
		if (empty($id)) {
			$sql = "INSERT INTO `promotions_list` set {$data} ";
		} else {
			$sql = "UPDATE `promotions_list` set {$data} where id = '{$id}' ";
		}

		$save = $this->conn->query($sql);
		if ($save) {
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			$resp['msg'] = empty($id) ? "สร้างโปรโมชั่นใหม่เรียบร้อย" : "อัปเดตโปรโมชั่นเรียบร้อย";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}

		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);

		return json_encode($resp);
	}
	function delete_promotion()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `promotions_list` WHERE id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "ลบโปรโมชันแล้ว");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function save_promotion_products()
	{
		// Sanitize and escape inputs
		$promotion_id = isset($_POST['promotion_id']) ? $_POST['promotion_id'] : null;
		$product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];

		// Check if promotion_id is provided
		if (empty($promotion_id)) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'จำเป็นต้องมีรหัสโปรโมชั่น';
			return json_encode($resp); // ส่งผลลัพธ์กลับเป็น JSON
		}

		if (empty($product_ids)) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'กรุณาเลือกสินค้าอย่างน้อยหนึ่งรายการ';
			return json_encode($resp); // ส่งผลลัพธ์กลับเป็น JSON
		}

		// Delete existing products for this promotion (if any)
		$delete_query = "DELETE FROM `promotion_products` WHERE `promotion_id` = {$promotion_id}";
		$this->conn->query($delete_query); // Assuming no errors

		// Insert selected products
		foreach ($product_ids as $product_id) {
			$product_id = intval($product_id);
			$sql = "INSERT INTO `promotion_products` (`promotion_id`, `product_id`) VALUES ('{$promotion_id}', '{$product_id}')";
			$this->conn->query($sql);
		}

		// Check for errors
		if ($this->conn->affected_rows > 0) {
			// ถ้าสำเร็จ
			$this->settings->set_flashdata('success', "เพิ่มสินค้าสำเร็จ");
			return json_encode(array('status' => 'success'));
		} else {
			// ถ้าล้มเหลว
			return json_encode(array('status' => 'failed', 'error' => $this->conn->error));
		}
	}


	function delete_promotion_product()
	{
		extract($_POST);
		$qry = $this->conn->prepare("DELETE FROM `promotion_products` where id = ?");
		$qry->bind_param("i", $id); // "i" for integer
		$resp = $qry->execute();

		if ($resp) {
			$this->settings->set_flashdata('success', "ลบสินค้าออกจากโปรโมชันสำเร็จ");
			return json_encode(array('status' => 'success'));
		} else {
			return json_encode(array('status' => 'failed', 'error' => $this->conn->error));
		}
	}

	function delete_all_promotion_products()
	{
		// รับค่า promotion_id ที่ส่งมาจาก AJAX
		extract($_POST);

		// ตรวจสอบว่ามี promotion_id ส่งมาหรือไม่
		if (!isset($promotion_id)) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Promotion ID is missing.';
			return json_encode($resp);
		}

		// สร้างคำสั่ง SQL เพื่อลบข้อมูล
		// ใช้ prepared statement เพื่อความปลอดภัย
		$sql = "DELETE FROM `promotion_products` WHERE promotion_id = ?";
		$stmt = $this->conn->prepare($sql);

		// ผูกค่า promotion_id เข้ากับคำสั่ง SQL
		$stmt->bind_param("i", $promotion_id);

		// สั่งให้คำสั่ง SQL ทำงาน
		$delete = $stmt->execute();

		if ($delete) {
			// หากลบสำเร็จ
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "ลบสินค้าทั้งหมดออกจากโปรโมชันเรียบร้อยแล้ว");
		} else {
			// หากลบไม่สำเร็จ
			$resp['status'] = 'failed';
			$resp['msg'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $this->conn->error;
		}

		// ส่งผลลัพธ์กลับไปให้ JavaScript ในรูปแบบ JSON
		return json_encode($resp);
	}


	function save_promotion_category()
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
		$check = $this->conn->query("SELECT * FROM `promotion_category` where `name` = '{$name}' and delete_flag = 0 " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "มีหมวดหมู่โปรโมชันนี้อยู่แล้ว";
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `promotion_category` set {$data} ";
		} else {
			$sql = "UPDATE `promotion_category` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if ($save) {
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = " สร้างโปรโมชันใหม่เรียบร้อย";
			else
				$resp['msg'] = " อัปเดตโปรโมชันเรียบร้อย";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}

	function delete_promotion_category()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `promotion_category` set `delete_flag` = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "ลบหมวดหมู่โปรโมชันเรียบร้อย");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function save_coupon_code()
	{
		// 1. สร้าง "Whitelist" ของคอลัมน์ที่อนุญาตให้บันทึกได้ เพื่อความปลอดภัย
		$allowed_columns = [
			'name',
			'coupon_code',
			'description',
			'type',
			'cpromo',
			'discount_value',
			'minimum_order',
			'limit_coupon',
			'unl_coupon',
			'coupon_amount',
			'unl_amount',
			'all_products_status',
			'start_date',
			'end_date',
			'status'
		];

		// ใช้ extract($_POST) เพื่อให้ง่ายต่อการเรียกใช้ตัวแปร แต่ต้องระมัดระวัง
		extract($_POST);

		// 2. ตรวจสอบข้อมูลซ้ำซ้อนจาก `coupon_code` (ไม่ใช่ `name`) เพราะเป็น UNIQUE KEY
		$escaped_coupon_code = $this->conn->real_escape_string($coupon_code);
		$check_sql = "SELECT id FROM `coupon_code_list` WHERE `coupon_code` = '{$escaped_coupon_code}' AND delete_flag = 0";
		if (!empty($id)) {
			$check_sql .= " AND id != '{$id}'";
		}

		$check = $this->conn->query($check_sql);
		if ($check && $check->num_rows > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "ไม่สามารถบันทึกได้: รหัสคูปอง '{$coupon_code}' นี้มีอยู่แล้วในระบบ";
			return json_encode($resp);
		}

		// 3. สร้างส่วน SET ของคำสั่ง SQL จาก Whitelist ที่กำหนดไว้
		$data_parts = [];
		foreach ($allowed_columns as $col) {
			// ตรวจสอบว่ามีข้อมูลส่งมาจากฟอร์มหรือไม่
			if (isset($_POST[$col])) {
				$value = $this->conn->real_escape_string($_POST[$col]);

				// ถ้าค่าที่ส่งมาเป็นสตริงว่าง ให้บันทึกเป็น NULL (ถ้าคอลัมน์อนุญาต)
				// โดยเฉพาะสำหรับฟิลด์ตัวเลขที่อาจไม่ได้กรอก
				if ($value === '' && in_array($col, ['discount_value', 'minimum_order', 'limit_coupon', 'coupon_amount'])) {
					$data_parts[] = "`{$col}` = NULL";
				} else {
					$data_parts[] = "`{$col}` = '{$value}'";
				}
			}
		}

		// 4. จัดการข้อมูลจาก Checkbox และ Radio button เป็นพิเศษ
		// ถ้า status ไม่ได้ถูกติ๊ก (ไม่ถูกส่งมา) ให้กำหนดค่าเป็น 0
		if (!isset($_POST['status'])) {
			$data_parts[] = "`status` = '0'";
		}

		// ถ้า unl_coupon ไม่ได้ถูกติ๊ก ให้กำหนดค่าเป็น 0
		if (!isset($_POST['unl_coupon'])) {
			$data_parts[] = "`unl_coupon` = '0'";
		}
		// ถ้าติ๊ก "ไม่จำกัดจำนวน" (`unl_coupon`=1) ให้ล้างค่า `limit_coupon` เป็น NULL
		if (isset($_POST['unl_coupon']) && $_POST['unl_coupon'] == 1) {
			// ค้นหาและลบ `limit_coupon` ที่อาจถูกเพิ่มไปแล้ว
			$data_parts = array_filter($data_parts, function ($part) {
				return strpos($part, '`limit_coupon`') === false;
			});
			// เพิ่มค่าที่ถูกต้องเข้าไปใหม่
			$data_parts[] = "`limit_coupon` = NULL";
		}

		// ถ้า unl_amount ไม่ได้ถูกติ๊ก ให้กำหนดค่าเป็น 0
		if (!isset($_POST['unl_amount'])) {
			$data_parts[] = "`unl_amount` = '0'";
		}
		// ถ้าติ๊ก "ไม่จำกัดจำนวน" (`unl_amount`=1) ให้ล้างค่า `coupon_amount` เป็น NULL
		if (isset($_POST['unl_amount']) && $_POST['unl_amount'] == 1) {
			// ค้นหาและลบ `coupon_amount` ที่อาจถูกเพิ่มไปแล้ว
			$data_parts = array_filter($data_parts, function ($part) {
				return strpos($part, '`coupon_amount`') === false;
			});
			// เพิ่มค่าที่ถูกต้องเข้าไปใหม่
			$data_parts[] = "`coupon_amount` = NULL";
		}
		$data = implode(", ", $data_parts);

		// 5. สร้างและรันคำสั่ง SQL
		if (empty($id)) {
			// สร้างข้อมูลใหม่
			$sql = "INSERT INTO `coupon_code_list` SET {$data}";
		} else {
			// อัปเดตข้อมูลเดิม
			$sql = "UPDATE `coupon_code_list` SET {$data} WHERE id = '{$id}'";
		}

		$save = $this->conn->query($sql);

		if ($save) {
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if (empty($id)) {
				$resp['msg'] = "สร้างคูปองเรียบร้อย";
			} else {
				$resp['msg'] = "อัปเดตคูปองเรียบร้อย";
			}
			// บันทึกข้อความลง session flash
			$this->settings->set_flashdata('success', $resp['msg']);
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลลงฐานข้อมูล';
			$resp['error'] = $this->conn->error; // ส่ง error ของ SQL กลับไปด้วยเพื่อช่วยในการดีบัก
			$resp['sql'] = $sql; // ส่งคำสั่ง SQL ที่ผิดพลาดกลับไปดูด้วย
		}

		return json_encode($resp);
	}

	function delete_coupon_code()
	{

		extract($_POST);
		// เปลี่ยนค่า delete_flag เป็น 1 และ status เป็น 0 แทนการลบจริง
		$update = $this->conn->query("UPDATE `coupon_code_list` 
                                  SET `status` = 0 ,`delete_flag` = 1
                                  WHERE `id` = '{$id}'");

		if ($update) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "ลบโค้ดคูปองแล้ว");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function save_coupon_code_products()
	{
		// Sanitize and escape inputs
		$coupon_code_id = isset($_POST['coupon_code_id']) ? $_POST['coupon_code_id'] : null;
		$product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];

		// Check if coupon_code_id is provided
		if (empty($coupon_code_id)) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Coupon Code ID is required.';
			return json_encode($resp); // ส่งผลลัพธ์กลับเป็น JSON
		}

		if (empty($product_ids)) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'At least one product must be selected.';
			return json_encode($resp); // ส่งผลลัพธ์กลับเป็น JSON
		}

		// Delete existing products for this promotion (if any)
		$delete_query = "DELETE FROM `coupon_code_products` WHERE `coupon_code_id` = {$coupon_code_id}";
		$this->conn->query($delete_query); // Assuming no errors

		// Insert selected products
		foreach ($product_ids as $product_id) {
			$product_id = intval($product_id);
			$sql = "INSERT INTO `coupon_code_products` (`coupon_code_id`, `product_id`) VALUES ('{$coupon_code_id}', '{$product_id}')";
			$this->conn->query($sql);
		}

		// Check for errors
		if ($this->conn->affected_rows > 0) {
			// ถ้าสำเร็จ
			$this->settings->set_flashdata('success', "เพิ่มสินค้าสำเร็จ");
			return json_encode(array('status' => 'success'));
		} else {
			// ถ้าล้มเหลว
			return json_encode(array('status' => 'failed', 'error' => $this->conn->error));
		}
	}



	function delete_coupon_code_products()
	{
		extract($_POST);
		$qry = $this->conn->prepare("DELETE FROM `coupon_code_products` where id = ?");
		$qry->bind_param("i", $id); // "i" for integer
		$resp = $qry->execute();

		if ($resp) {
			$this->settings->set_flashdata('success', "ลบสินค้าออกจากโค้ดคูปองเรียบร้อยแล้ว");
			return json_encode(array('status' => 'success'));
		} else {
			return json_encode(array('status' => 'failed', 'error' => $this->conn->error));
		}
	}
	// ใส่ฟังก์ชันนี้ภายใน Class Master ของคุณในไฟล์ classes/Master.php

	function delete_all_coupon_products()
	{
		// รับค่า coupon_id ที่ส่งมาจาก AJAX
		extract($_POST);

		// ตรวจสอบว่ามี coupon_id ส่งมาหรือไม่
		if (!isset($coupon_id)) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Coupon ID is missing.';
			return json_encode($resp);
		}

		// สร้างคำสั่ง SQL เพื่อลบข้อมูล
		// ใช้ prepared statement เพื่อความปลอดภัย
		$sql = "DELETE FROM `coupon_code_products` WHERE coupon_code_id = ?";
		$stmt = $this->conn->prepare($sql);

		// ผูกค่า coupon_id เข้ากับคำสั่ง SQL
		$stmt->bind_param("i", $coupon_id);

		// สั่งให้คำสั่ง SQL ทำงาน
		$delete = $stmt->execute();

		if ($delete) {
			// หากลบสำเร็จ
			$resp['status'] = 'success';
			// สามารถตั้งค่า flash message เพื่อแสดงผลหลัง reload ได้ (ถ้ามีระบบรองรับ)
			$this->settings->set_flashdata('success', "ลบสินค้าทั้งหมดออกจากคูปองเรียบร้อยแล้ว");
		} else {
			// หากลบไม่สำเร็จ
			$resp['status'] = 'failed';
			$resp['msg'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $this->conn->error;
		}

		// ส่งผลลัพธ์กลับไปให้ JavaScript ในรูปแบบ JSON
		return json_encode($resp);
	}

	function apply_coupon($conn, $post_data)
	{
		// ตรวจสอบให้แน่ใจว่าลูกค้า login อยู่ และมี customer_id
		if (!isset($_SESSION['userdata']['id']) || $_SESSION['userdata']['login_type'] != 2) {
			echo json_encode(['success' => false, 'error' => 'กรุณาเข้าสู่ระบบก่อนใช้คูปอง']);
			exit;
		}
		$customer_id = $_SESSION['userdata']['id'];

		// รับข้อมูลจาก AJAX
		$coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : '';
		$cart_items = isset($_POST['cart_items']) ? $_POST['cart_items'] : [];
		$cart_total = isset($_POST['cart_total']) ? floatval($_POST['cart_total']) : 0; // ยอดรวมเฉพาะสินค้า

		// ตรวจสอบข้อมูลเบื้องต้น
		if (empty($coupon_code) || empty($cart_items) || $cart_total <= 0) {
			echo json_encode(['success' => false, 'error' => 'ข้อมูลไม่ครบถ้วน']);
			exit;
		}

		// 1. ค้นหาและตรวจสอบคูปอง
		$qry = $conn->prepare("SELECT * FROM coupon_code_list WHERE coupon_code = ? AND status = 1 AND delete_flag = 0");
		$qry->bind_param("s", $coupon_code);
		$qry->execute();
		$result = $qry->get_result();

		if ($result->num_rows === 0) {
			echo json_encode(['success' => false, 'error' => 'รหัสคูปองไม่ถูกต้อง']);
			exit;
		}

		$coupon = $result->fetch_assoc();
		$coupon_code_id = $coupon['id']; // ดึง ID ของคูปองไว้ใช้

		// 2. ตรวจสอบเงื่อนไขของคูปอง (วันหมดอายุ, ยอดขั้นต่ำ)
		$current_date = date('Y-m-d H:i:s');
		if ($coupon['start_date'] > $current_date || $coupon['end_date'] < $current_date) {
			echo json_encode(['success' => false, 'error' => 'คูปองหมดอายุแล้ว']);
			exit;
		}

		if ($cart_total < $coupon['minimum_order']) {
			$needed = number_format($coupon['minimum_order'] - $cart_total, 2);
			echo json_encode(['success' => false, 'error' => "ยอดสั่งซื้อขั้นต่ำ {$coupon['minimum_order']} บาท (ขาดอีก {$needed} บาท)"]);
			exit;
		}

		// =================================================================
		// >> ส่วนที่เพิ่มเข้ามาใหม่: ตรวจสอบจำนวนการใช้งาน <<
		// =================================================================

		// 2.1 ตรวจสอบจำนวนครั้งที่ลูกค้าคนนี้ใช้คูปองไปแล้ว (Per-Customer Usage Limit)
		// ทำการตรวจสอบก็ต่อเมื่อคูปองไม่ได้ถูกตั้งค่าให้ใช้ได้ไม่จำกัดครั้ง (unl_coupon = 0)
		if ($coupon['unl_coupon'] == 0) {
			$usage_qry = $conn->prepare("SELECT COUNT(id) AS total_usage FROM coupon_code_usage_logs WHERE coupon_code_id = ? AND customer_id = ?");
			$usage_qry->bind_param("ii", $coupon_code_id, $customer_id);
			$usage_qry->execute();
			$usage_result = $usage_qry->get_result()->fetch_assoc();
			$times_used_by_customer = $usage_result['total_usage'];

			if ($times_used_by_customer >= $coupon['limit_coupon']) {
				echo json_encode(['success' => false, 'error' => 'คุณใช้คูปองนี้ครบจำนวนครั้งที่กำหนดแล้ว']);
				exit;
			}
		}

		// ประกาศตัวแปรสำหรับเก็บข้อความแจ้งเตือนเรื่องคูปองเหลือน้อย
		$quantity_warning_message = '';

		// 2.2 ตรวจสอบจำนวนคูปองทั้งหมดที่ถูกใช้ไปแล้ว (Overall Coupon Quantity)
		// ทำการตรวจสอบก็ต่อเมื่อคูปองไม่ได้ถูกตั้งค่าให้มีจำนวนไม่จำกัด (unl_amount = 0)
		if ($coupon['unl_amount'] == 0) {
			$amount_qry = $conn->prepare("SELECT COUNT(id) AS total_used FROM coupon_code_usage_logs WHERE coupon_code_id = ?");
			$amount_qry->bind_param("i", $coupon_code_id);
			$amount_qry->execute();
			$amount_result = $amount_qry->get_result()->fetch_assoc();
			$total_times_used = $amount_result['total_used'];

			// คำนวณหาจำนวนคูปองที่เหลืออยู่
			$remaining_coupons = $coupon['coupon_amount'] - $total_times_used;

			// ตรวจสอบว่าคูปองหมดหรือยัง
			if ($remaining_coupons <= 0) {
				echo json_encode(['success' => false, 'error' => 'คูปองหมดแล้ว']);
				exit;
			}

			// ถ้าคูปองที่เหลือมีจำนวนน้อยกว่า 4 ให้สร้างข้อความแจ้งเตือน
			if ($remaining_coupons < 4) {
				$quantity_warning_message = " เหลือคูปอง {$remaining_coupons} ชิ้น";
			}
		}


		// 3. คำนวณส่วนลดตามเงื่อนไข all_products_status
		$discount_amount = 0;
		$base_total_for_discount = 0; // ยอดรวมที่จะใช้เป็นฐานในการคำนวณส่วนลด

		if ($coupon['all_products_status'] == 1) {
			// ---- กรณี 1: ใช้ได้กับสินค้าทุกชิ้น ----
			$base_total_for_discount = $cart_total;
		} else {
			$product_qry = $conn->prepare("SELECT product_id FROM coupon_code_products WHERE coupon_code_id = ?");
			$product_qry->bind_param("i", $coupon_code_id);
			$product_qry->execute();
			$product_result = $product_qry->get_result();

			$eligible_product_ids = [];
			while ($row = $product_result->fetch_assoc()) {
				$eligible_product_ids[] = $row['product_id'];
			}

			if (empty($eligible_product_ids)) {
				echo json_encode(['success' => false, 'error' => 'คูปองนี้ไม่มีสินค้าที่ร่วมรายการ']);
				exit;
			}

			// วนลูปสินค้าในตะกร้า เพื่อหายอดรวมของสินค้าที่ร่วมรายการเท่านั้น
			foreach ($cart_items as $item) {
				if (in_array($item['product_id'], $eligible_product_ids)) {
					// ราคาสินค้าต่อชิ้น * จำนวน
					$base_total_for_discount += (floatval($item['price']) * intval($item['quantity']));
				}
			}

			if ($base_total_for_discount <= 0) {
				echo json_encode(['success' => false, 'error' => 'ไม่มีสินค้าที่ร่วมรายการในตะกร้าของคุณ']);
				exit;
			}
		}


		// 4. คำนวณยอดส่วนลดจากประเภทของคูปอง
		$message = "";
		switch ($coupon['type']) {
			case 'fixed':
				$discount_amount = floatval($coupon['discount_value']);
				$message = "ส่วนลด " . number_format($discount_amount, 2) . " บาท";
				break;
			case 'percent':
				$discount_value = floatval($coupon['discount_value']);
				$discount_amount = $base_total_for_discount * ($discount_value / 100);
				$message = "ส่วนลด {$discount_value}%";
				break;
			case 'free_shipping':
				$message = "คูปองส่งฟรี";
				break;
		}

		// 5. ส่งผลลัพธ์กลับไปเป็น JSON
		$response = [
			'success' => true,
			'coupon_code_id' => $coupon['id'],
			'type' => $coupon['type'],
			'discount_amount' => round($discount_amount, 2),
			'quantity_warning_message' => $quantity_warning_message,
			'message' => $message // นำข้อความแจ้งเตือนมาต่อท้าย
		];

		echo json_encode($response);
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
	case 'delete_gallery_image':
		echo $Master->delete_gallery_image();
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

	case 'get_shipping_details':
		$Master->get_shipping_details();
		break;

	case 'place_order':
		$result = $Master->place_order();
		ob_end_clean();  // เคลียร์ buffer
		echo $result;
		break;

	case 'approve_slip':
		$result = $Master->approve_slip();
		break;

	case 'cancel_order':
		echo $Master->cancel_order();
		break;
	case 'return_order':
		echo $Master->return_order();
		break;
	case 'log_promotion_usage':
		if (isset($promotion_id) && isset($customer_id) && isset($oid) && isset($promotion_discount) && isset($cart_data)) {
			echo $Master->log_promotion_usage($promotion_id, $customer_id, $oid, $promotion_discount, count($cart_data));
		}
		break;
	case 'save_shipping_methods':
		echo $Master->save_shipping_methods();
		break;
	case 'delete_order':
		echo $Master->delete_order();
		break;
	case 'update_tracking_id':
		echo $Master->update_tracking_id();
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
	case 'save_promotions':
		echo $Master->save_promotions();
		break;
	case 'delete_promotion':
		echo $Master->delete_promotion();
		break;
	case 'save_promotion_products':
		echo $Master->save_promotion_products();
		break;
	case 'delete_promotion_product':
		echo $Master->delete_promotion_product();
		break;
	case 'delete_all_promotion_products':
		echo $Master->delete_all_promotion_products();
		break;
	case 'save_promotion_category':
		echo $Master->save_promotion_category();
		break;
	case 'delete_promotion_category':
		echo $Master->delete_promotion_category();
		break;
	case 'save_coupon_code':
		echo $Master->save_coupon_code();
		break;
	case 'delete_coupon_code':
		echo $Master->delete_coupon_code();
		break;
	case 'save_coupon_code_products':
		echo $Master->save_coupon_code_products();
		break;
	case 'delete_coupon_code_products':
		echo $Master->delete_coupon_code_products();
		break;
	case 'delete_all_coupon_products':
		echo $Master->delete_all_coupon_products();
		break;
	case 'apply_coupon':
		header('Content-Type: application/json');
		echo $Master->apply_coupon($conn, $_POST);
		break;
	default:
		// echo $sysset->index();
		break;
}
