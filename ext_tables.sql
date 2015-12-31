#
# Table structure for table 'tx_votable_domain_model_voting'
#
CREATE TABLE tx_votable_domain_model_voting (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	allowed_frequency int(11) DEFAULT '0' NOT NULL,
	closing_date datetime DEFAULT '0000-00-00 00:00:00',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for table 'tx_votable_domain_model_vote'
#
CREATE TABLE tx_votable_domain_model_vote (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	item int(11) DEFAULT '0' NOT NULL,
	voting int(11) DEFAULT '0' NOT NULL,
	user int(11) DEFAULT '0' NOT NULL,
	value int(11) DEFAULT '0' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,
	time datetime DEFAULT '0000-00-00 00:00:00',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for table 'tx_votable_vote_record_mm'
#
CREATE TABLE tx_votable_vote_record_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(255) DEFAULT '' NOT NULL,
	fieldname varchar(255) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	sorting_foreign int(11) DEFAULT '0' NOT NULL,

	KEY uid_local_foreign (uid_local,uid_foreign),
	KEY uid_foreign_tablenames (uid_foreign,tablenames)
);
