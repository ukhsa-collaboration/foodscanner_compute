from sqlalchemy import create_engine
from pprint import pprint
import pymysql
import json
import pandas
import sys
from phe_recommender.recommender import top_3_swaps, long_list_swaps, eligible_swaps


def main():
    # Get the passed in barcodes that were passed in as paramters.
    barcodes = sys.argv

    # Remove first element, as that is the name of the script, not a barcode.
    barcodes.pop(0)

    #pprint(barcodes)
    df = pandas.read_pickle('df_cache.pkl');
    results = []

    for barcode in barcodes:
        result = swapsRoutineForBarcode(barcode, df)
        results.append(result)

    jsonString = json.dumps(results);
    print(jsonString)
    sys.exit()


def swapsRoutineForBarcode(barcode, df):
    #barcode = '1000139000284'

    try:
        output = eligible_swaps(barcode, df) # returns df of 30 top swaps
        jsonObj = output.to_json(orient='records')

        #for x in range(100):
        #    output = long_list_swaps(barcode, df) # returns df of 30 top swaps
        #    pprint(output)

        jsonObj2 = json.loads('{"barcode" : ' + barcode + ', "swaps" : ' + jsonObj + '}')

    except ValueError:
        jsonObj2 = json.loads('{"barcode" : ' + barcode + ', "swaps" : [] }')
    except Exception:
        jsonObj2 = json.loads('{"barcode" : ' + barcode + ', "swaps" : [] }')

    return jsonObj2

main()