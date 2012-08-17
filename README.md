RESTful API for Directory Application
===============

### Setup ###
If you want to setup the API on your server, you should follow then next steps:
- Set your site to point to /www/index.php
- Make sure that you have rights to write in the *uploads* folder because otherwise screenshot uploads will not work
- Create a database and then execute the code from *database/ws.sql*
- Create a *config.json* file in the *config* folder or edit the existing *confid.dist.json*
- Set the database fields: **db_host**, **db_user**, **db_pass** and **db_name**
- Set the app fields: **app_timezone**, **app_token**

### Documentation ###
The documentation of the API can be found at: http://ws-api.cloudaccess.net/ws/www/docs