import json
import os
import sys

import joblib
import pandas as pd

arguments = sys.argv[1:]

# -s json string
# -p json path
arguments = [arguments[i : i + 2] for i in range(0, len(arguments), 2)]
# path = os.path.abspath('sklearn/data/trial-json-products.json') #default for testing

path = os.path.abspath("spacy/data/test.json")  # default for testing
for argument in arguments:
    if argument[0] == "-s":
        path = argument[1]
    elif argument[0] == "-p":
        path = os.path.abspath(
            argument[1],
        )

if path is None:
    print("Error")
    exit()

df = (
    pd.read_json(
        path,
        orient="records",
        lines=False,
    )
    .set_index(
        "pvid",
    )
    .sort_index(
        ascending=True,
    )
)

df["category_level_1"] = df["categories"].apply(
    lambda c: c[0]["description"],
)

df["category_level_2"] = df["categories"].apply(
    lambda c: c[1]["description"],
)

df["regulated_product_name"] = df["languages"].apply(
    lambda c: c[0]["groupingSets"][0]["attributes"]["regulatedProductName"]
)

df["ingredients"] = df["languages"].apply(
    lambda c: ". ".join(c[0]["groupingSets"][0]["attributes"]["ingredients"])
)

df["text"] = df[["regulated_product_name", "ingredients"]].apply(
    lambda s: ". ".join(s[s.notna()]),
    axis=1,
)

df["storage_env"] = df["languages"].apply(
    lambda c: c[0]["groupingSets"][0]["attributes"]["storageType"][0]["lookupValue"]
)

df["pack_type"] = df["languages"].apply(
    lambda c: c[0]["groupingSets"][0]["attributes"]["packType"][0]["lookupValue"]
)

df['cooking_type'] = df['languages'].apply(
    lambda
    c: c[0]['groupingSets'][0]['attributes']['cookingGuidelines']
)

df = df[
    [
        "category_level_1",
        "category_level_2",
        "storage_env",
        "pack_type",
        "cooking_type",
        "text",
    ]
]


scriptPath = os.path.dirname(os.path.realpath(__file__))
le = joblib.load(os.path.abspath(scriptPath + "/sklearn/models-full/LabelEncoder.pkl"))

vc = joblib.load(os.path.abspath(scriptPath + "/sklearn/models-full/VotingClassifier.pkl"))

df["predict"] = le.inverse_transform(vc.predict(df))

newArr = list()
for k, v in df["predict"].items():
    new = dict()
    new["pvid"] = k
    new["category"] = v

    if new['category'] == 'snacks':
        new['category'] = 'snack'

    newArr.append(new)

print(json.dumps(newArr))