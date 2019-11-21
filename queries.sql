-- Добавляем записи в таблицу user

INSERT INTO user (registration_date, email, user_name, password) VALUES 
('2019-09-25 12:25:34', 'sefef@mail.ru', 'Mila', 'lollolvfr1234'),
('2019-10-22 14:36:12', 'caresdf@gmail.com', 'Jack', 'qwerty654');

-- Добавляем записи в таблицу project

INSERT INTO project ('title', 'user_id') VALUES 
('Авто', 1),
('Входящие', 2),
('Домашние дела', 2),
('Здоровье', 1),
('Работа', 1);

-- Добавляем записи в таблицу task

INSERT INTO task ('creation_date', 'status', 'task_name', 'file_link', 'deadline', 'user_id', 'project_id') VALUES 
('2019-10-31 22:43:12', 0, 'Купить корм для кота', NULL, '2019-12-02 22:42:10', 2, 3),
('2019-09-11 12:03:12', 0, 'Встреча с другом', NULL, '2019-12-31 23:02:40', 2, 2),
('2019-09-11 12:03:12', 1, 'Собеседование в IT компании', NULL, '2019-12-12 20:02:10', 1, 5),
('2019-09-03 09:01:00', 0, 'Отремонтировать машину', NULL, '2019-11-25 18:14:43', 1, 1),
('2019-11-07 20:12:00', 1, 'Заказать пиццу', NULL, '2019-11-08 20:12:01', 2, 3);

--получить список из всех проектов для одного пользователя

SELECT id, title FROM project WHERE user_id = 1;

--получить список из всех задач для одного проекта

SELECT id, task_name FROM task WHERE project_id = 3;

--пометить задачу как выполненную

UPDATE task SET status = 1 WHERE id = 2;

--обновить название задачи по её идентификатору

UPDATE task SET task_name = 'Отремонтировать машину' WHERE id = 4;

