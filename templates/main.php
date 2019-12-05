<section class="content__side">
                <h2 class="content__side-heading">Проекты</h2>

                <nav class="main-navigation">
                    <ul class="main-navigation__list">
                    <?php foreach ($projects as $project): ?>
                        <li class="main-navigation__list-item">
                            <a class="main-navigation__list-item-link <?php if($id == $project["id"]): ?>main-navigation__list-item--active <?php endif ?>" 
                            href="?project=<?=$project["id"]?>"><?=htmlspecialchars($project["title"]);?></a>
                            <span class="main-navigation__list-item-count"><?= $project["task_count"]; ?></span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </nav>

                <a class="button button--transparent button--plus content__side-button"
                   href="add_project.php" target="project_add">Добавить проект</a>
            </section>

            <main class="content__main">
                <h2 class="content__main-heading">Список задач</h2>
                <form class="search-form" action="index.php" method="get" autocomplete="off">
                    <input class="search-form__input" type="text" name="search" value="" placeholder="Поиск по задачам">

                    <input class="search-form__submit" type="submit" name="" value="Искать">
                </form>

                <div class="tasks-controls">
                    <nav class="tasks-switch">
                        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
                        <a href="/?filter=today" class="tasks-switch__item">Повестка дня</a>
                        <a href="/?filter=tomorrow" class="tasks-switch__item">Завтра</a>
                        <a href="/?filter=past" class="tasks-switch__item">Просроченные</a>
                    </nav>
                        <input class="checkbox__input visually-hidden show_completed" type="checkbox">
                          <label class="checkbox">
                <input class="checkbox__input visually-hidden show_completed" type="checkbox"
                    <?php if ($show_complete_tasks == 1): ?>
                        checked
                    <?php endif; ?>>
                        <span class="checkbox__text">Показывать выполненные</span>
                    </label>
                </div>

                <table class="tasks">
                <?php if (isset($_GET["search"]) && empty($tasks)): ?><? print_r($search_error); ?><?php endif; ?>
                 <?php foreach ($tasks as $item): ?>
                    <?php if ($show_complete_tasks == 0 && $item["status"] == 1): ?>
                      <?php continue; ?>
                    <?php endif; ?>
                      <tr class="tasks__item task <?php if ($item["status"]): ?>task--completed<?php endif; ?>
                      <?php if (is_task_urgent($item["deadline"])): ?>task--important<?php endif; ?>">
                        <td class="task__select">
                          <label class="checkbox task__checkbox">
                          <?php if (isset($item["id"])): ?>
                        <a href="index.php?task_id=<?= $item["id"]; ?>">
                            <?php endif; ?>
                            <input class="checkbox__input visually-hidden" type="checkbox"
                                <?php if (isset($item["status"]) && $item["status"]): ?>
                                    checked
                                <?php endif; ?>>
                            <span class="checkbox__text"><?=htmlspecialchars($item["task_name"]); ?></span>
                          </label>
                        </td>
                        <td class="task__date"><?= htmlspecialchars(date("d.m.Y", strtotime($item["deadline"]))); ?></td>
                        <td class="task__controls"></>
                      </tr>
                 <?php endforeach; ?>
                </table>
            </main>