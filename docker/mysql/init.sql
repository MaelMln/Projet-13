-- Grant privileges to greengoodies user from any host
GRANT ALL PRIVILEGES ON greengoodies.* TO 'greengoodies'@'%';
CREATE DATABASE IF NOT EXISTS greengoodies_test;
GRANT ALL PRIVILEGES ON greengoodies_test.* TO 'greengoodies'@'%';
FLUSH PRIVILEGES;
