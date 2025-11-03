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
			foreach ($_FILES['banners']['tmp_name'] as $k => $v) {
				if (!empty($_FILES['banners']['tmp_name'][$k])) {
					$accept = ['image/jpeg', 'image/png'];
					$type = $_FILES['banners']['type'][$k];

					if (!in_array($type, $accept)) {
						$err = "Image file type is invalid";
						break;
					}

					if ($type == 'image/jpeg')
						$uploadfile = imagecreatefromjpeg($_FILES['banners']['tmp_name'][$k]);
					elseif ($type == 'image/png')
						$uploadfile = imagecreatefrompng($_FILES['banners']['tmp_name'][$k]);

					if (!$uploadfile) {
						$err = "Image is invalid";
						break;
					}

					list($width, $height) = getimagesize($_FILES['banners']['tmp_name'][$k]);

					$temp = imagecreatetruecolor(1920, 600);

					// สำหรับ PNG: เปิด transparency
					if ($type == 'image/png') {
						imagealphablending($temp, false);
						imagesavealpha($temp, true);
						$transparent = imagecolorallocatealpha($temp, 0, 0, 0, 127);
						imagefilledrectangle($temp, 0, 0, 1920, 600, $transparent);
					}

					imagecopyresampled($temp, $uploadfile, 0, 0, 0, 0, 1920, 600, $width, $height);

					// 🔥 สำคัญมาก: ตั้งชื่อ path ปลายทาง
					$spath = base_app . $banner_path . '/' . $_FILES['banners']['name'][$k];
					$i = 1;
					while (is_file($spath)) {
						$spath = base_app . $banner_path . '/' . ($i++) . '_' . $_FILES['banners']['name'][$k];
					}

					// Save ไฟล์
					if ($type == 'image/jpeg')
						imagejpeg($temp, $spath, 90);
					elseif ($type == 'image/png')
						imagepng($temp, $spath, 6);

					imagedestroy($temp);
					imagedestroy($uploadfile);
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
	default:
		// echo $sysset->index();
		break;
}
