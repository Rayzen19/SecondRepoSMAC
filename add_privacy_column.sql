-- SQL to add the allow_guardian_access column to students table
-- Run this in phpMyAdmin or MySQL command line if migration fails

ALTER TABLE `students` 
ADD COLUMN `allow_guardian_access` TINYINT(1) NOT NULL DEFAULT 1 
AFTER `profile_picture`;
