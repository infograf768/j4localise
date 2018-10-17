ALTER TABLE `#__localise` MODIFY `asset_id` int(10) NOT NULL DEFAULT 0;
ALTER TABLE `#__localise` DROP KEY `idx_path`;

ALTER TABLE `#__localise` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__localise` MODIFY `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `#__localise_revised_values` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__localise` ADD KEY `idx_path` (`path`(191));