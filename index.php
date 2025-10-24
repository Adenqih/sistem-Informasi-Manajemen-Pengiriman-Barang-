<?php
// index.php - simple CRUD for shipments
require 'conec.php';

// helper to redirect (and avoid re-submission)
function redirect($url) {
    header("Location: $url");
    exit();
}

// Create or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval($_POST['id']) : null;
    $tracking = $conn->real_escape_string($_POST['tracking_number']);
    $sname = $conn->real_escape_string($_POST['sender_name']);
    $sphone = $conn->real_escape_string($_POST['sender_phone']);
    $rname = $conn->real_escape_string($_POST['receiver_name']);
    $rphone = $conn->real_escape_string($_POST['receiver_phone']);
    $origin = $conn->real_escape_string($_POST['origin']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $weight = floatval($_POST['weight']);
    $status = $conn->real_escape_string($_POST['status']);

    if ($id) {
        $stmt = $conn->prepare("UPDATE shipments SET tracking_number=?, sender_name=?, sender_phone=?, receiver_name=?, receiver_phone=?, origin=?, destination=?, weight=?, status=? WHERE id=?");
        $stmt->bind_param('sssssssdsi', $tracking, $sname, $sphone, $rname, $rphone, $origin, $destination, $weight, $status, $id);
        $stmt->execute();
        $stmt->close();
        redirect('index.php');
    } else {
        $stmt = $conn->prepare("INSERT INTO shipments (tracking_number, sender_name, sender_phone, receiver_name, receiver_phone, origin, destination, weight, status) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssssds', $tracking, $sname, $sphone, $rname, $rphone, $origin, $destination, $weight, $status);
        $stmt->execute();
        $stmt->close();
        redirect('index.php');
    }
}

// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delid = intval($_GET['delete']);
    $stmt = $conn->prepare('DELETE FROM shipments WHERE id=?');
    $stmt->bind_param('i', $delid);
    $stmt->execute();
    $stmt->close();
    redirect('index.php');
}

// Edit (fetch one)
$editing = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    $stmt = $conn->prepare('SELECT * FROM shipments WHERE id=?');
    $stmt->bind_param('i', $eid);
    $stmt->execute();
    $res = $stmt->get_result();
    $editing = $res->fetch_assoc();
    $stmt->close();
}

// List all
$result = $conn->query('SELECT * FROM shipments ORDER BY created_at DESC');

?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ShipTrack - Sistem Informasi Manajemen Pengiriman Barang</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>ShipTrack</h1>
    <p class="subtitle">Sistem Informasi Manajemen Pengiriman Barang</p>
  </header>

  <main class="container">
    <section class="form-card">
      <h2><?php echo $editing ? 'Edit Pengiriman' : 'Buat Pengiriman Baru'; ?></h2>
      <form method="post" action="index.php">
        <?php if ($editing): ?>
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($editing['id']); ?>">
        <?php endif; ?>
        <label>Nomor Resi (Tracking Number)
          <input required name="tracking_number" value="<?php echo $editing ? htmlspecialchars($editing['tracking_number']) : ''; ?>">
        </label>

        <label>Nama Pengirim
          <input required name="sender_name" value="<?php echo $editing ? htmlspecialchars($editing['sender_name']) : ''; ?>">
        </label>

        <label>Telepon Pengirim
          <input name="sender_phone" value="<?php echo $editing ? htmlspecialchars($editing['sender_phone']) : ''; ?>">
        </label>

        <label>Nama Penerima
          <input required name="receiver_name" value="<?php echo $editing ? htmlspecialchars($editing['receiver_name']) : ''; ?>">
        </label>

        <label>Telepon Penerima
          <input name="receiver_phone" value="<?php echo $editing ? htmlspecialchars($editing['receiver_phone']) : ''; ?>">
        </label>

        <label>Asal
          <input name="origin" value="<?php echo $editing ? htmlspecialchars($editing['origin']) : ''; ?>">
        </label>

        <label>Tujuan
          <input name="destination" value="<?php echo $editing ? htmlspecialchars($editing['destination']) : ''; ?>">
        </label>

        <label>Berat (kg)
          <input type="number" step="0.01" name="weight" value="<?php echo $editing ? htmlspecialchars($editing['weight']) : '0.00'; ?>">
        </label>

        <label>Status
          <select name="status">
            <?php
            $opts = ['Draft','Picked Up','In Transit','Delivered','Cancelled'];
            $cur = $editing ? $editing['status'] : 'Draft';
            foreach ($opts as $o) {
                $sel = ($o === $cur) ? 'selected' : '';
                echo "<option value=\"$o\" $sel>$o</option>";
            }
            ?>
          </select>
        </label>

        <div style="text-align:right">
          <button type="submit" class="btn"><?php echo $editing ? 'Simpan Perubahan' : 'Buat Pengiriman'; ?></button>
          <?php if ($editing): ?><a class="btn ghost" href="index.php">Batal</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="table-card">
      <h2>Daftar Pengiriman</h2>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Resi</th>
            <th>Pengirim</th>
            <th>Penerima</th>
            <th>Asal → Tujuan</th>
            <th>Berat (kg)</th>
            <th>Status</th>
            <th>Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $i=1; while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td><?php echo htmlspecialchars($row['tracking_number']); ?></td>
              <td><?php echo htmlspecialchars($row['sender_name']); ?><br><small><?php echo htmlspecialchars($row['sender_phone']); ?></small></td>
              <td><?php echo htmlspecialchars($row['receiver_name']); ?><br><small><?php echo htmlspecialchars($row['receiver_phone']); ?></small></td>
              <td><?php echo htmlspecialchars($row['origin']); ?> → <?php echo htmlspecialchars($row['destination']); ?></td>
              <td><?php echo htmlspecialchars($row['weight']); ?></td>
              <td><?php echo htmlspecialchars($row['status']); ?></td>
              <td><?php echo htmlspecialchars($row['created_at']); ?></td>
              <td class="actions">
                <a class="btn small" href="index.php?edit=<?php echo $row['id']; ?>">Edit</a>
                <a class="btn small danger" href="index.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Hapus pengiriman ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
          <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="9" style="text-align:center">Belum ada data pengiriman.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>

  <footer>
    &copy; <?php echo date('Y'); ?> ShipTrack
  </footer>
</body>
</html>
