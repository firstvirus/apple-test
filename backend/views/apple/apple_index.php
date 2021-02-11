<?php

$this->title = 'Яблоки';

?>

<div class="site-index">
    <div class="col-md-4">
        <a href="/apple/create-apples"
           class="btn btn-default">Создать яблочки</a>
    </div>
    <div class="col-md-4">
        <input id="color_apple" type="text" name="color"
               placeholder="Цвет яблочка" class="form-control">
    </div>
    <div class="col-md-4">
        <button id="create_apple"
                class="btn btn-default">Дайте еще яблочко</button>
    </div>
    <table id="apples" class="table">
        <th>Цвет</th>
        <th>Дата появления</th>
        <th>Дата падения</th>
        <th>Статус</th>
        <th>Размер</th>
        <th>Управление</th>
<?php
        if (!is_null($apples)) {
            foreach ($apples as $apple) {
?>
        <tr class="id-<?= $apple['id'] ?>">
            <td><?= $apple['color'] ?></td>
            <td><?= $apple['dateOfAppearance'] ?></td>
            <td class="apple-date-of-fall"><?= $apple['dateOfFall'] ?></td>
            <td class="apple-status"><?= $apple['status'] ?></td>
            <td class="apple-size"><?= $apple['size'] ?></td>
            <td class="apple-function">
<?php           switch ($apple['status_int']) {
                    case 1: ?>
                <div class="col-md-4">
                    <button class="btn btn-default apple_fall"
                            value="<?= $apple['id'] ?>">Уронить</button>
                </div>
                        <?php
                        break;
                    case 2: ?>
                <div class="col-md-4">
                    <input type="text" name="percent" placeholder="Проценты"
                           class="form-control">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-default apple_eat"
                            value="<?= $apple['id'] ?>">Съесть</button>
                </div>
                        <?php
                        break;
                } ?>
            </td>
        </tr>
        <?php }
        } ?>
    </table>
</div>

<?php
$ajaxJs = <<<JS

$('#create_apple').on('click', function() {
    $.ajax({
        url: '/apple/ajax-create-apple',
        data: { color : $("#color_apple").val() },
        method: 'POST',
        success: function(data) {
            data = JSON.parse(data);
            $('#apples').append('<tr class="id-' + data['id'] + '">' +
                '<td>' + data['color'] + '</td>' +
                '<td>' + data['dateOfAppearance'] + '</td>' +
                '<td class="apple-date-of-fall">' + data['dateOfFall'] +
                                                                       '</td>' +
                '<td class="apple-status">' + data['status'] + '</td>' +
                '<td class="apple-size">' + data['size'] + '</td>' +
                '<td class="apple-function">' + 
                    '<div class="col-md-4">' + 
                        '<button class="btn btn-default apple_fall"' +
                                'value="' + data['id'] + '">Уронить</button>' +
                    '</div></td></tr>');
        },
    });
});

$('.apple_fall').on('click', function() {
    $.ajax({
        url: '/apple/ajax-fall-apple',
        data: { id : $(this).val() },
        method: 'POST',
        success: function(data) {
            data = JSON.parse(data);
            $(".id-" + data["id"] + " .apple-date-of-fall").
                text(data["dateOfFall"]);
            $(".id-" + data["id"] + " .apple-status").
                text(data["status"]);
            $(".id-" + data["id"] + " .apple-function").
                html('<div class="col-md-4">' +
                     '<input type="text" name="percent" placeholder="Проценты"' +
                         'class="form-control"></div>'
                   + '<div class="col-md-4"><button class="btn btn-default apple_eat"'
                                     + 'value="data["id"]">Съесть</button></div>');
        },
    });
});

$('.apple_eat').on('click', function() {
    $.ajax({
        url: '/apple/ajax-eat-apple',
        data: {
            id : $(this).val(),
            percent : $('.id-' + $(this).val() + ' .apple-function input').val()
        },
        method: 'POST',
        success: function(data) {
            data = JSON.parse(data);
            if (data["size"] == 0) {
                $(".id-" + data["id"] + " .apple-function").html("");
                $(".id-" + data["id"] + " .apple-size").text("Съедено!");
            } else {
                $(".id-" + data["id"] + " .apple-size").text(data["size"]);
            }
        },
    });
});

JS;

$this->registerJs($ajaxJs);
