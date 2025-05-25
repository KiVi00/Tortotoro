<div class="form__outer" id="form-outer"></div>
<form class="form form--modal form--two-columns" action="registration-form.php" method="post">
    <h1 class="form__heading secondary-heading">Просмотр смен работника</h1>
    <table class="table">
        <caption class="table__heading">
            <h2 class="secondary-heading">Смены</h2>
        </caption>
        <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell table__cell--head">Работники</th>
                <th class="table__cell table__cell--head">Рабочее время</th>
                <th class="table__cell table__cell--head">Статус</th>
            </tr>
        </thead>
        <tbody class="table__body">
            <tr class="table__row">
                <td class="table__cell table__cell--dynamic">Виноградов К. С. (Повар)</td>
                <td class="table__cell table__cell--interactive">
                    23.05.2025 (10:00&ndash;18:00)
                </td>
                <td class="table__cell">Закрыта</td>
            </tr>
            <tr class="table__row">
                <td class="table__cell table__cell--dynamic table__cell--interactive" id="workers-2">Виноградов К. С.
                    (Официант)
                </td>
                <td class="table__cell table__cell--interactive" id="shift-2">24.05.2025 (10:00&ndash;18:00)</td>
                <td class="table__cell table__cell--interactive" id="status-2">Открыта</td>
            </tr>
        </tbody>
    </table>
</form>