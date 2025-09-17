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
		extract($_POST);

		// เตรียมคำสั่ง SQL เพื่อตรวจสอบชื่อผู้ใช้
		$stmt = $this->conn->prepare("SELECT * from users where username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			// ถ้าพบผู้ใช้
			$user = $result->fetch_array();

			// ตรวจสอบรหัสผ่านที่กรอกกับรหัสผ่านที่เก็บไว้ในฐานข้อมูล
			if (password_verify($password, $user['password'])) {
				// ถ้ารหัสผ่านตรง
				foreach ($user as $k => $v) {
					if (!is_numeric($k) && $k != 'password') {
						$this->settings->set_userdata($k, $v);
					}
				}

				// อัปเดตการเข้าสู่ระบบล่าสุด
				$last_login = date('Y-m-d H:i:s'); // วันที่และเวลาปัจจุบัน
				$update_stmt = $this->conn->prepare("UPDATE users SET last_login = ? WHERE username = ?");
				$update_stmt->bind_param('ss', $last_login, $username);
				$update_stmt->execute();

				// ตั้งค่า login_type เป็น 1
				$this->settings->set_userdata('login_type', 1);

				return json_encode(array('status' => 'success'));
			} else {
				// ถ้ารหัสผ่านไม่ถูกต้อง
				return json_encode(array('status' => 'incorrect', 'msg' => 'รหัสผ่านไม่ถูกต้อง'));
			}
		} else {
			// ถ้าชื่อผู้ใช้ไม่ถูกต้อง
			return json_encode(array('status' => 'incorrect', 'msg' => 'ชื่อผู้ใช้ไม่ถูกต้อง'));
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

		// เตรียมคำสั่ง SQL เพื่อตรวจสอบอีเมลและรหัสผ่าน
		$stmt = $this->conn->prepare("SELECT * from customer_list where email = ? ");
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
			} else {
				// ถ้ารหัสผ่านไม่ตรง
				$resp['status'] = 'failed';
				$resp['msg'] = "<i class='fa fa-exclamation-triangle'></i> บัญชี หรือรหัสผ่านไม่ถูกต้อง";
			}
		} else {
			// ถ้าอีเมลไม่พบในฐานข้อมูล
			$resp['status'] = 'failed';
			$resp['msg'] = "<i class='fa fa-exclamation-triangle'></i> บัญชี หรือรหัสผ่านไม่ถูกต้อง";
		}

		// ตรวจสอบข้อผิดพลาดจากการเชื่อมต่อ
		if ($this->conn->error) {
			$resp['status'] = 'failed';
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
