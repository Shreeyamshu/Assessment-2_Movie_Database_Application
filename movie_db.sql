CREATE DATABASE IF NOT EXISTS NP03CS4A240052;
USE NP03CS4A240052;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

);

CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    release_year INT NOT NULL,
    rating DECIMAL(3, 1) NOT NULL,
    description TEXT,
    poster_url VARCHAR(2048),
    wrapper_image_url VARCHAR(2048),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

);

CREATE TABLE IF NOT EXISTS genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

);

CREATE TABLE IF NOT EXISTS movie_genres (
    movie_id INT,
    genre_id INT,
    PRIMARY KEY (movie_id, genre_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
);

);

CREATE TABLE IF NOT EXISTS cast_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

);

CREATE TABLE IF NOT EXISTS movie_cast (
    movie_id INT,
    cast_id INT,
    role VARCHAR(100),
    PRIMARY KEY (movie_id, cast_id, role),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (cast_id) REFERENCES cast_members(id) ON DELETE CASCADE
);

);

INSERT INTO users (username, password_hash, role) VALUES 
('admin', '$2y$10$Zl3ARuYsflIGt6i6.3iqL.L6VGFLHrFI.g37z2Ye0CdjbOTyft9lm', 'admin');

('admin', '$2y$10$Zl3ARuYsflIGt6i6.3iqL.L6VGFLHrFI.g37z2Ye0CdjbOTyft9lm', 'admin');

INSERT INTO genres (name) VALUES 
('Action'), ('Sci-Fi'), ('Drama'), ('Comedy'), ('Horror'), ('Romance'), ('Thriller'), ('Animation'), ('Fantasy');

('Action'), ('Sci-Fi'), ('Drama'), ('Comedy'), ('Horror'), ('Romance'), ('Thriller'), ('Animation'), ('Fantasy');

INSERT INTO movies (title, release_year, rating, description, poster_url) VALUES 
('Inception', 2010, 8.8, 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.', 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg'),
('The Dark Knight', 2008, 9.0, 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.', 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg'),
('Interstellar', 2014, 8.6, 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.', 'https://upload.wikimedia.org/wikipedia/en/b/bc/Interstellar_film_poster.jpg'),
('Spider-Man: No Way Home', 2021, 8.2, 'With Spider-Man\'s identity now revealed, Peter asks Doctor Strange for help. When a spell goes wrong, dangerous foes from other worlds start to appear.', 'https://upload.wikimedia.org/wikipedia/en/0/00/Spider-Man_No_Way_Home_poster.jpg'),
('It', 2017, 7.3, 'In the summer of 1989, a group of bullied kids band together to destroy a shape-shifting monster, which disguises itself as a clown and preys on the children of Derry.', 'https://upload.wikimedia.org/wikipedia/en/5/5a/It_(2017)_poster.jpg'),
('Ra.One', 2011, 4.8, 'A video game designer creates a game in which the antagonist is more powerful than the protagonist, but things go wrong when the villain escapes the virtual world.', 'https://upload.wikimedia.org/wikipedia/en/2/2b/Ra.One_Poster.jpg'),
('Kuch Kuch Hota Hai', 1998, 7.5, 'During their college years, Anjali falls in love with her best-friend Rahul, but he has eyes only for Tina. Years later, Rahul\'s daughter attempts to reunite her father and Anjali.', 'https://upload.wikimedia.org/wikipedia/en/0/07/Kuch_Kuch_Hota_Hai_poster.jpg');

('Kuch Kuch Hota Hai', 1998, 7.5, 'During their college years, Anjali falls in love with her best-friend Rahul, but he has eyes only for Tina. Years later, Rahul\'s daughter attempts to reunite her father and Anjali.', 'https://upload.wikimedia.org/wikipedia/en/0/07/Kuch_Kuch_Hota_Hai_poster.jpg');

INSERT INTO movie_genres (movie_id, genre_id) VALUES 
(1, 1), (1, 2), -- Inception
(2, 1), (2, 3), -- Dark Knight
(3, 2), (3, 3), -- Interstellar
(4, 1), (4, 2), -- Spider-Man
(5, 5), (5, 7), -- It
(6, 1), (6, 2), -- Ra.One
(7, 6), (7, 3), (7, 4); -- Kuch Kuch Hota Hai

(7, 6), (7, 3), (7, 4); -- Kuch Kuch Hota Hai

INSERT INTO cast_members (name) VALUES 
('Leonardo DiCaprio'), ('Joseph Gordon-Levitt'), 
('Christian Bale'), ('Heath Ledger'), 
('Matthew McConaughey'), ('Anne Hathaway');

('Matthew McConaughey'), ('Anne Hathaway');

INSERT INTO movie_cast (movie_id, cast_id, role) VALUES 
(1, 1, 'Cobb'), (1, 2, 'Arthur'),
(2, 3, 'Bruce Wayne'), (2, 4, 'Joker'),
(3, 5, 'Cooper'), (3, 6, 'Brand');
