DROP TABLE IF EXISTS product;
CREATE TABLE product
(
    id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uid   VARCHAR(255) NOT NULL UNIQUE KEY,
    name  VARCHAR(255) NOT NULL,
    price INT UNSIGNED NOT NULL COMMENT 'в копейках',
    count INT UNSIGNED NOT NULL
);

DROP TABLE IF EXISTS user;
CREATE TABLE user
(
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role          ENUM('customer', 'operator', 'director'),
    login         VARCHAR(255) DEFAULT NULL UNIQUE KEY,
    email         VARCHAR(255) DEFAULT NULL UNIQUE KEY,
    phone_number  VARCHAR(255) DEFAULT NULL,
    password_hash VARCHAR(255) DEFAULT NULL,
    name          VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order`
(
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED REFERENCES user (id),
    address TEXT,
    status  ENUM( 'created',
        'fixed',
        'transportation_offered',
        'transportation_approved',
        'agreement_loaded',
        'agreement_rejected',
        'agreement_confirmed',
        'paid',
        'completed'),
    price   INT UNSIGNED COMMENT 'в копейках'
);

DROP TABLE IF EXISTS agreement_template;
CREATE TABLE agreement_template
(
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED REFERENCES `order`(id),
    status   ENUM('enabled', 'disabled'),
    hash     VARCHAR(255),
    path     VARCHAR(4096)
);

DROP TABLE IF EXISTS agreement;
CREATE TABLE agreement
(
    id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id              INT UNSIGNED REFERENCES `order`(id),
    agreement_template_id INT UNSIGNED REFERENCES agreement_template(id),
    status                ENUM( 'loaded',
        'rejected',
        'removed',
        'confirmed'),
    hash                  VARCHAR(255),
    path                  VARCHAR(4096)
);

DROP TABLE IF EXISTS order_product;
CREATE TABLE order_product
(
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED,
    order_id   INT UNSIGNED,
    count      INT UNSIGNED
);

DROP TABLE IF EXISTS transportation;
CREATE TABLE transportation
(
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED REFERENCES `order`(id),
    date     DATE,
    address  TEXT,
    status   VARCHAR(255)
);

DROP TABLE IF EXISTS change_password_token;
CREATE TABLE change_password_token
(
    id       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id  INT UNSIGNED NOT NULL UNIQUE REFERENCES user (id) ON DELETE CASCADE,
    token    VARCHAR(255) NOT NULL,
    creation TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS device;
CREATE TABLE device
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED REFERENCES user (id),
    comment     VARCHAR(255),
    creation    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_access TIMESTAMP,
    invalidated BOOLEAN   DEFAULT FALSE
);