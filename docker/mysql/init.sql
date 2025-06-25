CREATE USER 'blog_user'@'%' IDENTIFIED BY 'blog_user_password';
GRANT ALL PRIVILEGES ON blog_db.* TO 'blog_user'@'%';
GRANT ALL PRIVILEGES ON blog_testing_db.* TO 'blog_user'@'%';

CREATE DATABASE IF NOT EXISTS blog_db;
CREATE DATABASE IF NOT EXISTS blog_testing_db;

FLUSH PRIVILEGES;