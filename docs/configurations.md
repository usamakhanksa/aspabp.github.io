## php extensions to enable

1. extension=php_sqlsrv_82_ts_x64.dll
2. extension=curl
3. extension=openssl (if you want to use the application without fatoora sdk)

## setup environment variables

1. set user path variables (to check the working condition and check things manually)
2. set system path variables (to allow the php application to run fatoora command)

### setup system environment variables

1. Add 'FATOORA_HOME' - 'C:\zatca-einvoicing-sdk\Apps'
2. Add 'SDK_CONFIG' - 'C:\zatca-einvoicing-sdk\Configuration/config.json'