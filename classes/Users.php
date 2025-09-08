	<?php
	require_once('../config.php');
	class Users extends DBConnection
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

		function save_users()
		{
			// --- ส่วนจัดการรหัสผ่าน (เหมือนเดิม) ---
			if (empty($_POST['password'])) {
				unset($_POST['password']);
			} else {
				$_POST['password'] = md5($_POST['password']);
			}

			// 1. แยกข้อมูลรูปภาพ base64 ออกมาเก็บไว้ก่อน
			$cropped_image_data = isset($_POST['cropped_image']) ? $_POST['cropped_image'] : null;
			unset($_POST['cropped_image']); // เอาออกจาก $_POST หลัก
			unset($_POST['old_avatar']);    // เอาออกจาก $_POST หลัก

			extract($_POST);
			$data = '';

			// 2. เตรียมข้อมูลฟิลด์อื่นๆ ที่จะบันทึกลง DB (ยกเว้น id)
			foreach ($_POST as $k => $v) {
				if (!in_array($k, array('id'))) {
					if (!empty($data)) $data .= " , ";
					$v = $this->conn->real_escape_string($v); // ป้องกัน SQL Injection
					$data .= " `{$k}` = '{$v}' ";
				}
			}

			// 3. บันทึก/อัปเดตข้อมูลหลัก
			if (empty($id)) {
				// กรณีสร้าง User ใหม่ (ถ้ามี)
				$sql = "INSERT INTO `users` set {$data}";
			} else {
				// กรณีอัปเดต User เดิม
				$sql = "UPDATE `users` set {$data} where id = '{$id}'";
			}

			$save = $this->conn->query($sql);

			if ($save) {
				$uid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success', 'แก้ไขข้อมูลส่วนตัวเรียบร้อย');

				// 4. จัดการไฟล์รูปภาพ (ส่วนที่แก้ไขใหม่ทั้งหมด)
				if (!empty($cropped_image_data)) {
					// สร้างโฟลเดอร์ถ้ายังไม่มี
					if (!is_dir(base_app . "uploads/avatars")) {
						mkdir(base_app . "uploads/avatars", 0777, true);
					}

					// ถอดรหัส base64
					$image_parts = explode(";base64,", $cropped_image_data);
					$image_base64 = base64_decode($image_parts[1]);

					// กำหนดชื่อไฟล์และ path (เปลี่ยนเป็นโฟลเดอร์ avatars)
					$fname = "uploads/avatars/{$uid}.png";

					// บันทึกไฟล์
					if (file_put_contents(base_app . $fname, $image_base64)) {
						// อัปเดต path รูปในฐานข้อมูล
						$this->conn->query("UPDATE `users` set `avatar` = CONCAT('{$fname}', '?v=', unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$uid}'");

						// อัปเดต session ของ user ที่ login อยู่
						if ($this->settings->userdata('id') == $uid) {
							$this->settings->set_userdata('avatar', $fname . "?v=" . time());
						}
					}
				}

				// อัปเดตข้อมูลอื่นๆ ใน session
				if ($this->settings->userdata('id') == $uid) {
					foreach ($_POST as $k => $v) {
						if ($k != 'id') {
							$this->settings->set_userdata($k, $v);
						}
					}
				}
				// ไม่ต้อง return 1 หรือ 2 แล้ว ใช้ json response เพื่อความชัดเจน
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = $this->conn->error;
				$resp['sql'] = $sql;
			}

			// ส่งผลลัพธ์กลับเป็น JSON ให้ AJAX
			return json_encode($resp);
		}
		public function delete_users()
		{
			extract($_POST);
			$qry = $this->conn->query("DELETE FROM users where id = $id");
			if ($qry) {
				$this->settings->set_flashdata('success', 'แก้ไขข้อมูลส่วนตัวเรียบร้อย');
				if (is_file(base_app . "uploads/avatars/$id.png"))
					unlink(base_app . "uploads/avatars/$id.png");
				return 1;
			} else {
				return false;
			}
		}
		function registration()
		{
			if (!empty($_POST['password']))
				$_POST['password'] = md5($_POST['password']);
			else
				unset($_POST['password']);

			// ลบ cropped_image ออกจาก $_POST ก่อน เพราะเราจะ xử lý มันแยกต่างหาก
			$cropped_image_data = isset($_POST['cropped_image']) ? $_POST['cropped_image'] : null;
			unset($_POST['cropped_image']);

			extract($_POST);
			$main_field = [
				'firstname',
				'middlename',
				'lastname',
				'gender',
				'contact',
				'email',
				'status',
				'password',
				'address',
				'sub_district',
				'district',
				'province',
				'postal_code'
			];

			$data = "";
			$check = $this->conn->query("SELECT * FROM `customer_list` where email = '{$email}' " . (!empty($id) > 0 ? " and id!='{$id}'" : "") . " ")->num_rows;
			if ($check > 0) {
				$resp['status'] = 'failed';
				$resp['msg'] = 'Email already exists.';
				return json_encode($resp);
			}
			foreach ($_POST as $k => $v) {
				$v = $this->conn->real_escape_string($v);
				if (in_array($k, $main_field)) {
					if (!empty($data)) $data .= ", ";
					$data .= " `{$k}` = '{$v}' ";
				}
			}
			if (empty($id)) {
				$sql = "INSERT INTO `customer_list` set {$data} ";
			} else {
				$sql = "UPDATE `customer_list` set {$data} where id = '{$id}' ";
			}
			$save = $this->conn->query($sql);
			if ($save) {
				$uid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				$resp['uid'] = $uid;
				$resp['msg'] = !empty($id) ? 'แก้ไขข้อมูลส่วนตัวเรียบร้อย' : 'สร้างบัญชีเรียบร้อยแล้ว';

				// === ส่วนจัดการรูปภาพที่แก้ไขใหม่ ===
				if (!empty($cropped_image_data)) {
					if (!is_dir(base_app . "uploads/customers"))
						mkdir(base_app . "uploads/customers", 0777, true);

					// แยกส่วนหัวของ base64 ออกไป (เช่น data:image/png;base64,)
					$image_parts = explode(";base64,", $cropped_image_data);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1]; // png, jpeg, etc.
					$image_base64 = base64_decode($image_parts[1]);

					// กำหนดชื่อไฟล์และ path
					$fname = "uploads/customers/{$uid}.png"; // บันทึกเป็น .png เสมอเพื่อให้สอดคล้องกับ client-side

					// บันทึกไฟล์
					$file_saved = file_put_contents(base_app . $fname, $image_base64);

					if ($file_saved) {
						// อัปเดต path รูปในฐานข้อมูล
						$this->conn->query("UPDATE `customer_list` set `avatar` = CONCAT('{$fname}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$uid}'");
					} else {
						$resp['msg'] .= " (แต่ไม่สามารถบันทึกรูปโปรไฟล์ได้)";
					}
				} else {
					// ใช้รูปเก่าถ้าผู้ใช้ไม่ได้เลือกใหม่
					$avatar = isset($_POST['old_avatar']) ? $_POST['old_avatar'] : "uploads/customers/default_user.png";
					$this->conn->query("UPDATE `customer_list` SET `avatar` = '{$avatar}' WHERE id = '{$uid}'");
				}
				if (!empty($uid) && $this->settings->userdata('login_type') != 1) {
					$user = $this->conn->query("SELECT * FROM `customer_list` where id = '{$uid}' ");
					if ($user->num_rows > 0) {
						$res = $user->fetch_array();
						foreach ($res as $k => $v) {
							if (!is_numeric($k) && $k != 'password') {
								$this->settings->set_userdata($k, $v);
							}
						}
						$this->settings->set_userdata('login_type', '2');
					}
				}
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = $this->conn->error;
				$resp['sql'] = $sql;
			}

			if ($resp['status'] == 'success' && isset($resp['msg']))
				$this->settings->set_flashdata('success', $resp['msg']);
			return json_encode($resp);
		}

		public function delete_customer()
		{
			extract($_POST);
			$avatar = $this->conn->query("SELECT avatar FROM customer_list where id = $id");
			$qry = $this->conn->query("DELETE FROM customer_list where id = $id");
			if ($qry) {
				$this->settings->set_flashdata('success', 'Customer Details has been deleted successfully.');
				$resp['status'] = 'success';
				if ($avatar->num_rows > 0) {
					$avatar = explode("?", $avatar->fetch_array()[0])[0];
					if (is_file(base_app . $avatar)) {
						unlink(base_app . $avatar);
					}
				}
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = $this->conn->error;
			}

			return json_encode($resp);
		}
		public function update_profile()
		{
			extract($_POST);
			$data = "";
			$fields = ['firstname', 'lastname', 'contact', 'address',  'sub_district', 'district', 'province', 'postal_code',];
			foreach ($fields as $k) {
				if (isset($_POST[$k])) {
					$v = $this->conn->real_escape_string($_POST[$k]);
					if (!empty($data)) $data .= ", ";
					$data .= " `{$k}` = '{$v}' ";
				}
			}
			$update = $this->conn->query("UPDATE customer_list SET $data WHERE id = '{$this->settings->userdata('id')}'");
			if ($update) {
				$resp['status'] = 'success';
				foreach ($fields as $k) {
					if (isset($_POST[$k])) {
						$this->settings->set_userdata($k, $_POST[$k]);
					}
				}
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
			return json_encode($resp);
		}
	}

	$users = new users();
	$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
	switch ($action) {
		case 'save':
			echo $users->save_users();
			break;
		case 'delete':
			echo $users->delete_users();
			break;
		case 'registration':
			echo $users->registration();
			break;
		case 'delete_customer':
			echo $users->delete_customer();
			break;
		default:
			// echo $sysset->index();
			break;

		// Users.php
		case 'update_profile':
			echo $user->update_profile();
			break;
	}
