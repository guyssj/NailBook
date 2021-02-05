# NailBook
BookNail API

Updated 
php composer.phar dumpautoload -o

# TODO
1. Add to Users table colums RegId
ALTER TABLE `Users` ADD `RegId` VARCHAR(1000) NOT NULL AFTER `UserName`; 
ALTER TABLE `Users` CHANGE `RegId` `RegId` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL; 

2. add DAL for adding / update Reg Id by UserName
3. get from token the username