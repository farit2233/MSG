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

		public function save_users()
		{
			// --- Start: New logic for handling cropped images ---
			$cropped_image_data = isset($_POST['cropped_image']) ? $_POST['cropped_image'] : null;
			unset($_POST['cropped_image']); // Remove from POST to avoid database error
			// --- End: New logic ---

			// Check if password is empty (for new user, it will be set)
			if (empty($_POST['password']))
				unset($_POST['password']);
			else
				$_POST['password'] = md5($_POST['password']); // Hash the password

			extract($_POST);
			$data = '';

			// Whitelist of allowed fields to prevent mass assignment vulnerabilities
			$allowed_fields = ['firstname', 'middlename', 'lastname', 'username', 'password', 'type'];

			foreach ($_POST as $k => $v) {
				if (in_array($k, $allowed_fields) && !is_numeric($k)) {
					$v = $this->conn->real_escape_string($v);
					if (!empty($data)) $data .= ", ";
					$data .= " `{$k}` = '{$v}' ";
				}
			}

			// --- Check if it's a new user ---
			if (empty($id)) {
				// Check if the username already exists
				$check = $this->conn->query("SELECT * FROM `users` WHERE username = '{$username}'")->num_rows;
				if ($check > 0) {
					$resp['status'] = 'failed';
					$resp['msg'] = 'Username already exists.';
					return json_encode($resp);
					exit;
				}

				// --- Insert new user ---
				$sql = "INSERT INTO `users` SET $data";
				$save = $this->conn->query($sql);
				$new_user_id = $this->conn->insert_id; // Get the new user ID after insert

				if ($save) {
					$resp['status'] = 'success';
					$resp['msg'] = 'สร้างบัญชีสมาชิกเรียบร้อย';


					// --- Start: Save cropped image logic ---
					if (!empty($cropped_image_data)) {
						$upload_path = base_app . "uploads/avatars/";
						if (!is_dir($upload_path))
							mkdir($upload_path, 0777, true);

						// Decode base64 image
						$image_parts = explode(";base64,", $cropped_image_data);
						$image_base64 = base64_decode($image_parts[1]);

						// Define file name
						$fname = "{$upload_path}{$new_user_id}.png"; // Standardize as .png

						// Save the file
						if (file_put_contents($fname, $image_base64)) {
							// Update database with new avatar path
							$avatar_path = "uploads/avatars/{$new_user_id}.png";
							$this->conn->query("UPDATE `users` SET `avatar` = '{$avatar_path}' WHERE id = '{$new_user_id}'");

							// Update session avatar
							if ($this->settings->userdata('id') == $new_user_id)
								$this->settings->set_userdata('avatar', $avatar_path . "?v=" . time());
						} else {
							$resp['msg'] .= " (but failed to save profile picture)";
						}
					}
					// --- End: Save cropped image logic ---
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = $this->conn->error;
				}
			} else {
				// --- Update existing user ---
				// Check if the username already exists
				$check = $this->conn->query("SELECT * FROM `users` WHERE username = '{$username}' AND id != '{$id}'")->num_rows;
				if ($check > 0) {
					$resp['status'] = 'failed';
					$resp['msg'] = 'Username already exists.';
					return json_encode($resp);
					exit;
				}

				// Update the user
				$sql = "UPDATE users SET $data WHERE id = '{$id}'";
				$save = $this->conn->query($sql);

				$resp = array(); // Initialize response array

				if ($save) {
					$resp['status'] = 'success';
					$resp['msg'] = 'บัญชีสมาชิกอัปเดตเรียบร้อย';
					$this->settings->set_flashdata('success', 'บัญชีสมาชิกอัปเดตเรียบร้อย');

					// Update session data
					foreach ($_POST as $k => $v) {
						if ($this->settings->userdata('id') == $id)
							$this->settings->set_userdata($k, $v);
					}

					// --- Start: Save cropped image logic ---
					if (!empty($cropped_image_data)) {
						$upload_path = base_app . "uploads/avatars/";
						if (!is_dir($upload_path))
							mkdir($upload_path, 0777, true);

						// Decode base64 image
						$image_parts = explode(";base64,", $cropped_image_data);
						$image_base64 = base64_decode($image_parts[1]);

						// Define file name
						$fname = "{$upload_path}{$id}.png"; // Standardize as .png

						// Save the file
						if (file_put_contents($fname, $image_base64)) {
							// Update database with new avatar path
							$avatar_path = "uploads/avatars/{$id}.png";
							$this->conn->query("UPDATE `users` SET `avatar` = '{$avatar_path}' WHERE id = '{$id}'");

							// Update session avatar
							if ($this->settings->userdata('id') == $id)
								$this->settings->set_userdata('avatar', $avatar_path . "?v=" . time());
						} else {
							$resp['msg'] .= " (but failed to save profile picture)";
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
				$welcome_subject = "ยินดีต้อนรับสู่ MSG.com!";
				$welcome_body = "สวัสดีคุณ {$firstname}, ขอบคุณที่สมัครสมาชิกกับเรา";
				$recipient = [$email => $firstname]; // ส่งหาผู้ใช้ใหม่
				$this->send_email($recipient, $welcome_subject, $welcome_body);

				// ส่งแจ้งเตือนไปที่ Telegram
				$this->send_telegram_message("มีสมาชิกใหม่: {$firstname} ({$email})");

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
					// กรณีไม่มีการอัปโหลดรูป ให้ใช้รูป default (โค้ดเดิม)
					if (!is_dir(base_app . "uploads/customers"))
						mkdir(base_app . "uploads/customers");
					$fname = "uploads/customers/$uid.png";
					copy(base_app . "uploads/customers/default_user.png", base_app . $fname);
					$this->conn->query("UPDATE `customer_list` set `avatar` = CONCAT('{$fname}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$uid}'");
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


		// ====================================================================
		// 3. ฟังก์ชัน forgot_password ที่เรียกใช้ฟังก์ชันใหม่
		// ====================================================================
		public function forgot_password()
		{
			if (isset($_POST['email'])) {
				$email = $_POST['email'];
				$name = $_POST['name'];

				// ตรวจสอบว่าอีเมลในฐานข้อมูล customer_list มีอยู่หรือไม่
				$query = "SELECT * FROM customer_list WHERE email = ?";
				$stmt = $this->conn->prepare($query);
				$stmt->bind_param('s', $email);
				$stmt->execute();
				$result = $stmt->get_result();

				if ($result->num_rows > 0) {
					$user = $result->fetch_assoc();

					// ดึงข้อมูลผู้ใช้
					$first_name = $user['firstname'];
					$middle_name = $user['middlename'] ?? '';
					$last_name = $user['lastname'];
					$contact = $user['contact'];
					$email_costumer = $user['email'];

					// ---- เตรียมข้อความและข้อมูลสำหรับส่ง ----

					// สำหรับอีเมล
					$email_subject = "คำขอรีเซ็ตรหัสผ่านจากผู้ใช้";
					$email_body = "
						<div style='font-family: Arial, sans-serif; max-width: 600px; margin:auto;'>
							<h2 style='text-align:center;'>คำขอรีเซ็ตรหัสผ่านจากผู้ใช้</h2>
							<p><strong>อีเมล: </strong>{$email_costumer}</p>
							<p><strong>ชื่อ: </strong>{$name}</p>
							<p><strong>ชื่อในระบบ: </strong>{$first_name} {$middle_name} {$last_name}</p>
							<p><strong>เบอร์ติดต่อ: </strong>{$contact}</p>
						</div>
					";
					$admin_emails = [
						'faritre5566@gmail.com' => 'Admin',
						'faritre1@gmail.com'    => 'Admin',
						'faritre4@gmail.com'    => 'Admin'
					];

					// สำหรับ Telegram
					$telegram_message = "
					🔔 <b>คำขอรีเซ็ตรหัสผ่าน</b>
					- <b>อีเมล:</b> {$email_costumer}
					- <b>ชื่อ:</b> {$name}
					- <b>ชื่อในระบบ:</b> {$first_name} {$middle_name} {$last_name}
					- <b>เบอร์ติดต่อ:</b> {$contact}
                	";

					// ---- เรียกใช้ฟังก์ชันเพื่อส่งการแจ้งเตือน ----
					$this->send_email($admin_emails, $email_subject, $email_body);
					$this->send_telegram_message($telegram_message);

					echo json_encode(['status' => 'success', 'msg' => 'คำขอของคุณถูกส่งไปยังทีมงานแล้ว']);
				} else {
					echo json_encode(['status' => 'error', 'msg' => 'ไม่พบข้อมูลอีเมลนี้ในระบบ']);
				}
			} else {
				echo json_encode(['status' => 'error', 'msg' => 'กรุณากรอกอีเมล']);
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
		case 'delete_customer':
			echo $users->delete_customer();
			break;
		// Users.php
		case 'update_profile':
			echo $users->update_profile();
			break;
		// Users.php
		case 'forgot_password':
			echo $users->forgot_password();
			break;
		default:
			// echo $sysset->index();
			break;
	}
