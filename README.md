# NailBook
BookNail API

# TODO


# Ver 6.0
1. added new table BookCancel
2. Added new Foreign key added to table
3. procdure BookDelete was updated!
4. add new settings SET @p0='TIME_INTERVAL_CALENDAR'; SET @p1='30'; CALL `SettingSet`(@p0, @p1);


# Ver 6.1 
1. Adding Colums for Customer tabel with name OTP
-- ALTER TABLE `Customers` ADD `OTP` INT(11) NULL AFTER `Notes`; 

# Ver 6.1.1 - adding view for mulitaple books
2. Get Customer by phone number only!! ID of customer shuold be hiddeing
3. check the permmision of token form api
4. genetare a new OTP after sign in.
5. change the verfiy otp to phone number and not CUstomer id
6. change the generate otp to phone number and not customer id