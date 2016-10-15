*******************
Server Requirements
*******************

All requirements of CodeIgniter including a MySQL database, for details see the CodeIgniter's readme ("readme-CodeIgniter.rst"). In addition to this also gettext is used for translation, on Windows this can be enabled in the "php.ini" config file by uncommenting the line (don't forget to restart your webserver afterwards to apply the changes):

    extension=php_gettext.dll

Also writing access to "./application/sessions/" is required since by default files are used to track sessions. This can be avoided if using other ways of keeping track of sessions. See the CodeIgniter documentation on how to do this.

************
Installation
************

Copy the root entire project into your web folder. Then navigate to "./application/config/database.php" and enter all information required for the application to connect to your MySQL database.

Then also open the "config.php" file in the same folder and fill the "$config['base_url']" field with the domain name used.