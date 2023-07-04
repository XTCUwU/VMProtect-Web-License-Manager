<?php

if (!defined("INSTALLATION"))
    die("Direct access to this location is not allowed");
    
mysqli_query($mysqli_link, "DROP TABLE IF EXISTS {$DB_PREFIX}licenses");
mysqli_query($mysqli_link, "DROP TABLE IF EXISTS {$DB_PREFIX}activations");
mysqli_query($mysqli_link, "DROP TABLE IF EXISTS {$DB_PREFIX}products");
mysqli_query($mysqli_link, "DROP TABLE IF EXISTS {$DB_PREFIX}users");
mysqli_query($mysqli_link, "DROP TABLE IF EXISTS {$DB_PREFIX}registrators");
mysqli_query($mysqli_link, "DROP TABLE IF EXISTS {$DB_PREFIX}hwdata");
mysqli_query($mysqli_link, "DROP VIEW IF EXISTS {$DB_PREFIX}vw_products");

mysqli_query($mysqli_link, "CREATE TABLE {$DB_PREFIX}products (
    id int primary key auto_increment,
    parentid int DEFAULT NULL,
    name varchar(255) not null,
    active bool not null default true,
    algorithm varchar(8) not null default 'RSA',
    bits int not null default 2048,
    product_code varchar(16) DEFAULT NULL,
    public_exp varchar(16) DEFAULT NULL,
    private_exp text DEFAULT NULL,
    modulus text DEFAULT NULL,
    uses_act bool not null default false,
    snattrs text DEFAULT NULL,
    act_pattern varchar(32) DEFAULT '####-####-####',
    act_extracount int DEFAULT 0,
    CONSTRAINT {$DB_PREFIX}pro_pro FOREIGN KEY (parentid) REFERENCES {$DB_PREFIX}products (id) ON DELETE CASCADE
) ENGINE=INNODB CHARACTER SET = utf8 COLLATE = utf8_bin") or die(mysqli_error($mysqli_link));

mysqli_query($mysqli_link, "CREATE VIEW {$DB_PREFIX}vw_products AS
SELECT
	p1.id,
	p1.parentid,
	p1.name,
	p1.snattrs,
	p1.active,
	IF( p1.parentid IS NULL , p1.algorithm, p2.algorithm ) AS algorithm,
	IF( p1.parentid IS NULL , p1.bits, p2.bits ) AS bits,
	IF( p1.parentid IS NULL , p1.uses_act, p2.uses_act ) AS uses_act,
	IF( p1.parentid IS NULL , p1.act_pattern, p2.act_pattern ) AS act_pattern,
	IF( p1.parentid IS NULL , p1.act_extracount, p2.act_extracount ) AS act_extracount,
	IF( p1.parentid IS NULL , p1.name, CONCAT( p2.name, ' - ', p1.name ) ) AS fullname,
	IF( p1.parentid IS NULL , p1.product_code, p2.product_code ) AS product_code,
	IF( p1.parentid IS NULL , p1.public_exp, p2.public_exp ) AS public_exp,
	IF( p1.parentid IS NULL , p1.private_exp, p2.private_exp ) AS private_exp,
	IF( p1.parentid IS NULL , p1.modulus, p2.modulus ) AS modulus
FROM {$DB_PREFIX}products p1 LEFT OUTER JOIN {$DB_PREFIX}products p2 ON ( p1.parentid = p2.id )") or die(mysqli_error($mysqli_link));

mysqli_query($mysqli_link, "CREATE TABLE {$DB_PREFIX}activations (
    id int primary key auto_increment,
    productid int,
    name varchar(64),
    email varchar(64),
    orderref varchar(64),
    code varchar(32),
    act_count int DEFAULT NULL,
    expiredate date null,
    blocked bool not null default false,
    createdate date,
    UNIQUE code (code),
    CONSTRAINT {$DB_PREFIX}act_pro FOREIGN KEY (productid) REFERENCES {$DB_PREFIX}products (id) ON DELETE CASCADE
) ENGINE=INNODB CHARACTER SET = utf8 COLLATE = utf8_bin") or die(mysqli_error($mysqli_link));

mysqli_query($mysqli_link, "CREATE TABLE {$DB_PREFIX}licenses (
    id int primary key auto_increment,
    productid int,
    activationid int default null,
    name varchar(64),
    email varchar(64),
    createdate date,
    orderref varchar(64),
    comments text,
    sn text,
    snhash varchar(32),
    blocked bool not null default false,
    INDEX snhash (snhash),
    CONSTRAINT {$DB_PREFIX}lic_pro FOREIGN KEY (productid) REFERENCES {$DB_PREFIX}products (id) ON DELETE CASCADE,
    CONSTRAINT {$DB_PREFIX}lic_act FOREIGN KEY (activationid) REFERENCES {$DB_PREFIX}activations (id) ON DELETE CASCADE
) ENGINE=INNODB CHARACTER SET = utf8 COLLATE = utf8_bin") or die(mysqli_error($mysqli_link));

mysqli_query($mysqli_link, "CREATE TABLE {$DB_PREFIX}registrators (
    id int primary key auto_increment,
    name varchar(255) not null,
    authmode int default 0,
    ipranges varchar(255),
    login varchar(64),
    password varchar(64),
    active bool not null default true
) ENGINE=INNODB CHARACTER SET = utf8 COLLATE = utf8_bin") or die(mysqli_error($mysqli_link));

mysqli_query($mysqli_link, "CREATE TABLE {$DB_PREFIX}users (
    id int primary key auto_increment,
    login varchar(64) not null,
    password varchar(64) not null,
    email varchar(64) not null,
    isadmin bool not null,
    failures int,
    UNIQUE login (login),
    UNIQUE email (email)
) ENGINE=INNODB CHARACTER SET = utf8 COLLATE = utf8_bin") or die(mysqli_error($mysqli_link));

mysqli_query($mysqli_link, "CREATE TABLE {$DB_PREFIX}hwdata (
    id int primary key auto_increment,
    licenseid int,
    type int,
    value int unsigned,
    CONSTRAINT {$DB_PREFIX}hw_lic FOREIGN KEY (licenseid) REFERENCES {$DB_PREFIX}licenses (id) ON DELETE CASCADE
) ENGINE=INNODB CHARACTER SET = utf8 COLLATE = utf8_bin") or die(mysqli_error($mysqli_link));

?>