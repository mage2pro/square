The module integrates a Magento 2 based webstore with the **[Square](https://squareup.com/developers)** payment service.  
[The supported merchant countries](https://mage2.pro/t/4634): USA, Canada, Japan, Australia, and the United Kingdom.  
The module is **free** and **open source**.

## Screenshots
![](https://mage2.pro/uploads/default/original/2X/3/3ea222a91c14531e6cbe877fdacaf53534e6b648.png)
### Backend settings
![](https://mage2.pro/uploads/default/original/2X/0/0b3b91ce787cf8b0cac7545e57b7b5e1fd680ea4.png)

## How to install
[Hire me in Upwork](https://www.upwork.com/fl/mage2pro), and I will: 
- install and configure the module properly on your website
- answer your questions
- solve compatiblity problems with third-party checkout, shipping, marketing modules
- implement new features you need 

### 2. Self-installation
```
bin/magento maintenance:enable
rm -f composer.lock
composer clear-cache
composer require mage2pro/square:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/code
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy -f en_US <additional locales, e.g.: en_CA>
bin/magento maintenance:disable
```

## How to update
```
bin/magento maintenance:enable
composer remove mage2pro/square
rm -f composer.lock
composer clear-cache
composer require mage2pro/square:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/code
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy -f en_US <additional locales, e.g.: en_CA>
bin/magento maintenance:disable
```

