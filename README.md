# MRPacket Connect

This is a PHP class library for connecting to the MRPacket API.

MRPacket helps you to optimizes the shipping process of packages and making it efficient.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

You will need a webserver and a PHP Interpreter to run the code. 

System requirements: 
- PHP 7.3 or higher
- cURL library enabled

You can run the provided script *"check_requirements.php"* to find out if your infrastructure is matching the conditions above.

### Installing

This piece of code is intended for usage with existing Webshops based on PHP such as WooCommerce or Magento.

Download the code and include **autoload.php** to have the PHP classes available for integration in your PHP project. All relevant files are within the namespace "MRPacket".

There is a central configuration file in path *"/config/settings.php"* that rules which URLs are to be used for communication with MRPacket.

In order to start developing you will need to register an user account for the Staging version of the MRPacket API first.
Please drop us an email (info@mrpacket.de) to find out more.

### Deployment

Please contact info@mrpacket.de to contact our sales department in order to receive the support that you need to assist you in development and with the Go-Live of your plugin!

We have to insist that you use the predefined Staging URL of the MRPacket API preset in *"/config/settings.php"* for development and testing before switching to the live URL.

## License

Please refer to the file LICENSE.txt for details.
