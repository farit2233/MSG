<?php
require_once '../config.php';
class Login extends DBConnection
{
	private $settings;
	public function __construct()
	{
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct()
	{
		parent::__destruct();
	}
	public function index()
	{
		echo "<h1>Access Denied</h1> <a href='" . base_url . "'>Go Back.</a>";
	}
	public function login()
	{
		// ใช้ตัวแปรโดยตรงจาก $_POST เพื่อความชัดเจนและปลอดภัยกว่า extract()
		$username = $_POST['username'];
		$password = $_POST['password'];

		// 1. เตรียมคำสั่ง SQL เพื่อดึงข้อมูลผู้ใช้จาก "username" เพียงอย่างเดียว
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();

		// 2. ตรวจสอบว่ามีผู้ใช้นี้ในระบบหรือไม่
		if ($result->num_rows > 0) {
			$user_data = $result->fetch_assoc(); // ดึงข้อมูลผู้ใช้มาเก็บในรูปแบบ array

			// 3. ใช้ password_verify() เพื่อเปรียบเทียบรหัสผ่านที่กรอกกับ hash ในฐานข้อมูล
			if (password_verify($password, $user_data['password'])) {
				// ถ้ารหัสผ่านถูกต้อง
				foreach ($user_data as $k => $v) {
					if ($k != 'password') { // ไม่ควรเก็บรหัสผ่านไว้ใน session
						$this->settings->set_userdata($k, $v);
					}
				}

				// อัปเดตการเข้าสู่ระบบล่าสุด
				$last_login = date('Y-m-d H:i:s');
				$update_stmt = $this->conn->prepare("UPDATE users SET last_login = ? WHERE id = ?"); // อัปเดตโดยใช้ id จะเร็วกว่า
				$update_stmt->bind_param('si', $last_login, $user_data['id']);
				$update_stmt->execute();

				$this->settings->set_userdata('login_type', 1);

				return json_encode(array('status' => 'success'));
			} else {
				// รหัสผ่านไม่ถูกต้อง
				return json_encode(array('status' => 'incorrect'));
			}
		} else {
			// ไม่มีชื่อผู้ใช้นี้ในระบบ
			return json_encode(array('status' => 'incorrect'));
		}
	}
	public function logout()
	{
		if ($this->settings->sess_des()) {
			redirect('admin/login.php');
		}
	}
	function login_customer()
	{
		extract($_POST);

		// เตรียมคำสั่ง SQL เพื่อตรวจสอบอีเมล
		$stmt = $this->conn->prepare("SELECT * from customer_list WHERE email = ?");
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			$res = $result->fetch_array();

			// ตรวจสอบรหัสผ่านที่กรอกกับรหัสผ่านที่เก็บไว้ในฐานข้อมูล
			if (password_verify($password, $res['password'])) {
				// ตั้งค่าข้อมูลผู้ใช้ใน session หรือ userdata
				foreach ($res as $k => $v) {
					$this->settings->set_userdata($k, $v);
				}

				// ตั้งค่า login_type เป็น 2 สำหรับลูกค้า
				$this->settings->set_userdata('login_type', 2);

				// อัปเดต last_login
				$last_login = date('Y-m-d H:i:s'); // วันที่และเวลาปัจจุบัน
				$update_stmt = $this->conn->prepare("UPDATE customer_list SET last_login = ? WHERE email = ?");
				$update_stmt->bind_param('ss', $last_login, $email);
				$update_stmt->execute();

				// ส่งผลลัพธ์ที่สำเร็จ
				$resp['status'] = 'success';
				$resp['msg'] = "เข้าสู่ระบบสำเร็จ";
			} else {
				// ถ้ารหัสผ่านไม่ตรง
				$resp['status'] = 'error';
				$resp['msg'] = "<i class='fa fa-exclamation-triangle'></i> บัญชี หรือรหัสผ่านไม่ถูกต้อง";
			}
		} else {
			// ถ้าอีเมลไม่พบในฐานข้อมูล
			$resp['status'] = 'error';
			$resp['msg'] = "<i class='fa fa-exclamation-triangle'></i> บัญชี หรือรหัสผ่านไม่ถูกต้อง";
		}

		// ตรวจสอบข้อผิดพลาดจากการเชื่อมต่อ
		if ($this->conn->error) {
			$resp['status'] = 'error';
			$resp['_error'] = $this->conn->error;
		}

		// คืนค่า JSON response
		return json_encode($resp);
	}

	public function logout_customer()
	{
		if ($this->settings->sess_des()) {
			redirect('?');
		}
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->$action();
		break;
	case 'logout':
		echo $auth->logout(); //<!-- ans. -->
		break;
	case 'login_customer':
		echo $auth->login_customer(); //<!-- ans. -->
		break;
	case 'logout_customer':
		echo $auth->logout_customer(); //<!-- ans. -->
		break;
	default:
		echo $auth->index();
		break;
}
