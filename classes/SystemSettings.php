<?php
if (!class_exists('DBConnection')) {
	require_once('../config.php');
	require_once('DBConnection.php');
}
class SystemSettings extends DBConnection
{
	public function __construct()
	{
		parent::__construct();
	}
	function __destruct() {}
	function check_connection()
	{
		return ($this->conn);
	}
	function load_system_info()
	{
		if (!isset($_SESSION['system_info'])) {
			$sql = "SELECT * FROM system_info";

			$qry = $this->conn->query($sql);
			while ($row = $qry->fetch_assoc()) {
				$_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
			}
		}
	}
	function update_system_info()
	{
		$sql = "SELECT * FROM system_info";
		$qry = $this->conn->query($sql);
		while ($row = $qry->fetch_assoc()) {
			if (isset($_SESSION['system_info'][$row['meta_field']])) unset($_SESSION['system_info'][$row['meta_field']]);
			$_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
		}
		return true;
	}
	function update_settings_info()
	{
		$data = "";
		foreach ($_POST as $key => $value) {
			if (!in_array($key, array("content")))
				if (isset($_SESSION['system_info'][$key])) {
					$value = str_replace("'", "&apos;", $value);
					$qry = $this->conn->query("UPDATE system_info set meta_value = '{$value}' where meta_field = '{$key}' ");
				} else {
					$qry = $this->conn->query("INSERT into system_info set meta_value = '{$value}', meta_field = '{$key}' ");
				}
		}
		if (isset($_POST['content'])) {
			foreach ($_POST['content'] as $k => $v) {
				$v = addslashes(htmlspecialchars($v));
				file_put_contents("../$k.html", $v);
			}
		}
		if (!empty($_FILES['img']['tmp_name'])) {
			$ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
			$fname = "uploads/logo.{$ext}";
			$accept = array('image/jpeg', 'image/png');

			if (!in_array($_FILES['img']['type'], $accept)) {
				$err = "Image file type is invalid";
			} else {
				if ($_FILES['img']['type'] == 'image/jpeg') {
					$uploadfile = imagecreatefromjpeg($_FILES['img']['tmp_name']);
				} elseif ($_FILES['img']['type'] == 'image/png') {
					$uploadfile = imagecreatefrompng($_FILES['img']['tmp_name']);
				}

				if (!$uploadfile) {
					$err = "Image is invalid";
				} else {
					// ปรับขนาดให้ชัดด้วย imagecopyresampled
					$width = 200;
					$height = 200;
					$temp = imagecreatetruecolor($width, $height);

					// ถ้าเป็น PNG ต้อง preserve transparency
					if ($_FILES['img']['type'] == 'image/png') {
						imagealphablending($temp, false);
						imagesavealpha($temp, true);
					}

					$src_w = imagesx($uploadfile);
					$src_h = imagesy($uploadfile);
					imagecopyresampled($temp, $uploadfile, 0, 0, 0, 0, $width, $height, $src_w, $src_h);

					// ลบไฟล์เดิม
					if (is_file(base_app . $fname)) {
						unlink(base_app . $fname);
					}

					// บันทึกไฟล์ใหม่
					if ($_FILES['img']['type'] == 'image/jpeg') {
						$upload = imagejpeg($temp, base_app . $fname, 95); // quality 95
					} elseif ($_FILES['img']['type'] == 'image/png') {
						$upload = imagepng($temp, base_app . $fname, 1); // compression low = ชัด
					}

					if ($upload) {
						if (isset($_SESSION['system_info']['logo'])) {
							$qry = $this->conn->query("UPDATE system_info 
                        SET meta_value = CONCAT('{$fname}', '?v=', unix_timestamp(CURRENT_TIMESTAMP)) 
                        WHERE meta_field = 'logo'");

							if (is_file(base_app . $_SESSION['system_info']['logo'])) {
								unlink(base_app . $_SESSION['system_info']['logo']);
							}
						} else {
							$qry = $this->conn->query("INSERT INTO system_info SET meta_value = '{$fname}', meta_field = 'logo'");
						}
					}

					imagedestroy($temp);
					imagedestroy($uploadfile);
				}
			}
		}

		if (!empty($_FILES['cover']['tmp_name'])) {
			$ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
			$fname = "uploads/cover.png";
			$accept = array('image/jpeg', 'image/png');
			if (!in_array($_FILES['cover']['type'], $accept)) {
				$err = "Image file type is invalid";
			}
			if ($_FILES['cover']['type'] == 'image/jpeg')
				$uploadfile = imagecreatefromjpeg($_FILES['cover']['tmp_name']);
			elseif ($_FILES['cover']['type'] == 'image/png')
				$uploadfile = imagecreatefrompng($_FILES['cover']['tmp_name']);
			if (!$uploadfile) {
				$err = "Image is invalid";
			}
			list($width, $height) = getimagesize($_FILES['cover']['tmp_name']);
			$temp = imagescale($uploadfile, $width, $height);
			if (is_file(base_app . $fname))
				unlink(base_app . $fname);
			$upload = imagepng($temp, base_app . $fname);
			if ($upload) {
				if (isset($_SESSION['system_info']['cover'])) {
					$qry = $this->conn->query("UPDATE system_info set meta_value = CONCAT('{$fname}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where meta_field = 'cover' ");
					if (is_file(base_app . $_SESSION['system_info']['cover'])) unlink(base_app . $_SESSION['system_info']['cover']);
				} else {
					$qry = $this->conn->query("INSERT into system_info set meta_value = '{$fname}',meta_field = 'cover' ");
				}
			}
			imagedestroy($temp);
		}

		if (isset($_FILES['banners']) && count($_FILES['banners']['tmp_name']) > 0) {
			$err = '';
			$banner_path = "uploads/banner/";
			if (!is_dir(base_app . $banner_path)) {
				mkdir(base_app . $banner_path, 0755, true);
			}

			$target_width = 1920;
			$target_height = 600;

			// วนลูปตามลำดับที่ JavaScript (stagedFiles) ส่งมา
			foreach ($_FILES['banners']['tmp_name'] as $k => $v) {
				if (!empty($_FILES['banners']['tmp_name'][$k])) {

					// 1. อ่านข้อมูลไฟล์จริง (เช็คไส้ใน ไม่สนนามสกุล)
					$imgInfo = getimagesize($_FILES['banners']['tmp_name'][$k]);
					$uploadfile = null;

					if ($imgInfo) {
						// 2. สร้าง Object รูปภาพตามชนิดไฟล์จริง
						switch ($imgInfo[2]) {
							case IMAGETYPE_JPEG:
								$uploadfile = imagecreatefromjpeg($_FILES['banners']['tmp_name'][$k]);
								break;
							case IMAGETYPE_PNG:
								$uploadfile = imagecreatefrompng($_FILES['banners']['tmp_name'][$k]);
								break;
							case IMAGETYPE_GIF:
								$uploadfile = imagecreatefromgif($_FILES['banners']['tmp_name'][$k]);
								break;
						}
					}

					// ถ้าไฟล์เสีย หรือไม่ใช่รูปภาพที่รองรับ
					if (!$uploadfile) {
						$err = "Image is invalid (ไฟล์: " . $_FILES['banners']['name'][$k] . ")";
						continue; // ข้ามไฟล์นี้ไป
					}

					// ได้ขนาดภาพต้นฉบับ
					$width = $imgInfo[0];
					$height = $imgInfo[1];

					// 3. เตรียม Canvas ปลายทาง (ตามขนาดที่กำหนดไว้ 1920x600)
					$temp = imagecreatetruecolor($target_width, $target_height);

					// จัดการพื้นหลังโปร่งใส
					imagealphablending($temp, false);
					imagesavealpha($temp, true);
					$transparent = imagecolorallocatealpha($temp, 255, 255, 255, 127);
					imagefilledrectangle($temp, 0, 0, $target_width, $target_height, $transparent);

					// 4. คำนวณสัดส่วนแบบ Contain (เห็นภาพครบ ไม่โดนตัด)
					$target_ratio = $target_width / $target_height;
					$source_ratio = $width / $height;

					if ($source_ratio > $target_ratio) {
						// ภาพกว้างกว่า: ปรับความกว้างให้พอดี ความสูงจะลดลง
						$new_width = $target_width;
						$new_height = (int)($target_width / $source_ratio);
					} else {
						// ภาพสูงกว่า: ปรับความสูงให้พอดี ความกว้างจะลดลง
						$new_height = $target_height;
						$new_width = (int)($target_height * $source_ratio);
					}

					// หาจุดวางภาพให้อยู่กึ่งกลาง
					$dest_x = (int)(($target_width - $new_width) / 2);
					$dest_y = (int)(($target_height - $new_height) / 2);

					// วาดภาพลงบน Canvas
					imagecopyresampled($temp, $uploadfile, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $width, $height);

					// 5. ตั้งชื่อไฟล์ใหม่ (ใช้เวลา + ชื่อเดิมที่ปลอดภัย)
					$prefix = str_replace('.', '_', microtime(true));
					$original_name = $_FILES['banners']['name'][$k];
					$base_name = pathinfo($original_name, PATHINFO_FILENAME);
					$safe_base_name = preg_replace("/[^a-zA-Z0-9_\-\.]/", "", $base_name);
					if (empty($safe_base_name)) $safe_base_name = 'file';

					$new_name = $prefix . '_' . $safe_base_name . '.webp';
					$spath = base_app . $banner_path . $new_name;

					// 6. บันทึกไฟล์ลงเครื่อง (เป็น WebP)
					if (!imagewebp($temp, $spath, 85)) {
						$err = "ไม่สามารถบันทึกไฟล์ WebP ได้ (ไฟล์: " . $new_name . ")";
					}

					// 7. ล้างเมมโมรี่
					imagedestroy($temp);
					imagedestroy($uploadfile);

					// หน่วงเวลาเล็กน้อยเพื่อให้ชื่อไฟล์ไม่ซ้ำกันแน่นอน
					usleep(1000);
				}
			}

			if (!empty($err)) {
				$resp['status'] = 'failed';
				$resp['msg'] = $err;
			}
		}

		$update = $this->update_system_info();
		$flash = $this->set_flashdata('success', 'อัปเดตระบบเรียบร้อย');
		if ($update && $flash) {
			// var_dump($_SESSION);
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}

	// --- ฟังก์ชันลบรูปปก ---
	function delete_cover()
	{
		$cover = $this->info('cover');
		// ตัด parameter (?v=...) ออกเพื่อให้ได้พาทไฟล์ที่แท้จริง
		$real_path = explode('?', $cover)[0];

		// ลบไฟล์ออกจากเซิร์ฟเวอร์
		if (!empty($real_path) && is_file(base_app . $real_path)) {
			unlink(base_app . $real_path);
		}

		// ลบข้อมูลออกจากฐานข้อมูล
		$qry = $this->conn->query("UPDATE system_info set meta_value = '' where meta_field = 'cover' ");

		if ($qry) {
			// ล้าง session เพื่อให้รูปหายไปทันทีเมื่อรีเฟรชหน้า
			if (isset($_SESSION['system_info']['cover'])) unset($_SESSION['system_info']['cover']);
			return json_encode(['status' => 'success']);
		}
		return json_encode(['status' => 'failed']);
	}

	function set_userdata($field = '', $value = '')
	{
		if (!empty($field) && !empty($value)) {
			$_SESSION['userdata'][$field] = $value;
		}
	}
	function userdata($field = '')
	{
		if (!empty($field)) {
			if (isset($_SESSION['userdata'][$field]))
				return $_SESSION['userdata'][$field];
			else
				return null;
		} else {
			return false;
		}
	}
	function set_flashdata($flash = '', $value = '')
	{
		if (!empty($flash) && !empty($value)) {
			$_SESSION['flashdata'][$flash] = $value;
			return true;
		}
	}
	function chk_flashdata($flash = '')
	{
		if (isset($_SESSION['flashdata'][$flash])) {
			return true;
		} else {
			return false;
		}
	}
	function flashdata($flash = '')
	{
		if (!empty($flash)) {
			$_tmp = $_SESSION['flashdata'][$flash];
			unset($_SESSION['flashdata']);
			return $_tmp;
		} else {
			return false;
		}
	}
	function sess_des()
	{
		if (isset($_SESSION['userdata'])) {
			unset($_SESSION['userdata']);
			return true;
		}
		return true;
	}
	function info($field = '')
	{
		if (!empty($field)) {
			if (isset($_SESSION['system_info'][$field]))
				return $_SESSION['system_info'][$field];
			else
				return false;
		} else {
			return false;
		}
	}
	function set_info($field = '', $value = '')
	{
		if (!empty($field) && !empty($value)) {
			$_SESSION['system_info'][$field] = $value;
		}
	}
}
$_settings = new SystemSettings();
$_settings->load_system_info();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'update_settings_info':
		echo $sysset->update_settings_info();
		break;
	case 'delete_cover':
		echo $sysset->delete_cover();
		break;
	default:
		// echo $sysset->index();
		break;
}
