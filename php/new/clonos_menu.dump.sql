----
-- phpLiteAdmin database dump (https://www.phpliteadmin.org/)
-- phpLiteAdmin version: 1.9.9-dev
-- Exported: 1:33am on December 31, 2025 (MSK)
-- database file: /var/db/clonos/clonos.sqlite
----
BEGIN TRANSACTION;

----
-- Table structure for menu
----
CREATE TABLE 'menu' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'parent_id' INTEGER NOT NULL DEFAULT 1 , 'section' INTEGER, 'link' TEXT, 'name' TEXT, 'title' TEXT, 'icon' TEXT, 'sort_num' NUMERIC(5,2), 'modify'  DATETIME NOT NULL DEFAULT (CURRENT_TIMESTAMP)  , 'visible'  BOOLEAN DEFAULT (true)  , 'devmode'  BOOLEAN DEFAULT (false)  );

----
-- Data dump for menu, a total of 18 rows
----
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('1','0','1','clonos','ClonOS section','ClonOS menu section','icon-clonos','1','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('2','0','2','nas','NAS Section','NAS menu section','icon-nas','2','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('3','1','1','overview','Overview','Summary Overview','icon-chart-bar','100','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('4','1','1','containers','Сontainers','Jails containers control panel','icon-server','105','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('5','1','1','instance_jail','Template for jail','Helpers and wizard for containers','icon-cubes','110','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('6','1','1','vms','Virtual Machines','Virtual machine control panel','icon-th-list','115','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('7','1','1','vm_packages','VM Packages','Manage VM Packages group','icon-cubes','120','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('8','1','1','vpnet','Virtual Private Network','Manage for virtual private networks','icon-plug','125','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('9','1','1','authkey','Authkeys','Manage for SSH auth key','icon-key','130','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('10','1','1','media','Storage Media','Virtual Media Manager','icon-inbox','135','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('11','1','1','imported','Imported images','Imported images','icon-upload','140','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('12','1','1','repo','Repository','Remote repository','icon-globe','145','2025-11-19 21:18:02','0','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('13','1','1','bases','FreeBSD Bases','FreeBSD bases manager','icon-database','150','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('14','1','1','sources','FreeBSD Sources','FreeBSD sources manager','icon-edit','155','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('15','1','1','tasklog','TaskLog','System task log','icon-list-alt','160','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('16','1','1','sqlite','SQLite admin','SQLite admin interface','icon-wpforms','165','2025-11-19 21:18:02','1','1');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('17','2','2','disks','Disks list','disks list','icon-default','105','2025-11-19 21:18:02','1','0');
INSERT INTO "menu" ("id","parent_id","section","link","name","title","icon","sort_num","modify","visible","devmode") VALUES ('18','2','2','raids','RAID Array','RAID Array','icon-default','110','2025-11-30 20:56:04','1','0');
COMMIT;
