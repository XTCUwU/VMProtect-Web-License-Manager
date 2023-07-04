<?php

if (!defined("INSTALLATION"))
    die("Direct access to this location is not allowed");

set_time_limit(0);
ini_set("memory_limit", -1);

require_once "dbopen.inc.php";
require_once "license.inc.php";

mysqli_query($mysqli_link, "CREATE TABLE IF NOT EXISTS {$DB_PREFIX}activations (
    id int primary key auto_increment,
    productid int,
    name varchar(64),
    email varchar(64),
    orderref varchar(64),
    code varchar(32),
    act_count int DEFAULT NULL,
    blocked bool not null default false,
    createdate date,
    UNIQUE code (code),
    CONSTRAINT {$DB_PREFIX}act_pro FOREIGN KEY (productid) REFERENCES {$DB_PREFIX}products (id) ON DELETE CASCADE
) ENGINE=INNODB CHARACTER SET = utf8 COLLATE = utf8_bin") or die(mysqli_error($mysqli_link));
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}activations ADD UNIQUE code (code)");

@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}licenses ADD snhash varchar(32)");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}licenses ADD INDEX snhash (snhash)");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}licenses ADD activationid int DEFAULT NULL");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}licenses ADD CONSTRAINT {$DB_PREFIX}lic_act FOREIGN KEY (activationid) REFERENCES {$DB_PREFIX}activations (id) ON DELETE CASCADE");

/* Calculate serial number hashes */
$res = mysqli_query($mysqli_link, "SELECT id, sn FROM {$DB_PREFIX}licenses WHERE sn IS NOT NULL AND snhash IS NULL");
if ($res != FALSE && mysqli_num_rows($res) > 0)
    while (NULL != ($row = mysqli_fetch_assoc($res)))
    {
        $snhash = base64_encode(sha1(base64_decode($row["sn"]), TRUE));
        mysqli_query($mysqli_link, "UPDATE {$DB_PREFIX}licenses SET snhash='{$snhash}' WHERE id='{$row["id"]}'");
    }

/* Registrator changes */
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}registrators ADD ipranges varchar(255)");
@mysqli_query($mysqli_link, "UPDATE {$DB_PREFIX}registrators SET ipranges=startip WHERE endip IS NULL");
@mysqli_query($mysqli_link, "UPDATE {$DB_PREFIX}registrators SET ipranges=CONCAT(startip, '-', endip) WHERE endip IS NOT NULL");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}registrators DROP startip");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}registrators DROP endip");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}registrators MODIFY ipranges varchar(255) null");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}registrators ADD authmode int default 0");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}registrators ADD login varchar(64)");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}registrators ADD password varchar(64)");

/* Users changes */
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}users ADD failures int");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}users ADD UNIQUE login (login)");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}users ADD UNIQUE email (email)");

/* Product changes */
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}products ADD uses_act bool NOT NULL DEFAULT FALSE");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}products ADD parentid int DEFAULT NULL");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}products ADD snattrs text DEFAULT NULL");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}products ADD act_pattern varchar(32) DEFAULT '####-####-####'");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}products ADD act_extracount int DEFAULT 0");
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}products ADD CONSTRAINT {$DB_PREFIX}pro_pro FOREIGN KEY (parentid) REFERENCES {$DB_PREFIX}products (id) ON DELETE CASCADE");

@mysqli_query($mysqli_link, "DROP VIEW IF EXISTS {$DB_PREFIX}vw_products");
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

/* Hardware data */
$hwdata_not_exists = mysqli_num_rows(mysqli_query($mysqli_link, "SHOW TABLES LIKE '{$DB_PREFIX}hwdata'")) == 0;
if ($hwdata_not_exists)
{
    mysqli_query($mysqli_link, "CREATE TABLE IF NOT EXISTS {$DB_PREFIX}hwdata (
        id int primary key auto_increment,
        licenseid int,
        type int,
        value int unsigned,
        CONSTRAINT {$DB_PREFIX}hw_lic FOREIGN KEY (licenseid) REFERENCES {$DB_PREFIX}licenses (id) ON DELETE CASCADE
    ) ENGINE=INNODB CHARACTER SET = utf8 COLLATE = utf8_bin") or die(mysqli_error($mysqli_link));

    /* Extract hardware data from licenses */
    $lics = ObjectsSqlLoad("SELECT * FROM {$DB_PREFIX}licenses l WHERE NOT EXISTS(SELECT * FROM {$DB_PREFIX}hwdata WHERE licenseid=l.id)", "License");
    if (is_array($lics))
        foreach ($lics as $lic)
            $lic->SaveHwdata();
}

/* Expiration date for activations */
@mysqli_query($mysqli_link, "ALTER TABLE {$DB_PREFIX}activations ADD expiredate date null");

?>