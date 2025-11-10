	<?php
	require_once('../config.php');
	require_once(__DIR__ . '/../vendor/autoload.php');

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

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
			// --- Start: Logic for handling cropped images ---
			$cropped_image_data = isset($_POST['cropped_image']) ? $_POST['cropped_image'] : null;
			unset($_POST['cropped_image']); // Remove from POST to avoid database error
			// --- End: Logic ---

			// Sanitize all POST data
			foreach ($_POST as $k => $v) {
				if (!is_array($v)) {
					$_POST[$k] = trim($v);
				}
			}

			extract($_POST);
			$resp = array(); // Initialize response array

			// Server-side validation for password if it is being set/changed
			if (!empty($password)) {
				$errors = [];
				if (strlen($password) < 8) {
					$errors[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร";
				}

				// ตรวจสอบตัวอักษรพิมพ์เล็ก
				if (!preg_match('/[a-z]/', $password)) {
					$errors[] = "รหัสผ่านต้องมีตัวอักษรพิมพ์เล็กอย่างน้อย 1 ตัว";
				}

				// ตรวจสอบตัวเลข
				if (!preg_match('/[0-9]/', $password)) {
					$errors[] = "รหัสผ่านต้องมีตัวเลขอย่างน้อย 1 ตัว";
				}

				// ตรวจสอบตัวอักษรพิมพ์ใหญ่
				if (!preg_match('/[A-Z]/', $password)) {
					$errors[] = "รหัสผ่านต้องมีตัวอักษรพิมพ์ใหญ่อย่างน้อย 1 ตัว";
				}

				// ตรวจสอบสัญลักษณ์พิเศษ
				if (!preg_match('/[\W_]/', $password)) {
					$errors[] = "รหัสผ่านต้องมีสัญลักษณ์พิเศษอย่างน้อย 1 ตัว (เช่น @, #, $, %)";
				}

				// หากมีข้อผิดพลาดในการตรวจสอบรหัสผ่าน
				if (!empty($errors)) {
					echo json_encode(['status' => 'failed', 'msg' => implode('<br>', $errors)]);
					return;
				}

				// Hash the password only after validation passes
				$_POST['password'] = password_hash($password, PASSWORD_DEFAULT);
			} else {
				// If password is not being set, remove it from the data to be saved
				unset($_POST['password']);
			}

			$data = '';

			// Whitelist of allowed fields from the form (username removed)
			$allowed_fields = ['firstname', 'middlename', 'lastname', 'username', 'password', 'type'];

			foreach ($_POST as $k => $v) {
				if (in_array($k, $allowed_fields) && !is_array($v)) {
					$v_escaped = $this->conn->real_escape_string($v);
					if (!empty($data)) $data .= ", ";
					$data .= " `{$k}` = '{$v_escaped}' ";
				}
			}

			// --- Check if it's a new user (INSERT) ---
			if (empty($id)) {
				// --- Insert new user ---
				$sql = "INSERT INTO `users` SET $data";
				$save = $this->conn->query($sql);

				if ($save) {
					$new_user_id = $this->conn->insert_id;
					$resp['status'] = 'success';
					$resp['msg'] = 'สร้างบัญชีสมาชิกเรียบร้อย';
					$this->settings->set_flashdata('success', 'สร้างบัญชีสมาชิกเรียบร้อย');

					// --- Start: Save cropped image logic (UNCHANGED) ---
					if (!empty($cropped_image_data)) {
						$upload_path = base_app . "uploads/avatars/";
						if (!is_dir($upload_path))
							mkdir($upload_path, 0755, true);

						$image_parts = explode(";base64,", $cropped_image_data);
						$image_base64 = base64_decode($image_parts[1]);
						$fname = "{$upload_path}{$new_user_id}.png";

						if (file_put_contents($fname, $image_base64)) {
							$avatar_path = "uploads/avatars/{$new_user_id}.png";
							$this->conn->query("UPDATE `users` SET `avatar` = '{$this->conn->real_escape_string($avatar_path)}' WHERE id = '{$new_user_id}'");

							if ($this->settings->userdata('id') == $new_user_id)
								$this->settings->set_userdata('avatar', $avatar_path . "?v=" . time());
						} else {
							$resp['msg'] .= " (แต่บันทึกรูปโปรไฟล์ไม่สำเร็จ)";
						}
					}
					// --- End: Save cropped image logic ---
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = $this->conn->error;
				}
			} else {
				// --- Update existing user (UPDATE) ---
				$sql = "UPDATE users SET $data WHERE id = '{$id}'";
				$save = $this->conn->query($sql);

				if ($save) {
					$resp['status'] = 'success';
					$resp['msg'] = 'บัญชีสมาชิกอัปเดตเรียบร้อย';
					$this->settings->set_flashdata('success', 'บัญชีสมาชิกอัปเดตเรียบร้อย');

					foreach ($_POST as $k => $v) {
						if ($this->settings->userdata('id') == $id && in_array($k, ['firstname', 'middlename', 'lastname', 'type']))
							$this->settings->set_userdata($k, $v);
					}

					// --- Start: Save cropped image logic (UNCHANGED) ---
					if (!empty($cropped_image_data)) {
						$upload_path = base_app . "uploads/avatars/";
						if (!is_dir($upload_path))
							mkdir($upload_path, 0755, true);

						$image_parts = explode(";base64,", $cropped_image_data);
						$image_base64 = base64_decode($image_parts[1]);
						$fname = "{$upload_path}{$id}.png";

						if (file_put_contents($fname, $image_base64)) {
							$avatar_path = "uploads/avatars/{$id}.png";
							$this->conn->query("UPDATE `users` SET `avatar` = '{$this->conn->real_escape_string($avatar_path)}' WHERE id = '{$id}'");

							if ($this->settings->userdata('id') == $id)
								$this->settings->set_userdata('avatar', $avatar_path . "?v=" . time());
						} else {
							$resp['msg'] .= " (แต่บันทึกรูปโปรไฟล์ไม่สำเร็จ)";
						}
					}
					// --- End: Save cropped image logic ---
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = $this->conn->error;
				}
			}

			// Return JSON response
			echo json_encode($resp);
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

			extract($_POST);

			// === 1. เพิ่มการตรวจสอบรหัสผ่านฝั่ง Server ===
			if (isset($password)) {
				$errors = [];
				if (strlen($password) < 8) {
					$errors[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร";
				}
				if (!preg_match('/[a-z]/', $password)) {
					$errors[] = "รหัสผ่านต้องมีตัวอักษรพิมพ์เล็กอย่างน้อย 1 ตัว";
				}
				if (!preg_match('/[0-9]/', $password)) {
					$errors[] = "รหัสผ่านต้องมีตัวเลขอย่างน้อย 1 ตัว";
				}

				if (!empty($errors)) {
					$resp['status'] = 'failed';
					// รวม error ทั้งหมดมาแสดง
					$resp['msg'] = '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
					return json_encode($resp);
				}
			}

			// === 2. เปลี่ยนจาก md5() เป็น password_hash() ===
			if (!empty($_POST['password'])) {
				// ใช้ BCRYPT ซึ่งเป็น default และปลอดภัย
				$_POST['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
			} else {
				unset($_POST['password']);
			}

			$cropped_image_data = isset($_POST['cropped_image']) ? $_POST['cropped_image'] : null;
			unset($_POST['cropped_image']);

			$main_field = [
				'firstname',
				'middlename',
				'lastname',
				'gender',
				'contact',
				'email',
				'status',
				'password'
			];

			$data = "";
			$check = $this->conn->query("SELECT * FROM `customer_list` where email = '{$email}' " . (!empty($id) > 0 ? " and id!='{$id}'" : "") . " ")->num_rows;
			if ($check > 0) {
				$resp['status'] = 'failed';
				$resp['msg'] = 'อีเมลนี้ถูกใช้แล้ว';
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
						mkdir(base_app . "uploads/customers", 0755, true);

					// แยกส่วนหัวของ base64 ออกไป (เช่น data:image/webp;base64,)
					$image_parts = explode(";base64,", $cropped_image_data);

					// [แก้ไข] ตรวจสอบว่า base64 มาถูกต้องหรือไม่
					if (count($image_parts) < 2) {
						$resp['msg'] .= " (แต่ข้อมูลรูปภาพที่ส่งมาไม่ถูกต้อง)";
					} else {
						$image_base64 = base64_decode($image_parts[1]);

						// [แก้ไข] กำหนดชื่อไฟล์เป็น .webp
						$fname = "uploads/customers/{$uid}.webp";

						// บันทึกไฟล์
						$file_saved = file_put_contents(base_app . $fname, $image_base64);

						if ($file_saved) {
							// อัปเดต path รูปในฐานข้อมูล
							$this->conn->query("UPDATE `customer_list` set `avatar` = CONCAT('{$fname}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$uid}'");
						} else {
							$resp['msg'] .= " (แต่ไม่สามารถบันทึกรูปโปรไฟล์ได้)";
						}
					}
				}
				// [แก้ไข] ลบ else block ที่ไม่จำเป็นออก
				// (ถ้าไม่มีการอัปโหลดรูป ก็ไม่จำเป็นต้อง query หรือ update avatar)

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

		function save_address()
		{
			extract($_POST);
			$customer_id = $this->settings->userdata('id');

			if (empty($customer_id)) {
				$resp['status'] = 'failed';
				$resp['msg'] = 'กรุณาเข้าสู่ระบบก่อนเพิ่มที่อยู่';
				return json_encode($resp);
			}

			// Fields allowed to be saved
			$fields = [
				'name',
				'contact',
				'address',
				'sub_district',
				'district',
				'province',
				'postal_code',
				'is_primary'
			];

			$data = "";
			foreach ($_POST as $k => $v) {
				$v = $this->conn->real_escape_string($v);
				if (in_array($k, $fields)) {
					if (!empty($data)) $data .= ", ";
					$data .= " `{$k}` = '{$v}' ";
				}
			}

			// ถ้าตั้งเป็นที่อยู่หลัก (is_primary == 1) ให้รีเซ็ตที่อยู่หลักเดิมเป็น 0
			if (!empty($is_primary) && $is_primary == 1) {
				$this->conn->query("UPDATE `customer_addresses` SET is_primary = 0 WHERE customer_id = '{$customer_id}'");
			}

			// ตรวจสอบว่าเป็นการเพิ่มที่อยู่ใหม่หรือแก้ไขที่อยู่เดิม
			if (!empty($address_id)) {
				// อัปเดตที่อยู่เดิม
				$sql = "UPDATE `customer_addresses` SET {$data} WHERE id = '{$address_id}' AND customer_id = '{$customer_id}'";
			} else {
				// เพิ่มที่อยู่ใหม่
				$sql = "INSERT INTO `customer_addresses` SET {$data}, `customer_id` = '{$customer_id}'";
			}

			// Execute query
			$save = $this->conn->query($sql);

			if ($save) {
				// Get address ID (if inserting)
				if (empty($address_id)) {
					$addr_id = $this->conn->insert_id;
				} else {
					$addr_id = $address_id;
				}

				// Prepare response
				$resp['status'] = 'success';
				$resp['addr_id'] = $addr_id;
				$resp['msg'] = empty($address_id) ? 'เพิ่มที่อยู่ใหม่เรียบร้อยแล้ว' : 'อัปเดตที่อยู่เรียบร้อยแล้ว';
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = $this->conn->error;
				$resp['sql'] = $sql;
			}

			// Set flash message if successful
			if ($resp['status'] == 'success' && isset($resp['msg'])) {
				$this->settings->set_flashdata('success', $resp['msg']);
			}

			return json_encode($resp);
		}

		function delete_address()
		{
			extract($_POST);
			$customer_id = $this->settings->userdata('id');

			if (empty($customer_id)) {
				$resp['status'] = 'failed';
				$resp['msg'] = 'กรุณาเข้าสู่ระบบก่อนลบที่อยู่';
				return json_encode($resp);
			}

			if (empty($address_id)) {
				$resp['status'] = 'failed';
				$resp['msg'] = 'กรุณาระบุที่อยู่ที่จะลบ';
				return json_encode($resp);
			}

			// ลบที่อยู่จากฐานข้อมูล
			$sql = "DELETE FROM `customer_addresses` WHERE id = '{$address_id}' AND customer_id = '{$customer_id}'";
			$delete = $this->conn->query($sql);

			if ($delete) {
				// กรณีลบที่อยู่สำเร็จ
				$resp['status'] = 'success';
				$resp['msg'] = 'ลบที่อยู่เรียบร้อย';
			} else {
				// กรณีลบที่อยู่ไม่สำเร็จ
				$resp['status'] = 'failed';
				$resp['msg'] = 'ไม่สามารถลบที่อยู่ได้ โปรดลองอีกครั้ง';
			}

			// ตั้งค่าข้อความแจ้งเตือนหากลบสำเร็จ
			if ($resp['status'] == 'success' && isset($resp['msg'])) {
				$this->settings->set_flashdata('success', $resp['msg']);
			}

			return json_encode($resp);
		}

		public function delete_customer()
		{
			extract($_POST);
			$avatar = $this->conn->query("SELECT avatar FROM customer_list where id = $id");
			$qry = $this->conn->query("DELETE FROM customer_list where id = $id");
			if ($qry) {
				$this->settings->set_flashdata('success', 'ลบบัญชีผู้ใช้เรียบร้อย');
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

		// 1. ฟังก์ชันสำหรับส่งอีเมล (แยกออกมาใหม่)
		// ====================================================================
		/**
		 * Sends an email using PHPMailer.
		 *
		 * @param array $recipients An associative array of recipients [email => name].
		 * @param string $subject The subject of the email.
		 * @param string $body The HTML body of the email.
		 * @return bool True on success, false on failure.
		 */
		public function send_email(array $recipients, string $subject, string $body): bool
		{
			$mail = new PHPMailer(true);
			try {
				// *** Best Practice: ควรย้ายข้อมูลเหล่านี้ไปไว้ในไฟล์ config.php ***
				$mail->isSMTP();
				$mail->Host       = 'smtp.gmail.com';
				$mail->Port       = 465;
				$mail->SMTPAuth   = true;
				$mail->Username   = "faritre5566@gmail.com"; // ใช้อีเมลของคุณ
				$mail->Password   = "bchljhaxoqflmbys";      // ใช้รหัสผ่านสำหรับแอป (App Password)
				$mail->SMTPSecure = "ssl";
				$mail->CharSet    = 'UTF-8';

				// Sender
				$mail->setFrom('faritre5566@gmail.com', 'MSG.com');

				// Recipients
				foreach ($recipients as $email => $name) {
					$mail->addAddress($email, $name);
				}

				// Content
				$mail->isHTML(true);
				$mail->Subject = $subject;
				$mail->Body    = $body;

				$mail->send();
				return true; // ส่งสำเร็จ
			} catch (Exception $e) {
				// บันทึก error ไว้ดูภายหลัง แทนที่จะแสดงผลออกมา
				error_log("❌ ส่งอีเมลไม่สำเร็จ: " . $mail->ErrorInfo);
				return false; // ส่งไม่สำเร็จ
			}
		}

		// ====================================================================
		// 2. ฟังก์ชันสำหรับส่งข้อความ Telegram (แยกออกมาใหม่)
		// ====================================================================
		/**
		 * Sends a message to a Telegram chat.
		 *
		 * @param string $message The message text to send.
		 * @return bool True on success, false on failure.
		 */
		public function send_telegram_message(string $message): bool
		{
			// *** Best Practice: ควรย้ายข้อมูลเหล่านี้ไปไว้ในไฟล์ config.php ***
			$bot_token = "8060343667:AAEK7rfDeBszjWOFkITO-wC7_YhMmQuILDk"; // ใช้ Bot Token ของคุณ
			$chat_id   = "-4869854888";                                   // ใช้ Chat ID ของแอดมินหรือกลุ่ม

			$url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
			$data = [
				'chat_id'    => $chat_id,
				'text'       => $message,
				'parse_mode' => 'HTML',
			];

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For development only

			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($http_code == 200 && $response) {
				return true; // ส่งสำเร็จ
			} else {
				error_log("❌ ส่ง Telegram ไม่สำเร็จ: " . $response);
				return false; // ส่งไม่สำเร็จ
			}
		}

		function password()
		{
			global $conn; // เชื่อมต่อกับฐานข้อมูล

			// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
			if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password']) && isset($_GET['id']) && $_GET['id'] > 0) {
				// รับค่า id ของผู้ใช้ที่กำลังจะเปลี่ยนรหัสผ่าน
				$user_id = $_GET['id'];

				// รับค่า รหัสผ่านเดิม, รหัสผ่านใหม่ และยืนยันรหัสผ่านใหม่
				$old_password = $_POST['current_password']; // รหัสผ่านเดิม
				$new_password = $_POST['new_password']; // รหัสผ่านใหม่
				$confirm_password = $_POST['confirm_password']; // ยืนยันรหัสผ่านใหม่

				// ตรวจสอบว่ารหัสผ่านเดิมถูกต้องหรือไม่
				$qry = $conn->query("SELECT password FROM customer_list WHERE id = '$user_id'");

				if ($qry->num_rows > 0) {
					$row = $qry->fetch_assoc();
					// ใช้ password_verify() เช็คว่ารหัสผ่านเดิมตรงกับที่เก็บในฐานข้อมูลหรือไม่
					if (password_verify($old_password, $row['password'])) {
						// ตรวจสอบว่ารหัสใหม่และยืนยันรหัสตรงกันหรือไม่
						if ($new_password === $confirm_password) {
							// แฮชรหัสผ่านใหม่ก่อนบันทึกลงฐานข้อมูล
							$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

							// อัปเดตรหัสผ่านใหม่ในฐานข้อมูล
							$update = $conn->query("UPDATE customer_list SET password = '$hashed_new_password' WHERE id = '$user_id'");

							if ($update) {
								// หากอัปเดตสำเร็จ, ส่งข้อความสำเร็จ
								echo json_encode(['status' => 'success', 'msg' => 'รหัสผ่านถูกอัปเดตเรียบร้อยแล้ว']);
							} else {
								// หากไม่สามารถอัปเดตได้, ส่งข้อความผิดพลาด
								echo json_encode(['status' => 'failed', 'msg' => 'ไม่สามารถอัปเดตรหัสผ่านได้']);
							}
						} else {
							// หากรหัสใหม่และยืนยันรหัสไม่ตรงกัน
							echo json_encode(['status' => 'failed', 'msg' => 'รหัสใหม่และยืนยันรหัสไม่ตรงกัน']);
						}
					} else {
						// หากรหัสเดิมไม่ถูกต้อง
						echo json_encode(['status' => 'failed', 'msg' => 'รหัสผ่านเดิมไม่ถูกต้อง']);
					}
				} else {
					// หากไม่พบผู้ใช้
					echo json_encode(['status' => 'failed', 'msg' => 'ไม่พบผู้ใช้']);
				}
			} else {
				// หากไม่ได้ส่งข้อมูลมาครบ
				echo json_encode(['status' => 'failed', 'msg' => 'ข้อมูลไม่ครบ']);
			}
		}

		function forgot_password()
		{
			if (isset($_POST['email']) && !empty($_POST['email'])) {
				$email = $_POST['email'];

				// 1. ตรวจสอบว่าอีเมลมีอยู่ในฐานข้อมูล customer_list หรือไม่
				$query = "SELECT id, firstname, lastname FROM customer_list WHERE email = ? LIMIT 1";
				$stmt = $this->conn->prepare($query);
				$stmt->bind_param('s', $email);
				$stmt->execute();
				$result = $stmt->get_result();

				if ($result->num_rows > 0) {
					$user = $result->fetch_assoc();
					$user_id = $user['id'];
					$user_name = $user['firstname'] . ' ' . $user['lastname'];

					// 2. สร้าง Token และกำหนดวันหมดอายุ
					$token = bin2hex(random_bytes(32));
					$expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

					// 3. **(เปลี่ยนแปลง)** บันทึก Token ลงในตารางใหม่ `password_resets`
					$insert_query = "INSERT INTO password_resets (customer_id, token, expires_at) VALUES (?, ?, ?)";
					$insert_stmt = $this->conn->prepare($insert_query);
					$insert_stmt->bind_param('iss', $user_id, $token, $expires_at);

					if ($insert_stmt->execute()) {
						// 4. เตรียมและส่งอีเมลไปยังผู้ใช้ (ส่วนนี้เหมือนเดิม)
						$reset_link = base_url .  "reset_password.php?token=" . $token; // **สำคัญ:** เปลี่ยน yourwebsite.com เป็นโดเมนของคุณ

						$email_subject = "คำขอรีเซ็ตรหัสผ่านของคุณ";
						$email_body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin:auto; border: 1px solid #ddd; padding: 20px;'>
                        <h2 style='text-align:center;'>รีเซ็ตรหัสผ่านของคุณ</h2>
                        <p>สวัสดีคุณ {$user_name},</p>
                        <p>เราได้รับคำขอรีเซ็ตรหัสผ่านสำหรับบัญชีของคุณ กรุณาคลิกลิงก์ด้านล่างเพื่อตั้งรหัสผ่านใหม่:</p>
                        <p style='text-align:center;'>
                            <a href='{$reset_link}' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>รีเซ็ตรหัสผ่าน</a>
                        </p>
                        <p>ลิงก์นี้จะหมดอายุใน 1 ชั่วโมง</p>
                        <p>หากคุณไม่ได้เป็นผู้ส่งคำขอนี้ กรุณาเพิกเฉยอีเมลฉบับนี้</p>
                    </div>
                ";

						$this->send_email([$email => $user_name], $email_subject, $email_body);

						echo json_encode(['status' => 'success', 'msg' => 'หากอีเมลของคุณมีอยู่ในระบบ เราได้ส่งลิงก์สำหรับรีเซ็ตรหัสผ่านไปให้แล้ว']);
					} else {
						echo json_encode(['status' => 'error', 'msg' => 'เกิดข้อผิดพลาดในการสร้าง Token']);
					}
				} else {
					// เพื่อความปลอดภัย: ส่งข้อความเดียวกันเสมอ ไม่ว่าจะเจออีเมลหรือไม่
					echo json_encode(['status' => 'success', 'msg' => 'หากอีเมลของคุณมีอยู่ในระบบ เราได้ส่งลิงก์สำหรับรีเซ็ตรหัสผ่านไปให้แล้ว']);
				}
			} else {
				echo json_encode(['status' => 'error', 'msg' => 'กรุณากรอกอีเมล']);
			}
		}

		function manage_password()
		{
			global $conn; // เชื่อมต่อกับฐานข้อมูล

			// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
			if (isset($_POST['new_password']) && isset($_POST['confirm_password']) && isset($_POST['id']) && $_POST['id'] > 0) {
				// รับค่า id ของผู้ใช้ที่กำลังจะเปลี่ยนรหัสผ่าน
				$user_id = $_POST['id'];

				// รับค่า รหัสผ่านใหม่ และยืนยันรหัสผ่านใหม่
				$new_password = $_POST['new_password']; // รหัสผ่านใหม่
				$confirm_password = $_POST['confirm_password']; // ยืนยันรหัสผ่านใหม่

				// === ตรวจสอบความถูกต้องของรหัสผ่าน ===
				$errors = [];
				if (strlen($new_password) < 8) {
					$errors[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร";
				}
				if (!preg_match('/[a-z]/', $new_password)) {
					$errors[] = "รหัสผ่านต้องมีตัวอักษรพิมพ์เล็กอย่างน้อย 1 ตัว";
				}
				if (!preg_match('/[0-9]/', $new_password)) {
					$errors[] = "รหัสผ่านต้องมีตัวเลขอย่างน้อย 1 ตัว";
				}

				// หากมีข้อผิดพลาดในการตรวจสอบรหัสผ่าน
				if (!empty($errors)) {
					echo json_encode(['status' => 'failed', 'msg' => implode('<br>', $errors)]);
					return;
				}

				// ตรวจสอบว่ารหัสใหม่และยืนยันรหัสตรงกันหรือไม่
				if ($new_password === $confirm_password) {
					// แฮชรหัสผ่านใหม่ก่อนบันทึกลงฐานข้อมูล
					$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

					// อัปเดตรหัสผ่านใหม่ในฐานข้อมูล
					$stmt = $conn->prepare("UPDATE customer_list SET password = ? WHERE id = ?");

					// ผูกตัวแปรเข้ากับ placeholder
					$stmt->bind_param("si", $hashed_new_password, $user_id);

					// สั่งให้คำสั่งทำงาน
					$update = $stmt->execute();

					// ปิด statement
					$stmt->close();

					if ($update) {
						// หากอัปเดตสำเร็จ, ส่งข้อความสำเร็จ
						echo json_encode(['status' => 'success', 'msg' => 'รหัสผ่านถูกอัปเดตเรียบร้อยแล้ว']);
					} else {
						// หากไม่สามารถอัปเดตได้, ส่งข้อความผิดพลาด
						echo json_encode(['status' => 'failed', 'msg' => 'ไม่สามารถอัปเดตรหัสผ่านได้']);
					}
				} else {
					// หากรหัสใหม่และยืนยันรหัสไม่ตรงกัน
					echo json_encode(['status' => 'failed', 'msg' => 'รหัสใหม่และยืนยันรหัสไม่ตรงกัน']);
				}
			} else {
				// หากไม่ได้ส่งข้อมูลมาครบ
				echo json_encode(['status' => 'failed', 'msg' => 'ข้อมูลไม่ครบ']);
			}
		}
		function user_manage_password()
		{
			global $conn; // เชื่อมต่อกับฐานข้อมูล

			// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
			if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password']) && isset($_POST['id']) && $_POST['id'] > 0) {
				// รับค่า id ของผู้ใช้ที่กำลังจะเปลี่ยนรหัสผ่าน
				$user_id = $_POST['id'];

				// รับค่า รหัสผ่านเดิม, รหัสผ่านใหม่ และยืนยันรหัสผ่านใหม่
				$current_password = $_POST['current_password']; // รหัสผ่านเดิม
				$new_password = $_POST['new_password']; // รหัสผ่านใหม่
				$confirm_password = $_POST['confirm_password']; // ยืนยันรหัสผ่านใหม่

				// === ตรวจสอบความถูกต้องของรหัสผ่าน ===
				$errors = [];

				// ตรวจสอบว่ารหัสผ่านเดิมตรงกับรหัสผ่านในฐานข้อมูลหรือไม่
				$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
				$stmt->bind_param("i", $user_id);
				$stmt->execute();
				$result = $stmt->get_result();
				$user = $result->fetch_assoc();

				// ถ้ารหัสผ่านเดิมไม่ตรง
				if (!password_verify($current_password, $user['password'])) {
					$errors[] = "รหัสผ่านเดิมไม่ถูกต้อง";
				}

				// ตรวจสอบความยาวของรหัสผ่าน
				if (strlen($new_password) < 8) {
					$errors[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร";
				}

				// ตรวจสอบตัวอักษรพิมพ์เล็ก
				if (!preg_match('/[a-z]/', $new_password)) {
					$errors[] = "รหัสผ่านต้องมีตัวอักษรพิมพ์เล็กอย่างน้อย 1 ตัว";
				}

				// ตรวจสอบตัวเลข
				if (!preg_match('/[0-9]/', $new_password)) {
					$errors[] = "รหัสผ่านต้องมีตัวเลขอย่างน้อย 1 ตัว";
				}

				// ตรวจสอบตัวอักษรพิมพ์ใหญ่
				if (!preg_match('/[A-Z]/', $new_password)) {
					$errors[] = "รหัสผ่านต้องมีตัวอักษรพิมพ์ใหญ่อย่างน้อย 1 ตัว";
				}

				// ตรวจสอบสัญลักษณ์พิเศษ
				if (!preg_match('/[\W_]/', $new_password)) {
					$errors[] = "รหัสผ่านต้องมีสัญลักษณ์พิเศษอย่างน้อย 1 ตัว (เช่น @, #, $, %)";
				}

				// หากมีข้อผิดพลาดในการตรวจสอบรหัสผ่าน
				if (!empty($errors)) {
					echo json_encode(['status' => 'failed', 'msg' => implode('<br>', $errors)]);
					return;
				}

				// ตรวจสอบว่ารหัสใหม่และยืนยันรหัสตรงกันหรือไม่
				if ($new_password === $confirm_password) {
					// แฮชรหัสผ่านใหม่ก่อนบันทึกลงฐานข้อมูล
					$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

					// อัปเดตรหัสผ่านใหม่ในฐานข้อมูล
					$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
					$stmt->bind_param("si", $hashed_new_password, $user_id);
					$update = $stmt->execute();

					// ปิด statement
					$stmt->close();

					if ($update) {
						// หากอัปเดตสำเร็จ, ส่งข้อความสำเร็จ
						echo json_encode(['status' => 'success', 'msg' => 'รหัสผ่านถูกอัปเดตเรียบร้อยแล้ว']);
					} else {
						// หากไม่สามารถอัปเดตได้, ส่งข้อความผิดพลาด
						echo json_encode(['status' => 'failed', 'msg' => 'ไม่สามารถอัปเดตรหัสผ่านได้']);
					}
				} else {
					// หากรหัสใหม่และยืนยันรหัสไม่ตรงกัน
					echo json_encode(['status' => 'failed', 'msg' => 'รหัสใหม่และยืนยันรหัสไม่ตรงกัน']);
				}
			} else {
				// หากไม่ได้ส่งข้อมูลมาครบ
				echo json_encode(['status' => 'failed', 'msg' => 'ข้อมูลไม่ครบ']);
			}
		}

		// เพิ่มฟังก์ชันนี้ในไฟล์ classes/Users.php ภายใน class Users
		function reset_password_with_token()
		{
			extract($_POST);

			// ตรวจสอบข้อมูลเบื้องต้น
			if (!isset($token) || !isset($new_password) || !isset($confirm_password)) {
				return json_encode(['status' => 'error', 'msg' => 'ข้อมูลไม่ครบถ้วน']);
			}

			if ($new_password !== $confirm_password) {
				return json_encode(['status' => 'error', 'msg' => 'รหัสผ่านใหม่และการยืนยันไม่ตรงกัน']);
			}

			// ตรวจสอบความปลอดภัยของรหัสผ่าน
			if (strlen($new_password) < 8 || !preg_match('/[a-z]/', $new_password) || !preg_match('/\d/', $new_password)) {
				return json_encode(['status' => 'error', 'msg' => 'รหัสผ่านไม่ตรงตามเงื่อนไขความปลอดภัย']);
			}

			// 1. ค้นหา Token ในฐานข้อมูล
			$token_qry = $this->conn->prepare("SELECT * FROM `password_resets` WHERE token = ?");
			$token_qry->bind_param("s", $token);
			$token_qry->execute();
			$result = $token_qry->get_result();

			if ($result->num_rows == 0) {
				return json_encode(['status' => 'error', 'msg' => 'Token ไม่ถูกต้องหรือไม่พบในระบบ']);
			}

			$row = $result->fetch_assoc();
			$expires_at = strtotime($row['expires_at']);

			// 2. ตรวจสอบว่า Token หมดอายุหรือไม่
			if ($expires_at <= time()) {
				return json_encode(['status' => 'error', 'msg' => 'Token หมดอายุแล้ว กรุณาดำเนินการขอรีเซ็ตรหัสผ่านใหม่อีกครั้ง']);
			}

			// 3. ถ้า Token ถูกต้อง: ทำการ Hash รหัสผ่านใหม่และอัปเดต
			$user_id = $row['customer_id'];
			$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

			$update_stmt = $this->conn->prepare("UPDATE `customer_list` SET `password` = ? WHERE `id` = ?");
			$update_stmt->bind_param("si", $hashed_password, $user_id);

			if ($update_stmt->execute()) {
				// 4. ลบ Token ที่ใช้งานแล้วออกจากฐานข้อมูล
				$delete_stmt = $this->conn->prepare("DELETE FROM `password_resets` WHERE `token` = ?");
				$delete_stmt->bind_param("s", $token);
				$delete_stmt->execute();

				return json_encode(['status' => 'success', 'msg' => 'รหัสผ่านถูกอัปเดตเรียบร้อยแล้ว']);
			} else {
				return json_encode(['status' => 'error', 'msg' => 'เกิดข้อผิดพลาดในการอัปเดตรหัสผ่าน']);
			}
		}
	}

	$users = new users();
	$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
	switch ($action) {
		case 'save_users':
			echo $users->save_users();
			break;
		case 'delete':
			echo $users->delete_users();
			break;
		case 'registration':
			echo $users->registration();
			break;
		case 'save_address':
			echo $users->save_address();
			break;
		case 'delete_address':
			echo $users->delete_address();
			break;
		case 'delete_customer':
			echo $users->delete_customer();
			break;
		// Users.php
		case 'update_profile':
			echo $users->update_profile();
			break;
		case 'password':
			echo $users->password();
			break;
		case 'forgot_password':
			echo $users->forgot_password();
			break;
		case 'manage_password':
			echo $users->manage_password();
			break;
		case 'user_manage_password':
			echo $users->user_manage_password();
			break;
		case 'reset_password_with_token':
			echo $users->reset_password_with_token();
			break;
		default:
			// echo $sysset->index();
			break;
	}
