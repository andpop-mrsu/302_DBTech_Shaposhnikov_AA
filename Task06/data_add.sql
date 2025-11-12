-- SQL Script для добавления новых данных
BEGIN TRANSACTION;

-- ==============================
-- 👥 Добавление 5 новых пользователей
-- ==============================
INSERT INTO users (name, email, gender_id, occupation_id, register_date)
VALUES (
    'Алексей Шапошников',
    'alexeysh945@gmail.com',
    (SELECT id FROM genders WHERE name = 'male'),
    (SELECT id FROM occupations WHERE name = 'student'),
    CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, gender_id, occupation_id, register_date)
VALUES (
    'Роман Лукьянов',
    'romka52@gmail.com',
    (SELECT id FROM genders WHERE name = 'male'),
    (SELECT id FROM occupations WHERE name = 'student'),
    CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, gender_id, occupation_id, register_date)
VALUES (
    'Константин Маркин',
    'markinvolf@gmail.com',
    (SELECT id FROM genders WHERE name = 'male'),
    (SELECT id FROM occupations WHERE name = 'student'),
    CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, gender_id, occupation_id, register_date)
VALUES (
    'Никита Кармазов',
    'karmaz.auto@gmail.com',
    (SELECT id FROM genders WHERE name = 'male'),
    (SELECT id FROM occupations WHERE name = 'programmer'),
    CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, gender_id, occupation_id, register_date)
VALUES (
    'Ольга Шиляева',
    'olyastarosta@gmail.com',
    (SELECT id FROM genders WHERE name = 'female'),
    (SELECT id FROM occupations WHERE name = 'educator'),
    CURRENT_TIMESTAMP
);

-- ==============================
-- 🎬 Добавление 3 новых фильмов
-- ==============================
INSERT INTO movies (title, year)
VALUES ('Матрица: Воскрешение', 2021);

INSERT INTO movies (title, year)
VALUES ('Дюна', 2021);

INSERT INTO movies (title, year)
VALUES ('Человек-паук: Нет пути домой', 2021);

-- ==============================
-- 🔗 Связь фильмов с жанрами
-- ==============================
-- Матрица: Воскрешение
INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Матрица: Воскрешение'),
    (SELECT id FROM genres WHERE name = 'Action')
);
INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Матрица: Воскрешение'),
    (SELECT id FROM genres WHERE name = 'Sci-Fi')
);

-- Дюна
INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Дюна'),
    (SELECT id FROM genres WHERE name = 'Adventure')
);
INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Дюна'),
    (SELECT id FROM genres WHERE name = 'Drama')
);
INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Дюна'),
    (SELECT id FROM genres WHERE name = 'Sci-Fi')
);

-- Человек-паук
INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Человек-паук: Нет пути домой'),
    (SELECT id FROM genres WHERE name = 'Action')
);
INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Человек-паук: Нет пути домой'),
    (SELECT id FROM genres WHERE name = 'Adventure')
);
INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Человек-паук: Нет пути домой'),
    (SELECT id FROM genres WHERE name = 'Sci-Fi')
);
 
-- Отзыв на "Матрица: Воскрешение"
INSERT INTO ratings (user_id, movie_id, rating, timestamp)
VALUES (
    (SELECT id FROM users WHERE email = 'alexeysh945@gmail.com'),
    (SELECT id FROM movies WHERE title = 'Матрица: Воскрешение'),
    4.0,
    CURRENT_TIMESTAMP
);

-- Отзыв на "Дюна"
INSERT INTO ratings (user_id, movie_id, rating, timestamp)
VALUES (
    (SELECT id FROM users WHERE email = 'alexeysh945@gmail.com'),
    (SELECT id FROM movies WHERE title = 'Дюна'),
    4.5,
    CURRENT_TIMESTAMP
);

-- Отзыв на "Человек-паук: Нет пути домой"
INSERT INTO ratings (user_id, movie_id, rating, timestamp)
VALUES (
    (SELECT id FROM users WHERE email = 'alexeysh945@gmail.com'),
    (SELECT id FROM movies WHERE title = 'Человек-паук: Нет пути домой'),
    5.0,
    CURRENT_TIMESTAMP
);

COMMIT;
