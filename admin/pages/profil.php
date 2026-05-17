<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';

$currentPage = 'profil';
$pageTitle   = 'Profil Gym';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['gym_name','tagline','history','vision','mission','address','phone','email','whatsapp','maps_embed'];
    $data   = array_map(fn($f) => trim($_POST[$f] ?? ''), array_combine($fields, $fields));

    $exists = $pdo->query('SELECT id FROM gym_profile LIMIT 1')->fetch();
    if ($exists) {
        $set = implode(', ', array_map(fn($f) => "$f = :$f", $fields));
        $stmt = $pdo->prepare("UPDATE gym_profile SET $set WHERE id = :id");
        $data['id'] = $exists['id'];
    } else {
        $cols = implode(', ', $fields) . ', created_by';
        $vals = ':' . implode(', :', $fields) . ', :created_by';
        $stmt = $pdo->prepare("INSERT INTO gym_profile ($cols) VALUES ($vals)");
        $data['created_by'] = $_SESSION['admin_id'];
    }
    $stmt->execute($data);
    flash('success', 'Profil gym berhasil disimpan.');
    header('Location: profil.php');
    exit;
}

$profil = $pdo->query('SELECT * FROM gym_profile LIMIT 1')->fetch() ?: [];

require_once '../../includes/header.php';
?>
<form method="POST" class="form-card">
  <div class="form-section">
    <h3>Identitas Gym</h3>
    <div class="field-row cols-2">
      <div class="field">
        <label>Nama Gym</label>
        <input type="text" name="gym_name" value="<?= e($profil['gym_name'] ?? '') ?>" required>
      </div>
      <div class="field">
        <label>Tagline</label>
        <input type="text" name="tagline" value="<?= e($profil['tagline'] ?? '') ?>">
      </div>
    </div>
    <div class="field">
      <label>Sejarah</label>
      <textarea name="history" rows="3"><?= e($profil['history'] ?? '') ?></textarea>
    </div>
    <div class="field-row cols-2">
      <div class="field">
        <label>Visi</label>
        <textarea name="vision" rows="3"><?= e($profil['vision'] ?? '') ?></textarea>
      </div>
      <div class="field">
        <label>Misi</label>
        <textarea name="mission" rows="3"><?= e($profil['mission'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <div class="form-section">
    <h3>Kontak</h3>
    <div class="field">
      <label>Alamat</label>
      <input type="text" name="address" value="<?= e($profil['address'] ?? '') ?>">
    </div>
    <div class="field-row cols-3">
      <div class="field">
        <label>Telepon</label>
        <input type="text" name="phone" value="<?= e($profil['phone'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" value="<?= e($profil['email'] ?? '') ?>">
      </div>
      <div class="field">
        <label>WhatsApp (tanpa +)</label>
        <input type="text" name="whatsapp" value="<?= e($profil['whatsapp'] ?? '') ?>" placeholder="628xx">
      </div>
    </div>
    <div class="field">
      <label>Google Maps Embed URL</label>
      <input type="url" name="maps_embed" value="<?= e($profil['maps_embed'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?pb=...">
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn-primary">Simpan Profil</button>
  </div>
</form>
<?php require_once '../../includes/footer.php'; ?>
