-- add field for 2 letter lang code by 3 letter languages codes 

ALTER TABLE `languages` ADD `lang_code2` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL AFTER `lng_coding`;

-- add field for setting show images

ALTER TABLE `versions` ADD `show_images` INT(1) NULL DEFAULT NULL AFTER `license`;


db_umsetz_offset.php
    foreach($versions_all as $vs_id => $vs_a_name) 
    { echo "ALTER TABLE for Version ".$vs_id.":".$vs_a_name;
      db_query("ALTER TABLE images_$vs_id  add   column `offset_x` int(11) NOT NULL default '0';");
      db_query("ALTER TABLE images_$vs_id  add   column `offset_y` int(11) NOT NULL default '0';");
}

ALTER TABLE `objects` ADD KEY (`obj`);

ALTER TABLE `objects` ADD `subversion` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '' after `version_version_id`;
ALTER TABLE `objects` ADD `type`       varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '' after `obj`;
ALTER TABLE `objects` ADD KEY (`subversion`);
ALTER TABLE `objects` ADD KEY (`type`);
ALTER TABLE `objects` drop column  `img_state`;
ALTER TABLE `objects` drop column  `img_dsc`;

db_umsetz_offset.php
    foreach($versions_all as $vs_id => $vs_a_name) 
    { echo "ALTER TABLE for Version ".$vs_id.":".$vs_a_name;
      db_query("ALTER TABLE images_$vs_id  drop   column `save_to` );
}

ALTER TABLE `versions` ADD `maintainer_user_id4`       varchar(20) COLLATE utf8_bin NULL DEFAULT NULL after `maintainer_user_id3`;
ALTER TABLE `versions` ADD `maintainer_user_id5`       varchar(20) COLLATE utf8_bin NULL DEFAULT NULL after `maintainer_user_id4`;
ALTER TABLE `versions` ADD `maintainer_user_id6`       varchar(20) COLLATE utf8_bin NULL DEFAULT NULL after `maintainer_user_id5`;
ALTER TABLE `versions` modify `maintainer_user_id`       varchar(20) COLLATE utf8_bin NULL DEFAULT NULL;
ALTER TABLE `versions` modify `lng_disabled`       varchar(100) COLLATE utf8_bin NULL DEFAULT NULL;
   foreach($versions_all as $vs_id => $vs_a_name) 
    { echo "ALTER TABLE for Version ".$vs_id.":".$vs_a_name;
 
      db_query("ALTER TABLE images_$vs_id  modify  `image_data` MEDIUMBLOB NULL ;");

      
ALTER TABLE `objects` ADD `dat_file_name` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '' ;
ALTER TABLE `objects` ADD `dat_path`      varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '' ;


ALTER TABLE `users` modify `config1`       varchar(255) COLLATE utf8_bin NULL DEFAULT NULL;
