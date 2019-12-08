Уважаемый, <?=$user["user_name"]; ?>. 
У вас запланирована <?php foreach ($tasks as $item): ?> задача <?=$item["task_name"]; ?> на  <?= date('Y-m-d', strtotime($item["deadline"])); ?>.<?php endforeach; ?>


