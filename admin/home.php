<?php
// --- PHP Data Fetching ---

// แก้ไขข้อผิดพลาด: ดึงยอดขายวันนี้จากคอลัมน์ 'total_amount'
$today_sales_query = $conn->query("SELECT SUM(total_amount) as total FROM order_list WHERE DATE(date_created) = CURDATE()");
$today_sales = $today_sales_query->fetch_assoc()['total'] ?? 0;

// คำสั่งซื้อทั้งหมด
$total_orders = $conn->query("SELECT id FROM order_list")->num_rows;

// สินค้าทั้งหมดที่ยังเปิดใช้งาน
$total_products = $conn->query("SELECT id FROM product_list where `status` = 1 and delete_flag = 0")->num_rows;

// ตรวจสอบสต็อกใกล้หมด (น้อยกว่าหรือเท่ากับ 10)
$low_stock_threshold = 10;
$low_stock_query = $conn->query("
    SELECT COUNT(pl.id) as low_stock_count
    FROM product_list pl
    WHERE pl.delete_flag = 0 AND pl.status = 1 AND (
        COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = pl.id), 0)
        -
        COALESCE((SELECT SUM(oi.quantity) FROM `order_items` oi INNER JOIN `order_list` ol ON oi.order_id = ol.id WHERE oi.product_id = pl.id AND ol.delivery_status != 10), 0)
    ) <= {$low_stock_threshold}
");
$low_stock_count = $low_stock_query->fetch_assoc()['low_stock_count'] ?? 0;


// ข้อมูลสำหรับกราฟแนวโน้มยอดขาย 6 เดือนล่าสุด
$sales_chart_labels = [];
$sales_chart_data = [];
$thai_months = [
  '1' => 'ม.ค.',
  '2' => 'ก.พ.',
  '3' => 'มี.ค.',
  '4' => 'เม.ย.',
  '5' => 'พ.ค.',
  '6' => 'มิ.ย.',
  '7' => 'ก.ค.',
  '8' => 'ส.ค.',
  '9' => 'ก.ย.',
  '10' => 'ต.ค.',
  '11' => 'พ.ย.',
  '12' => 'ธ.ค.'
];

for ($i = 5; $i >= 0; $i--) {
  $month_date = date('Y-m', strtotime("-$i months"));
  $month_num = date('n', strtotime($month_date));
  $sales_chart_labels[] = $thai_months[$month_num];

  $query = "SELECT SUM(total_amount) as monthly_total FROM order_list WHERE DATE_FORMAT(date_created, '%Y-%m') = '{$month_date}'";
  $result = $conn->query($query);
  $row = $result->fetch_assoc();
  $sales_chart_data[] = $row['monthly_total'] ?? 0;
}
$sales_chart_labels_json = json_encode($sales_chart_labels);
$sales_chart_data_json = json_encode($sales_chart_data);


// คำสั่งซื้อล่าสุด 5 รายการ
$recent_orders_query = $conn->query("SELECT * FROM `order_list` ORDER BY `date_created` DESC LIMIT 5");

// Array สำหรับสถานะการชำระเงินและการจัดส่ง
$payment_status_map = [
  0 => ['label' => 'ยังไม่ชำระเงิน', 'bg' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-wallet'],
  1 => ['label' => 'รอตรวจสอบ', 'bg' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-search-dollar'],
  2 => ['label' => 'ชำระเงินแล้ว', 'bg' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check-circle'],
  3 => ['label' => 'ชำระเงินล้มเหลว', 'bg' => 'bg-red-100 text-red-800', 'icon' => 'fa fa-exclamation-circle'],
  4 => ['label' => 'รอการยกเลิก', 'bg' => 'bg-orange-100 text-orange-800', 'icon' => 'fas fa-clock'],
  5 => ['label' => 'คำขอคืนเงิน', 'bg' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-reply'],
  6 => ['label' => 'คืนเงินแล้ว', 'bg' => 'bg-indigo-100 text-indigo-800', 'icon' => 'fas fa-undo-alt'],
];

$delivery_status_map = [
  0 => ['label' => 'ตรวจสอบคำสั่งซื้อ', 'bg' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-file-invoice'],
  1 => ['label' => 'กำลังเตรียมของ', 'bg' => 'bg-cyan-100 text-cyan-800', 'icon' => 'fas fa-box'],
  2 => ['label' => 'แพ๊กของแล้ว', 'bg' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-truck-loading'],
  3 => ['label' => 'กำลังจัดส่ง', 'bg' => 'bg-indigo-100 text-indigo-800', 'icon' => 'fas fa-truck'],
  4 => ['label' => 'จัดส่งสำเร็จ', 'bg' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check-circle'],
  5 => ['label' => 'จัดส่งไม่สำเร็จ', 'bg' => 'bg-orange-100 text-orange-800', 'icon' => 'fas fa-exclamation-triangle'],
  6 => ['label' => 'รอการยกเลิก', 'bg' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock'],
  7 => ['label' => 'คืนของระหว่างทาง', 'bg' => 'bg-purple-100 text-purple-800', 'icon' => 'fas fa-truck-moving'],
  8 => ['label' => 'คำขอคืนสินค้า', 'bg' => 'bg-pink-100 text-pink-800', 'icon' => 'fas fa-reply'],
  9 => ['label' => 'คืนของสำเร็จ', 'bg' => 'bg-teal-100 text-teal-800', 'icon' => 'fas fa-box-open'],
  10 => ['label' => 'ยกเลิกแล้ว', 'bg' => 'bg-red-100 text-red-800', 'icon' => 'fa-solid fa-circle-xmark'],
];

?>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  /* ใช้ฟอนต์ที่สวยงามและเข้ากับระบบ */
  body,
  h1,
  h2,
  h3,
  h4,
  h5,
  h6,
  p,
  span,
  a,
  th,
  td {
    font-family: 'Sarabun', sans-serif;
  }

  .stat-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }
</style>

<div class="w-full px-4 md:px-6 py-6">
  <h1 class="text-2xl font-bold text-gray-800 mb-2">
    ยินดีต้อนรับ, <?php echo $_settings->userdata('firstname') . " " . $_settings->userdata('lastname') ?>!
  </h1>
  <p class="text-gray-600 mb-6">ภาพรวมข้อมูลทั้งหมดในระบบของคุณ</p>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500">ยอดขายวันนี้</p>
        <p class="text-3xl font-bold text-gray-800">฿<?php echo format_num($today_sales, 2); ?></p>
      </div>
      <div class="bg-indigo-100 rounded-full p-3">
        <i class="fas fa-dollar-sign h-6 w-6 text-indigo-500"></i>
      </div>
    </div>
    <div class="stat-card bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500">คำสั่งซื้อทั้งหมด</p>
        <p class="text-3xl font-bold text-gray-800"><?php echo format_num($total_orders); ?></p>
      </div>
      <div class="bg-green-100 rounded-full p-3">
        <i class="fas fa-shopping-cart h-6 w-6 text-green-500"></i>
      </div>
    </div>
    <div class="stat-card bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500">สินค้าทั้งหมด</p>
        <p class="text-3xl font-bold text-gray-800"><?php echo format_num($total_products); ?></p>
      </div>
      <div class="bg-blue-100 rounded-full p-3">
        <i class="fas fa-boxes h-6 w-6 text-blue-500"></i>
      </div>
    </div>
    <div class="stat-card <?php echo $low_stock_count > 0 ? 'bg-red-50 border-red-500' : 'bg-white'; ?> rounded-lg shadow-md p-6 flex items-center justify-between border-2">
      <div>
        <p class="text-sm font-medium <?php echo $low_stock_count > 0 ? 'text-red-700' : 'text-gray-500'; ?>">สต็อกใกล้หมด/หมด</p>
        <p class="text-3xl font-bold <?php echo $low_stock_count > 0 ? 'text-red-600' : 'text-gray-800'; ?>"><?php echo format_num($low_stock_count); ?></p>
      </div>
      <div class="<?php echo $low_stock_count > 0 ? 'bg-red-100' : 'bg-yellow-100'; ?> rounded-full p-3">
        <i class="fas fa-exclamation-triangle h-6 w-6 <?php echo $low_stock_count > 0 ? 'text-red-500' : 'text-yellow-500'; ?>"></i>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
    <div class="lg:col-span-3 bg-white rounded-lg shadow-md p-6">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">แนวโน้มยอดขาย (6 เดือนล่าสุด)</h3>
      <div class="relative h-72">
        <canvas id="salesChart"></canvas>
      </div>
    </div>
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md">
      <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-gray-700">คำสั่งซื้อล่าสุด</h3>
      </div>
      <div class="overflow-y-auto h-72">
        <table class="w-full text-sm text-left text-gray-500">
          <tbody>
            <?php while ($row = $recent_orders_query->fetch_assoc()): ?>
              <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-6 py-3">
                  <p class="font-medium text-gray-900"><?php echo $row['code'] ?></p>
                  <p class="text-xs text-gray-500"><?php echo date("d M Y, H:i", strtotime($row['date_created'])) ?></p>
                </td>
                <td class="px-6 py-3 text-right">
                  <p class="font-medium text-gray-900">฿<?php echo format_num($row['total_amount'], 2) ?></p>
                  <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $payment_status_map[$row['payment_status']]['bg'] ?? 'bg-gray-100 text-gray-800'; ?>">
                    <?php echo $payment_status_map[$row['payment_status']]['label'] ?? 'ไม่ระบุ'; ?>
                  </span>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">สถานะการชำระเงิน</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php foreach ($payment_status_map as $status => $data):
          $count = $conn->query("SELECT id FROM order_list WHERE payment_status = $status")->num_rows;
        ?>
          <a href="?page=orders&payment_status=<?= $status ?>" class="bg-gray-50 p-3 rounded-lg flex items-center hover:bg-gray-100 transition-colors">
            <i class="fas <?= $data['icon'] ?> text-gray-500 text-xl w-8 text-center"></i>
            <div class="ml-3">
              <p class="font-semibold text-gray-700"><?= $data['label'] ?></p>
              <p class="text-gray-500"><?= format_num($count) ?> รายการ</p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">สถานะการจัดส่ง</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php foreach ($delivery_status_map as $status => $data):
          $count = $conn->query("SELECT id FROM order_list WHERE delivery_status = $status")->num_rows;
        ?>
          <a href="?page=orders&delivery_status=<?= $status ?>" class="bg-gray-50 p-3 rounded-lg flex items-center hover:bg-gray-100 transition-colors">
            <i class="fas <?= $data['icon'] ?> text-gray-500 text-xl w-8 text-center"></i>
            <div class="ml-3">
              <p class="font-semibold text-gray-700"><?= $data['label'] ?></p>
              <p class="text-gray-500"><?= format_num($count) ?> รายการ</p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Sales Chart
      const salesCtx = document.getElementById('salesChart').getContext('2d');
      const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
          labels: <?php echo $sales_chart_labels_json; ?>,
          datasets: [{
            label: 'ยอดขาย (บาท)',
            data: <?php echo $sales_chart_data_json; ?>,
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            borderColor: 'rgba(79, 70, 229, 1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: 'rgba(79, 70, 229, 1)',
            pointBorderColor: '#fff',
            pointHoverRadius: 6,
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(79, 70, 229, 1)'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value, index, values) {
                  return '฿' + new Intl.NumberFormat().format(value);
                }
              }
            }
          },
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.dataset.label || '';
                  if (label) {
                    label += ': ';
                  }
                  if (context.parsed.y !== null) {
                    label += new Intl.NumberFormat('th-TH', {
                      style: 'currency',
                      currency: 'THB'
                    }).format(context.parsed.y);
                  }
                  return label;
                }
              }
            }
          }
        }
      });
    });
  </script>