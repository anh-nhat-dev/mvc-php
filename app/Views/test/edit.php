<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <table>
        <?php foreach($users as $user) { ?>
        <tr>
            <td><?= $user->full_name ?></td>
        </tr>
        <?php } ?>
    </table>
</body>

</html>