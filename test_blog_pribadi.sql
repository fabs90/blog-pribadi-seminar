use blog_pribadi;

create table posts(
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    title varchar(100) NOT NULL,
    content TEXT NOT NULL,
    created_at date,
    slug varchar(100) NOT NULL
    );
    
create table categories(
id int(11) AUTO_INCREMENT PRIMARY KEY,
   name varchar(50),
   description varchar(100)
);

create table posts_categories(
id int(11),
 post_id int(11) NOT NULL,
 category_id int(11) NOT NULL,
 FOREIGN KEY (post_id) REFERENCES posts(id),
 FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE users (
    id int(2),
    user_id int(11) PRIMARY KEY,
    username varchar(100) NOT NULL,
    password varchar(255) NOT NULL,
    email varchar(255) NOT NULL
);