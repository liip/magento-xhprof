<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('liip_xhprof/run')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('liip_xhprof/run')}` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `path_info` varchar(255) NOT NULL DEFAULT '',
  `data` mediumblob,
  `pmu` int(11) unsigned DEFAULT NULL,
  `wt` int(11) unsigned DEFAULT NULL,
  `cpu` int(11) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `cpu` (`cpu`),
  KEY `wt` (`wt`),
  KEY `pmu` (`pmu`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
