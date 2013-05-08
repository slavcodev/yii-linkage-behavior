BEGIN;

------------------------------------------------------

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL
);

------------------------------------------------------

DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE IF NOT EXISTS `user_profile` (
	`user_id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`name` VARCHAR(255) NOT NULL
);

CREATE INDEX user_profile_idx ON user_profile (user_id);

------------------------------------------------------

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`user_id` INTEGER NOT NULL
);

CREATE INDEX post_user_idx ON post (user_id);

------------------------------------------------------

DROP TABLE IF EXISTS `post_comment`;
CREATE TABLE `post_comment` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`text` TEXT NOT NULL,
	`post_id` INTEGER NOT NULL
);

CREATE INDEX post_comment_idx ON post_comment (post_id);

------------------------------------------------------

DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`name` VARCHAR(50) NOT NULL
);

DROP TABLE IF EXISTS `post_tag`;
CREATE TABLE `post_tag` (
	`post_id` INTEGER NOT NULL,
	`tag_id` INTEGER NOT NULL
);

CREATE INDEX post_tag_idx ON post_tag (post_id, tag_id);

------------------------------------------------------

COMMIT;