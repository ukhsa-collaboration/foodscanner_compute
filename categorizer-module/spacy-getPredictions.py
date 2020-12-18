# spacy
import os

import pandas
import spacy
import json
import sys
import datetime

begin_time = datetime.datetime.now()
arguments = sys.argv[1:]

# -s json string
# -p json path
arguments = [arguments[i:i+2] for i in range(0, len(arguments), 2)]
path = 'spacy/data/trial-json-products.json' #default for testing
for argument in arguments:
    if argument[0] == "-s":
        path = argument[1]
    elif argument[0] == "-p":
        path = os.path.abspath(
            argument[1],
        )

if path == None:
    print("Error")
    exit()


df = pandas.read_json(
    path,
    orient='records',
    lines=False,
).set_index(
    'pvid',
).sort_index(
    ascending=True,
)

df['category_level_1'] = df['categories'].apply(
    lambda
    c: c[0]['description'],
)

df['category_level_2'] = df['categories'].apply(
    lambda
    c: c[1]['description'],
)

df['regulated_product_name'] = df['languages'].apply(
    lambda
    c: c[0]['groupingSets'][0]['attributes']['regulatedProductName']
)

df['ingredients'] = df['languages'].apply(
    lambda
    c: '.'.join(
        c[0]['groupingSets'][0]['attributes']['ingredients']
    )
)

df['storage_env'] = df['languages'].apply(
    lambda
    c: c[0]['groupingSets'][0]['attributes']['storageType'][0]
    ['lookupValue']
)

df['pack_type'] = df['languages'].apply(
    lambda
    c: c[0]['groupingSets'][0]['attributes']['packType'][0]
    ['lookupValue']
)


def parse_cooking_guidelines(c):
    try:
        guidelines = [
            item['nameValue']
            for item in c[0]['groupingSets'][0]['attributes']
            ['cookingGuidelines']
        ]
        return '. '.join(set(guidelines))

    except KeyError:
        return 'None'


df['cooking_type'] = df['languages'].apply(
    parse_cooking_guidelines
)

df = df[[
    'category_level_1',
    'category_level_2',
    'regulated_product_name',
    'ingredients',
    'storage_env',
    'pack_type',
    'cooking_type',
]]

df['text'] = df.apply(
    lambda s: '. '.join(s[s.notna()]),
    axis=1,
)

scriptPath = os.path.dirname(os.path.realpath(__file__))

nlp = spacy.load(
    os.path.abspath(scriptPath + '/spacy/training/model-best')
)

def predict(text):
    doc = nlp(text)
    return max(
        doc.cats,
        key=lambda key: doc.cats[key],
    )

predictions = df['text'].apply(predict)

newArr = list()
for k,v in predictions.items():
    new = dict()
    new['pvid'] = k
    new['category'] = v
    newArr.append(new)

print(json.dumps(newArr))