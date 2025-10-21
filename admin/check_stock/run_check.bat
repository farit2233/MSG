@echo off
REM --- ตั้งค่า Path ของ PHP และ สคริปต์ ---

REM 1. แก้ไข Path ไปยังไฟล์ php.exe ของคุณ (ตัวอย่างนี้ใช้ของ XAMPP)
set PHP_EXE="C:\xampp\php\php.exe"

REM 2. แก้ไข Path ไปยังไฟล์ check_low_stock.php ของคุณ (ใช้ %~dp0 เพื่ออ้างอิงโฟลเดอร์ปัจจุบัน)
set SCRIPT_PATH="%~dp0check_low_stock.php"

REM --- สั่งรันสคริปต์ ---
echo Running Stock Check Script...
%PHP_EXE% %SCRIPT_PATH%

echo.
echo Script execution finished.
pause