# README #

Lof Fast Order extension allows customers to quickly order products in bulk without navigating the product pages. Simply enter the SKU, quantity and add items to cart.

### What is this repository for? ###

* Quick summary
* Version
* [Learn Markdown](https://landofcoder.com)

### How do I get set up? ###

* Search and order products using SKU from frontend
* View all details of the products being ordered
* Add multiple products to cart at one time using CSV file
* Allow selected customer groups to access order by SKU feature
* B2B customers can add products quickly by specifying the product
* Enabled Autosuggestion on SKU inputNew
* Supports all product typesNew
* Even Guest Users can use quick order featureNew

### Install Extension ###

Run magento 2 commands:

```
php bin/magento module:enable Lof_FastOrder
php bin/magento setup:upgrade --keep-generated
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
```
### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact