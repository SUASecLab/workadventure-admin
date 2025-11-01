FOSS Reimplementation of the WorkAdventure Administration Services
========================================================

This is an open source reimplementation of (most of) the proprietary WorkAdventure Admin Services.

Description
----------------

The software package distributed here is an open-source reimplementation of the proprietary administration services of WorkAdventure. It contains almost all features of the proprietary admin services (it is not possible to enforce a maximum number of people on a map or to report other users. Also you can only set up one world and you can not add custom textures). 
Furthermore, a web UI is provided which makes it easier to  modify and add data about users, maps and textures.

The administration API calls are provided and answered in the code within the `src/api`directory. The code for the web UI relies  mainly in `src/js/snippets` and `src/snippets` directories. 

Prerequisites
-------------
In order to use this project, you must have administration rights to a self-hosted WorkAdventure instance (community version). We use Docker to provide the services (see setup below). Furthermore, we assume that you use Traefik as reverse proxy. If you do not use Traefik, you have to drop the containers' labels and provide the routing information according to the software you use.


Setup with Docker
-------------------------

In order to run the administration services, four Docker containers have to be created:
1. A database service (mongodb) to store user data persistently.
2. A PHP container for running the PHP code
3. A  composer container to downloading the PHP and web UI dependencies.
4. An HTTP server for hosting the files for the API and the web UI.

We use docker compose to deploy the containers. Please note that our template file contains compose v2 syntax and won't work with compose v1 scripts. In the `docker-compose.template.yaml` file you can find a minimum working example for a base compose configuration you can include into your setup. In the `.env.example` file you can find the additional environment variables which have to be declared to run the admin services. Afterwards, add the containers which are being exposed as dependencies to your Traefik container.

Usage
-----------------------

First of all, the containers have to be set up. Then, start them and navigate to the URL under which the code is hosted (`https://YOUR-DOMAIN/admin/` if you did not alter the labels). If you log in the first time to the web UI, a user account is created with the provided credentials. Currently it is not possible to create multiple user accounts. The account created first becomes the global admin of the WorkAdventure instance.
Within the web UI, you can set up maps, users and textures.

Sidecar
------------------
Within the source code, you will find references to a sidecar service.
This service is also available in this organisation.
We developed it to hand out JWT to authenticate users on other services by us we embed into WorkAdventure as CoWebsites.
However, the sidecar itself is not required to run the administration services.

License
-----------
This software is distributed under same license as WorkAdventure ([see here](https://workadventu.re/faq/license "see here")).

Currently, the targeted version of WorkAdventure is 1.26. The contents of this repository are used in production with WorkAdventure 1.26.16.