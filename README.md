# NailBook
BookNail API

Updated 
php composer.phar dumpautoload -o

# Deployment ver 4.0
1. add new cloums to customer with name `Active` and type `tinyint`
2. change customer update to updated the active clounms

# Deployment ver 3.1
1. adding new settings SET @p0='SMS_TEMPLATE_UPAPP'; SET @p1='fff'; CALL `SettingSet`(@p0, @p1); 
2. adding value to settings
UPDATE `Settings` SET `SettingValue` = 'שלום {FirstName} {LastName},\\nהפגישה לטיפול {ServiceType} אצל מיריתוש\\n עודכנה לתאריך {Date} בשעה {Time}\\nיש להגיע עם מסכה' WHERE `Settings`.`SettingName` = 'SMS_TEMPLATE_UPAPP'; 