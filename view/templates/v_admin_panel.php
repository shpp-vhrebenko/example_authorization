<?php //TODO переделать вывод ?>
<section>
    <form method="post" autocomplete="off">
        <table>
            <tr>
                <td>имя</td>
                <td>user</td>
                <td>moder</td>
                <td>admin</td>
            </tr>
            <?php foreach($usersList as $value): ?>
            <tr>
                <td>
                    <label><?php echo $value['name'] ?>:</label>
                </td>
                <td>
                    <input type="radio" name="<?php echo $value['id_user'] ?>" value="1" <?php if($value['id_role'] == 1) {echo 'checked';} ?>>
                </td>
                <td>
                    <input type="radio" name="<?php echo $value['id_user'] ?>" value="2" <?php if($value['id_role'] == 2) {echo 'checked';} ?>>
                </td>
                <td>
                    <input type="radio" name="<?php echo $value['id_user'] ?>" value="3" <?php if($value['id_role'] == 3) {echo 'checked';} ?>>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <input type="submit">
    </form>
</section>
