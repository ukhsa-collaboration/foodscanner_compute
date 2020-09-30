from sqlalchemy import create_engine
from pprint import pprint
import pymysql
import json
import pandas
import sys
from phe_recommender.recommender import top_3_swaps, long_list_swaps, eligible_swaps, update_stopwords

update_stopwords()
