<?php
require_once '../../php-modules/connect-db.php';

try {
  $query = "
        SELECT 
            u.id,
            CONCAT(u.last_name, ' ', u.first_name, ' ', COALESCE(u.patronomic, '')) AS full_name,
            r.name AS position,
            u.photo_file,
            u.is_active
        FROM users u
        JOIN roles r ON u.role_id = r.id
        ORDER BY u.last_name, u.first_name
    ";

  $stmt = $conn->prepare($query);
  $stmt->execute();
  $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  echo "Ошибка: " . $e->getMessage();
  exit;
}
?>

<h1 class="content__title">Админ-панель</h1>
<div class="table__outer">
  <button class="table__button" id="add-worker-button" data-modal="components/admin/registration-form.php">
    <svg class="table__add-icon" width="30" height="30" viewBox="0 0 40 41" fill="none"
      xmlns="http://www.w3.org/2000/svg">
      <g clip-path="url(#clip0_150_374)">
        <path fill-rule="evenodd" clip-rule="evenodd"
          d="M13.3333 4.94444C13.3333 3.7657 13.8016 2.63524 14.6351 1.80175C15.4686 0.968252 16.599 0.5 17.7778 0.5H22.2222C23.401 0.5 24.5314 0.968252 25.3649 1.80175C26.1984 2.63524 26.6667 3.7657 26.6667 4.94444V13.8333H35.5556C36.7343 13.8333 37.8648 14.3016 38.6983 15.1351C39.5317 15.9686 40 17.099 40 18.2778V22.7222C40 23.901 39.5317 25.0314 38.6983 25.8649C37.8648 26.6984 36.7343 27.1667 35.5556 27.1667H26.6667V36.0556C26.6667 37.2343 26.1984 38.3648 25.3649 39.1983C24.5314 40.0317 23.401 40.5 22.2222 40.5H17.7778C16.599 40.5 15.4686 40.0317 14.6351 39.1983C13.8016 38.3648 13.3333 37.2343 13.3333 36.0556V27.1667H4.44444C3.2657 27.1667 2.13524 26.6984 1.30175 25.8649C0.468252 25.0314 0 23.901 0 22.7222V18.2778C0 17.099 0.468252 15.9686 1.30175 15.1351C2.13524 14.3016 3.2657 13.8333 4.44444 13.8333H13.3333V4.94444ZM22.2222 4.94444H17.7778V16.0556C17.7778 16.6449 17.5437 17.2102 17.1269 17.6269C16.7102 18.0437 16.1449 18.2778 15.5556 18.2778H4.44444V22.7222H15.5556C16.1449 22.7222 16.7102 22.9564 17.1269 23.3731C17.5437 23.7898 17.7778 24.3551 17.7778 24.9444V36.0556H22.2222V24.9444C22.2222 24.3551 22.4564 23.7898 22.8731 23.3731C23.2898 22.9564 23.8551 22.7222 24.4444 22.7222H35.5556V18.2778H24.4444C23.8551 18.2778 23.2898 18.0437 22.8731 17.6269C22.4564 17.2102 22.2222 16.6449 22.2222 16.0556V4.94444Z"
          fill="white" />
      </g>
      <defs>
        <clipPath id="clip0_150_374">
          <rect width="40" height="40" fill="white" transform="translate(0 0.5)" />
        </clipPath>
      </defs>
    </svg>
  </button>
  <table class="table">
    <caption class="table__heading">
      <h2 class="secondary-heading">Работники</h2>
    </caption>
    <thead class="table__head">
      <tr class="table__row">
        <th class="table__cell table__cell--head">ФИО</th>
        <th class="table__cell table__cell--head">Должность</th>
        <th class="table__cell table__cell--head">Фото</th>
        <th class="table__cell table__cell--head">Статус</th>
      </tr>
    </thead>
    <tbody class="table__body">
      <?php foreach ($workers as $worker): ?>
        <?php
        $status = $worker['is_active'] ? 'Работает' : 'Уволен';
        $statusClass = $worker['is_active'] ? 'table__cell--active' : 'table__cell--inactive';
        $photoPath = $worker['photo_file'] ?: 'assets/images/default-avatar.jpg';
        ?>
        <tr class="table__row table__row--interactive <?= $worker['is_active'] ? '' : 'table__row--inactive' ?>">
          <td class="table__cell" data-worker-id="<?= $worker['id'] ?>" data-modal="components/admin/view-worker-shifts.php">
            <?= htmlspecialchars($worker['full_name']) ?>
          </td>
          <td class="table__cell">
            <?= htmlspecialchars($worker['position']) ?>
          </td>
          <td class="table__cell">
            <div class="photo__wrapper photo__wrapper--small">
              <img class="photo" src="<?= htmlspecialchars($photoPath) ?>"
                alt="Фото работника <?= htmlspecialchars($worker['full_name']) ?>">
            </div>
          </td>
          <td class="table__cell <?= $statusClass ?>" data-worker-id="<?= $worker['id'] ?>" data-action="dismiss"
            data-modal="components/admin/dismiss-worker-form.php">
            <?= $status ?>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($workers)): ?>
        <tr class="table__row">
          <td colspan="4" class="table__cell">Нет данных о работниках</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>