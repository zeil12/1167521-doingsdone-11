<section class="content__side">
                <h2 class="content__side-heading">Проекты</h2>

                <nav class="main-navigation">
                    <ul class="main-navigation__list">
                    <?php foreach ($projects as $project): ?>
                        <li class="main-navigation__list-item">
                            <a class="main-navigation__list-item-link <?php if($id === $project["id"]): ?>main-navigation__list-item--active <?php endif ?>" href="?project=<?=$project["id"]?>"><?=htmlspecialchars($project["title"]);?></a>
                            <span class="main-navigation__list-item-count"><?= $project["task_count"]; ?></span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </nav>

                <a class="button button--transparent button--plus content__side-button"
                   href="add_project.php" target="project_add">Добавить проект</a>
            </section>

<main class="content__main">
        <h2 class="content__main-heading">Добавление задачи</h2>

        <form class="form" action="add.php" method="post" enctype="multipart/form-data">
          <div class="form__row">
          <?php $classname = isset($errors['title']) ? "form__input--error" : ""; ?>

            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?= $classname; ?>" type="text" name="title" id="name" value="<?= htmlspecialchars(getPostVal("title")); ?>" placeholder="Введите название">
            <?php if (isset($errors["title"])): ?>
                <p class="form__message"><?= $errors["title"]; ?></p>
            <?php endif; ?>  
        </div>

          <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select <?php if (isset($errors["project"])): ?>form__input--error<?php endif; ?>" name="project" id="project">
              <option>Выберите проект</option>
              <?php foreach ($projects as $item): ?>
                    <option value="<?= $item["id"]; ?>"
                        <?php if ($item["id"] == getPostVal("project")): ?>selected<?php endif; ?>>
                        <?= $item["title"]; ?>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors["project"])): ?>
                <p class="form__message"><?= $errors["project"]; ?></p>
            <?php endif; ?>
          </div>

          <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

<input class="form__input form__input--date <?php if (isset($errors["date"])): ?>form__input--error<?php endif; ?>" type="text" name="date" id="date" value="<?=htmlspecialchars(getPostVal('date')) ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
            <?php if (isset($errors["date"])): ?>
                <p class="form__message"><?= $errors["date"]; ?></p>
            <?php endif; ?>
        </div>
          <div class="form__row">
            <label class="form__label" for="file">Файл</label>

            <div class="form__input-file <?php if (isset($errors["file"])): ?>form__input--error<?php endif; ?>">
              <input class="visually-hidden" type="file" name="file" id="file" value="">

              <label class="button button--transparent" for="file">
                <span>Выберите файл</span>
              </label>
              <?php if (isset($errors["file"])): ?>
                    <p class="form__message"><?= $errors["file"]; ?></p>
                <?php endif; ?>
            </div>
          </div>

          <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
          </div>
        </form>
      </main>