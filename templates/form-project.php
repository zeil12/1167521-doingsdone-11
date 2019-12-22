<section class="content__side">
                <h2 class="content__side-heading">Проекты</h2>

                <nav class="main-navigation">
                    <ul class="main-navigation__list">
                    <?php foreach ($projects as $project): ?>
                        <li class="main-navigation__list-item">
                            <a class="main-navigation__list-item-link <?php if($id === $project["id"]): ?>main-navigation__list-item--active <?php endif ?>" href="?project=<?=$project["id"]?>"><?=htmlspecialchars($project["title"]);?></a>
                            <span class="main-navigation__list-item-count"><?= htmlspecialchars($project["task_count"]); ?></span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </nav>

                <a class="button button--transparent button--plus content__side-button"
                   href="pages/form-project.html" target="project_add">Добавить проект</a>
            </section>
<main class="content__main">
        <h2 class="content__main-heading">Добавление проекта</h2>

        <form class="form"  action="add_project.php" method="post" enctype="multipart/form-data">
          <div class="form__row">
          <?php $classname = isset($errors['name']) ? "form__input--error" : ""; ?>
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input <?=$classname; ?>" type="text" name="name" id="project_name" value="<?= htmlspecialchars(getPostVal("name")); ?>" placeholder="Введите название проекта">
            <?php if (isset($errors["name"])): ?>
                <p class="form__message"><?= $errors["name"]; ?></p>
            <?php endif; ?>   
        </div>

          <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
          </div>
        </form>
      </main>