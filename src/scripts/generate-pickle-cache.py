from sqlalchemy import create_engine
from pprint import pprint
import pymysql
import os
import sys
import json
import pandas
from phe_recommender.recommender import top_3_swaps, long_list_swaps, eligible_swaps

if False:
    tableName = os.getenv("ETL_TABLE_NAME")
    dbHost = os.getenv("ETL_DB_HOST")
    dbName = os.getenv("ETL_DB_NAME")
    dbUser = os.getenv("ETL_DB_USER")
    dbPassword = os.getenv("ETL_DB_PASSWORD")
    dbPort = os.getenv("ETL_DB_PORT")
    connectionString = "mysql+pymysql://" + dbUser + ":" + dbPassword + "@" + dbHost + ":" + dbPort +  "/" + dbName
    sqlEngine = create_engine(connectionString, pool_recycle=3600)
    dbConnection = sqlEngine.connect()
    query = "SELECT * FROM " + dbName + "." + tableName
    df = pandas.read_sql(query, dbConnection);
else:
    filepath = sys.argv[1]
    df = pandas.read_json(filepath)

df.to_pickle('df_cache.pkl');
