DROP TABLE IF EXISTS product;
CREATE TABLE product
(
    id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uid   VARCHAR(255) NOT NULL UNIQUE KEY,
    name  VARCHAR(255) NOT NULL,
    price INT UNSIGNED NOT NULL COMMENT 'в тысячах рублей'
);

DROP TABLE IF EXISTS customer;
CREATE TABLE customer
(
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(255) NOT NULL UNIQUE KEY,
    address      VARCHAR(2000) DEFAULT NULL,
    phone_number VARCHAR(255)  DEFAULT NULL
);

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order`
(
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id    INT UNSIGNED  NOT NULL REFERENCES customer (id),
    product_id     INT UNSIGNED  NOT NULL REFERENCES product (id),
    address        VARCHAR(2000) NOT NULL,
    date           DATE         DEFAULT NULL,
    agreement_code VARCHAR(255) DEFAULT NULL,
    agreement_url  VARCHAR(255) DEFAULT NULL
);

DROP TABLE IF EXISTS transportation;
CREATE TABLE transportation
(
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id     INT UNSIGNED                 NOT NULL REFERENCES `order` (id) ON DELETE CASCADE,
    planned_date DATE                         NOT NULL,
    real_date    DATE DEFAULT NULL,
    number       INT UNSIGNED                 NOT NULL,
    status       ENUM ('planned', 'finished') NOT NULL
);


DROP TABLE IF EXISTS user;
CREATE TABLE user
(
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role     ENUM ('operator', 'director') NOT NULL,
    login    VARCHAR(255) DEFAULT NULL UNIQUE KEY,
    password VARCHAR(255) DEFAULT NULL
);
