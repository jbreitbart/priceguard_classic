CREATE TABLE mos_user (
  id INT(11)) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY(id)
)
AUTO_INCREMENT = 0;

CREATE TABLE amazon_product (
  asin VARCHAR(10) NOT NULL,
  amazon_price INT NULL,
  3rdparty_new_price INT NULL,
  3rdparty_used_price INT NULL,
  amazon_availabillity TINYTEXT NULL,
  image_small TINYTEXT NULL,
  PRIMARY KEY(asin)
);

CREATE TABLE priceguard_categories (
  id INTEGER NOT NULL AUTO_INCREMENT,
  mos_user_id INT(11) NOT NULL,
  parent INTEGER DEFAULT NULL,
  name TINYTEXT NOT NULL,
  PRIMARY KEY(id),
  INDEX categories_FKIndex1(mos_user_id),
  FOREIGN KEY(mos_user_id)
    REFERENCES mos_user(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
);

CREATE TABLE amazon_product_old__temp (
  amazon_product_asin VARCHAR(10) NOT NULL,
  amazon_price_old INT NULL,
  3rdparty_new_price_old INT NULL,
  3rdparty_used_price_old INT NULL,
  amazon_availabillity_old TINYTEXT NULL,
  PRIMARY KEY(amazon_product_asin),
  INDEX amazon_product_old__temp_FKIndex1(amazon_product_asin),
  FOREIGN KEY(amazon_product_asin)
    REFERENCES amazon_product(asin)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE priceguard_product (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  priceguard_categories_id INTEGER UNSIGNED NOT NULL,
  mos_user_id INT(11) UNSIGNED NOT NULL,
  amazon_product_asin VARCHAR(10) NULL,
  name TINYTEXT NOT NULL,
  guard_availabillity BOOL NOT NULL DEFAULT 'false',
  deadline INTEGER UNSIGNED NULL DEFAULT NULL,
  remind_price_amazon INT NULL DEFAULT NULL,
  remind_price_amazon_3rdparty_new INT NULL DEFAULT NULL,
  remind_price_amazon_3rdparty_used INT NULL DEFAULT NULL,
  PRIMARY KEY(id),
  INDEX product_FKIndex1(priceguard_categories_id),
  INDEX priceguard_product_FKIndex2(amazon_product_asin),
  INDEX priceguard_product_FKIndex3(mos_user_id),
  FOREIGN KEY(priceguard_categories_id)
    REFERENCES priceguard_categories(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  FOREIGN KEY(amazon_product_asin)
    REFERENCES amazon_product(asin)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  FOREIGN KEY(mos_user_id)
    REFERENCES mos_user(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
);

