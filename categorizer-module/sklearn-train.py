import os

import joblib
import pandas as pd
from sklearn.compose import ColumnTransformer
from sklearn.ensemble import RandomForestClassifier, VotingClassifier
from sklearn.feature_extraction import FeatureHasher
from sklearn.feature_extraction.text import CountVectorizer, TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import (balanced_accuracy_score,
                             multilabel_confusion_matrix)
from sklearn.model_selection import GridSearchCV, train_test_split
from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import LabelEncoder, OneHotEncoder
from sklearn.svm import LinearSVC
from sklearn.utils.class_weight import (compute_class_weight,
                                        compute_sample_weight)

import xgboost as xgb

df = pd.read_excel(
    os.path.join(
        'sklearn',
        'data',
        '200901_PHE_category_sheet.xlsx',
    ),
    usecols=[
        'lProductVersionID',
        'sDescription',
        'sCategoryLevel1',
        'sCategoryLevel2',
        'regulated_product_name',
        'ingredients',
        'storage_env',
        'pack_type',
        'cooking_type',
        'PHE_category_jan',
    ],
    dtype={
        'lProductVersionID': 'uint64',
        'sDescription': str,
        'sCategoryLevel1': 'category',
        'sCategoryLevel2': 'category',
        'regulated_product_name': str,
        'ingredients': str,
        'storage_env': 'category',
        'pack_type': 'category',
        'cooking_type': str,
        'PHE_category_jan': 'category',
    },
).rename(
    columns={
        'lProductVersionID': 'pvid',
        'sDescription': 'description',
        'sCategoryLevel1': 'category_level_1',
        'sCategoryLevel2': 'category_level_2',
        'PHE_category_jan': 'label',
    }
).assign(
    ingredients=lambda df: df['ingredients'].str.replace(
        '|', '.').fillna('None'),
    cooking_type=lambda df: df['cooking_type'].fillna('None').str.rsplit('| '),
    text=lambda
    df: df[['description', 'regulated_product_name', 'ingredients']].apply(
        lambda s: '. '.join(s[s.notna()]),
        axis=1,
    )
).set_index(
    'pvid',
).sort_index(
    ascending=True,
).drop_duplicates(
    subset='description',
    keep='last',
).dropna(
    how='any',
).drop(
    ['description', 'regulated_product_name', 'ingredients'],
    axis=1,
)

df.info()

y = df['label']
num_class = len(y.unique())

class_weights = compute_class_weight(
    class_weight='balanced',
    classes=y.unique(),
    y=y,
)

sample_weights = compute_sample_weight(
    class_weight='balanced',
    y=y,
)


X = df.drop('label', axis=1)

le = LabelEncoder()
y = le.fit_transform(y)

joblib.dump(
    le,
    os.path.join(
        'sklearn',
        'models',
        'LabelEncoder.pkl',
    )
)

X_train, X_test, y_train, y_test = train_test_split(
    X,
    y,
    test_size=0.3,
    random_state=42,
    shuffle=True,
    stratify=y,
)

classifiers = dict()

text_transformer = Pipeline(
    steps=[
        ('tfidf', TfidfVectorizer(lowercase=True,
                                  ngram_range=(1, 2),
                                  norm='l2',
                                  use_idf=True))
    ]
)

cat_transformer = Pipeline(
    steps=[
        ('onehot', OneHotEncoder(categories='auto',
                                 sparse=False,
                                 handle_unknown='ignore'))
    ]
)

multi_cat_transformer = Pipeline(
    steps=[
        ('binarizer', CountVectorizer(analyzer=set))
    ]
)

hash_transformer = Pipeline(
    steps=[
        ('hasher', FeatureHasher(n_features=10,
                                 input_type='string',
                                 alternate_sign=False))
    ]
)

preprocessor = ColumnTransformer(
    transformers=[
        ('text', text_transformer, 'text'),
        ('cat', cat_transformer, ['category_level_1',
                                  'category_level_2', 'storage_env']),
        ('multi_cat', multi_cat_transformer, 'cooking_type'),
        ('hash', hash_transformer, 'pack_type'),
    ],
    remainder='drop',
)

hashed_features = hash_transformer.named_steps['hasher'].fit_transform(
    df['pack_type']
).toarray()

df_hashed_features = df[['pack_type']].reset_index(drop=True).join(
    pd.DataFrame(hashed_features)
)

df_hashed_features.groupby(
    ['pack_type']
).first().duplicated(
    keep=False
).sum()

classifier = MultinomialNB(alpha=1.0)

pipeline = Pipeline(
    steps=[
        ('preprocessor', preprocessor),
        ('classifier', classifier),
    ]
)

params = {
    'classifier__alpha': [0.001, 0.01, 0.1, 0.3, 0.5, 1],
}

classifiers['MultinomialNB'] = {
    'pipeline': pipeline,
    'params': params,
}

classifier = LogisticRegression(
    solver='saga',
    class_weight='balanced',
    multi_class='multinomial',
    penalty='l2',
    random_state=42,
    max_iter=100,
)

pipeline = Pipeline(
    steps=[
        ('preprocessor', preprocessor),
        ('classifier', classifier),
    ]
)

params = {
    'classifier__C': [1, 10, 100],
}

classifiers['LogisticRegression'] = {
    'pipeline': pipeline,
    'params': params,
}

classifier = LinearSVC(
    penalty='l2',
    loss='squared_hinge',
    # dual=True,
    multi_class='ovr',
    class_weight='balanced',
    random_state=42,
    max_iter=1000,
)

pipeline = Pipeline(
    steps=[
        ('preprocessor', preprocessor),
        ('classifier', classifier),
    ]
)

params = {
    'classifier__C': [1, 10, 100],
    'classifier__dual': [True, False],
}

classifiers['LinearSVC'] = {
    'pipeline': pipeline,
    'params': params,
}

classifier = RandomForestClassifier(
    bootstrap=True,
    oob_score=True,
    max_features='sqrt',
    class_weight='balanced',
    random_state=42,
)

pipeline = Pipeline(
    steps=[
        ('preprocessor', preprocessor),
        ('classifier', classifier),
    ]
)

params = {
    'classifier__criterion': ['gini', 'entropy'],
    'classifier__max_depth': [4, 5, 6],
    'classifier__n_estimators': [100, 200, 400],
}

classifiers['RandomForestClassifier'] = {
    'pipeline': pipeline,
    'params': params,
}

classifier = xgb.XGBClassifier(
    booster='gbtree',
    objective='multi:softmax',
    sampling_method='uniform',
    num_class=num_class,
    random_state=42,
)

pipeline = Pipeline(
    steps=[
        ('preprocessor', preprocessor),
        ('classifier', classifier),
    ]
)

params = {
    'classifier__learning_rate': [0.1, 0.3],
    'classifier__subsample': [0.7, 0.9],
    'classifier__colsample_bytree': [0.7, 0.9],
    'classifier__max_depth': [3, 4],
    'classifier__n_estimators': [100, 200, 400],
}

classifiers['XGBClassifier'] = {
    'pipeline': pipeline,
    'params': params,
}

if not os.path.isdir('models'):
    os.mkdir('models')

for k, v in classifiers.items():

    print(f'\nRunning grid search with cross validation for {k}...')

    gs = GridSearchCV(
        v['pipeline'],
        v['params'],
        scoring='balanced_accuracy',
        cv=5,
        n_jobs=-1,
        verbose=2,
    )

    gs.fit(
        X_train,
        y_train,
    )

    joblib.dump(
        gs,
        os.path.join(
            'models',
            f'{k}.pkl',
        )
    )

for k, v in classifiers.items():

    gs = joblib.load(
        os.path.join(
            'models',
            f'{k}.pkl',
        )
    )

    v['best_score'] = gs.best_score_
    v['best_params'] = gs.best_params_
    v['best_estimator'] = gs.best_estimator_
    v['best_estimator_params'] = gs.best_estimator_.named_steps['classifier'].get_params()

    print(f'Running evaluation on test data for {k}...')
    y_pred = gs.predict(X_test)

    v['testing_accuracy'] = balanced_accuracy_score(
        y_test,
        y_pred,
    )

    v['testing_conf_matrix'] = multilabel_confusion_matrix(
        y_test,
        y_pred,
        samplewise=False,
    )

best_estimators = [(k, v['best_estimator'])
                   for k, v in classifiers.items()]

vc = VotingClassifier(
    estimators=best_estimators,
    voting='hard',
)

print('Fitting VotingClassifier using all best estimators...')
vc.fit(X_train, y_train)

y_pred = vc.predict(X_test)

classifiers['VotingClassifier'] = {

    'best_score': vc.score(X_train, y_train),

    'testing_accuracy': balanced_accuracy_score(
        y_test,
        y_pred,
    ),

    'testing_conf_matrix': multilabel_confusion_matrix(
        y_test,
        y_pred,
        samplewise=False,
    )
}

joblib.dump(
    vc,
    os.path.join(
        'sklearn',
        'models',
        'VotingClassifier.pkl',
    )
)

pd.DataFrame.from_dict(
    classifiers,
    orient='index',
).to_csv(
    os.path.join(
        'sklearn',
        'models',
        'models.csv',
    )
)