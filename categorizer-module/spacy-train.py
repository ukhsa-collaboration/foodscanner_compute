from pathlib import Path

import pandas as pd
from sklearn.model_selection import train_test_split
from spacy.gold import docs_to_json

import spacy

df = (
    pd.read_excel(
        Path(
            "spacy",
            "data",
            "201026_PHE_category_sheet.xlsx",
        ),
        usecols=[
            "lProductVersionID",
            "sDescription",
            "sCategoryLevel1",
            "sCategoryLevel2",
            "regulated_product_name",
            "ingredients",
            "storage_env",
            "pack_type",
            "cooking_type",
            "PHE_category_jan",
        ],
        dtype={
            "lProductVersionID": "uint64",
            "sDescription": str,
            "sCategoryLevel1": "category",
            "sCategoryLevel2": "category",
            "regulated_product_name": str,
            "ingredients": str,
            "storage_env": "category",
            "pack_type": "category",
            "cooking_type": str,
            "PHE_category_jan": "category",
        },
    )
    .rename(
        columns={
            "lProductVersionID": "pvid",
            "sDescription": "description",
            "sCategoryLevel1": "category_level_1",
            "sCategoryLevel2": "category_level_2",
            "PHE_category_jan": "label",
        }
    )
    .assign(
        ingredients=lambda df: df["ingredients"].str.replace("|", ".").fillna("None"),
        cooking_type=lambda df: df["cooking_type"].fillna("None").str.replace("|", "."),
    )
    .set_index(
        "pvid",
    )
    .sort_index(
        ascending=True,
    )
    .drop_duplicates(
        subset="description",
        keep="last",
    )
    .dropna(
        how="any",
    )
    .assign(
        text=lambda df: df.apply(
            ". ".join,
            axis=1,
        )
    )
)

labels = df["label"].unique()

nlp = spacy.load("en_core_web_sm")


def convert_to_spacy(s, labels):
    """
    Convert text and labels into a spaCy compitable format
    """
    cats = {label: 1.0 if label in s["multilabel"] else 0.0 for label in labels}

    # make spacy document from the 'text' column
    # Update document categories to cats dictionary
    doc = nlp(s["text"])
    doc.cats = cats

    return docs_to_json([doc])


df["spacy"] = df.apply(
    lambda s: convert_to_spacy(s, labels),
    axis=1,
)


def split_save_json(df, test_size):
    """
    Split data 30/70 and stratify by label
    Save into json
    """
    train, val = train_test_split(
        df["spacy"],
        test_size=test_size,
        random_state=42,
        shuffle=True,
    )

    train.to_json(
        Path(
            "spacy",
            "data",
            "dataset_spacy_train.json",
        ),
        orient="records",
    )

    val.to_json(
        Path(
            "spacy",
            "data",
            "dataset_spacy_val.json",
        ),
        orient="records",
    )


split_save_json(df, test_size=0.3)
