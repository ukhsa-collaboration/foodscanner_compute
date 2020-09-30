# PHE Swaps Compute Engine

A service that will pre-calculate swaps for all the products PHE have, and stick the results into a MySQL table to act like a cache.
This service needs to run on an instance with as many CPU's as posssible in order to calculate the result's as fast as possible.
It should automatically shut down as soon as the results have been calculated.

## Expected Environment Variables

This is the full list of environment variables that need to be passed to the docker container for it to run:

The first set of environment variables we need is the connection details for the ETL database.

* `ETL_DB_HOST` - The hostname or IP address of the ETL database e.g. `etl-rds.mydomain.com`
* `ETL_DB_USER` - The user to connect to the database with. e.g. `read-only-user`
* `ETL_DB_PASSWORD` - The password to connect to the database with: e.g. `myStrongRandomPassword`
* `ETL_DB_NAME` - The name of the ETL database. e.g. `food_consolidated`
* `ETL_DB_PORT` - The port to connect with: e.g. 3306 (default mysql port)
* `ETL_TABLE_NAME` - The name of the ETL database table to build swaps based off. E.g. `food_consolidated`


The second set of environment variables we need are the connection details for the swaps database where we will put
the cached results.

* `SWAPS_CACHE_DB_HOST` - The hostname or IP address of the database e.g. `swaps-rds.mydomain.com`
* `SWAPS_CACHE_DB_USER` - The username to connect to the database with (should have full access). e.g. `user123`
* `SWAPS_CACHE_DB_PASSWORD` - The password to connect to the database with: e.g. `myStrongRandomPassword`
* `SWAPS_CACHE_DB_NAME` -  The name of the database that will hold the swaps cache. E.g. `swaps`
* `SWAPS_CACHE_DB_PORT` - The port to connect with: e.g. 3306 (default mysql port)


## Example Build and Deployment Commands

When you have checked out the codebase, you should be able to build with:
```bash
docker build . --tag swaps-compute-engine
```

If running manually, here is an example deployment command:

```bash
docker run -it \
    --restart=no \
    -e ETL_DB_HOST=etl-database.mydomain.com \
    -e ETL_DB_USER=foodUser \
    -e ETL_DB_PASSWORD=myStrongPassword \
    -e ETL_DB_NAME=food_consolidated \
    -e ETL_DB_PORT=3306 \
    -e ETL_TABLE_NAME=food_consolidated \
    -e SWAPS_CACHE_DB_HOST="swaps-database.mydomain.com" \
    -e SWAPS_CACHE_DB_USER=swapsUser \
    -e SWAPS_CACHE_DB_PASSWORD="myOtherAmazingPassword" \
    -e SWAPS_CACHE_DB_NAME="swaps" \
    -e SWAPS_CACHE_DB_PORT=3306 \
    swaps-compute-engine
```

Alternatively, you can put the environment variables into an env file, and specify it with `--env-file`.


### Crontab
To achieve the desired effect of starting up, running the calculations, and shutting down, install the cros.conf file
on the server hosting the docker container.

