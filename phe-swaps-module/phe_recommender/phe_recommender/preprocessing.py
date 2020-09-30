#This class represents the methods for preprocessing (cleansing) the dataset:
#1- dedupe
#2-merging the dataframes
#3-formating the columns
#4-natural language processing
import numpy as np
import pandas as pd
import re

#Natural Language Processing libraries imports
import nltk
from nltk.tokenize import RegexpTokenizer
from nltk.stem import WordNetLemmatizer,PorterStemmer
from nltk.corpus import stopwords
import nltk; nltk.download('stopwords')
from nltk import word_tokenize
from nltk.corpus import stopwords
#import fasttext

ps = nltk.porter.PorterStemmer()
stop_words = stopwords.words('english')
stop_words.extend(['from', 'subject', 're', 'edu', 'use'])



class preprocessing:


    def dedupe (self, df):
        '''
            clean up from duplicate product names and barcodes
        '''
        for i in ['barcode']:
            df[i]=df[i].astype('str')
            df['is_duplicated']=df.duplicated(i)
            df=df.drop_duplicates([i], keep='first')
        return (df)


    def merge(self, new, full):
        '''
            add columns from the previous full file
            create a combined dataframe
        '''
        columns_to_add = ['barcode','manufacturer', 'packunits', '100units', 'saturates_level', 'salt_level', 'sugar_level']
        combined=pd.merge(new, full[columns_to_add], how = 'left', left_on='barcode', right_on='barcode')
        return combined

    def format_fields (self, df):
        '''
            format barcodes as strings
        '''
        df['sub_other'] = df['sub_category_name'].astype(str) + '_' + df['product_category_name'].astype(str)
        df['ingredients'] = df['ingredients'].replace("\|", " ", regex=True)
        df['packunits'] = df['packunits'].replace('Millilitres', 'ml').replace('Grams', 'g')
        df['item_type'] = np.where((df['packunits'] == 'ml'), 'drink', 'food')
        replace_values = {'P1' : 101, 'P2' : 102, 'P3' : 103, 'P4' : 104, 'P5' : 105, 'P6' : 106, 'P7' : 107, 'P8' : 108, 'P9' : 109,
                     'p2' : 102, 'p5' : 105, 'p1' : 101}

        for col in ['main_category', 'sub_category_1', 'sub_category_2']:
            df[col] = df[col].replace('Check', 0, regex=True).replace('07l17', 0, regex=True).replace('PHE Sticker ', 0, regex=True).replace('P1', 101).replace('09>p1', 0)
            df[col].fillna(0)

        df['sub_category_2'] = df['sub_category_2'].replace(replace_values)
        df['sub_category_2'] = df['sub_category_2'].astype(float)

        df['high_five_man_label'] = df['saturates_level'].astype(str) + df['sugar_level'].astype(str) + df['salt_level'].astype(str)
        df['high_five_man'] = np.where((df['high_five_man_label'] == 'LowLowLow'), 1, 0)

        columns_list = ['fat_100', 'fat_serving', 'saturates_100', 'saturates_serving', 'sugar_100',
                    'sugar_serving', 'salt_100', 'salt_serving']

        for col in columns_list:
            df[col] = df[col].replace('<', '', regex=True).replace(['0', '0.0'], 0.0, regex=False).replace(['0..1'], 0.1, regex=False).astype(float)
            for value in df[col].values:
                value = re.sub("[^\d\.]", "", str(value))

        df_prep = df[['barcode', 'product_name', 'manufacturer', 'category', 'sugar_100', 'fat_100',
                      'salt_100', 'fibre_100', 'saturates_100', 'cals_100', 'sugar_serving',
                      'fat_serving', 'salt_serving', 'fibre_serving', 'saturates_serving',
                      'cals_serving', 'packsize', 'packunits', 'packcount', 'ingredients', 'badge_new',
                      'company_name', 'scans', 'main_category', 'saturates_level',
                      'sub_category_1', 'sub_category_2', 'main_and_sub1', 'sugar_level',
                      'main_category_name', 'sub_category_name', 'source_name', 'salt_level',
                      'product_category_name','high_five_man']]

        cols_types = {'barcode': str, 'product_name': str, 'manufacturer':str, 'category': str, 'sugar_100': float, 'fat_100': float,
                  'salt_100': float, 'fibre_100': float, 'saturates_100': float, 'cals_100': float, 'sugar_serving': float,
                  'fat_serving': float, 'salt_serving': float, 'fibre_serving': float, 'saturates_serving': float,
                  'cals_serving': float, 'packsize': float, 'packcount': float, 'ingredients': str, 'badge_new': float,
                  'company_name': str, 'scans': float, 'main_category': str, 'sub_category_1': str, 'sub_category_2': str,
                  'main_and_sub1': str, 'main_category_name': str, 'sub_category_name': str, 'source_name': float,
                  'product_category_name': str,'high_five_man':float}
        df_prep = df_prep.astype(cols_types)

        percent_missing = df_prep.isnull().sum() * 100 / len(df_prep)
        count_missing = df_prep.isnull().sum()
        missing_values_df = pd.DataFrame({'column_name': df_prep.columns,
                                     'percent_missing': percent_missing,
                                     'count_missing' : count_missing})
        missing_values_df = missing_values_df.sort_values(by = ['percent_missing'], ascending = False)
        df_prep.fillna(0, inplace=True)


        return df_prep

    def nlp_process (self, df, cols_to_tokens):
        '''
            Generating NL-processed DataFrame
        '''
        df['product_name'] = df["product_name"].str.replace("\s:pmp|PMP|£", "")
        df['product_name'] = df["product_name"].str.replace("(\d+)(?:p)'", "")
        def preprocess (sentence):
            #Change to lowercase
            sentence=str(sentence).lower()
            #tokenizing the sentences.
            #Reducing the words to their stems
            tokenizer = RegexpTokenizer(r'\w+')
            tokens = tokenizer.tokenize(sentence)
            stems =' '.join([ps.stem(word) for word in tokens]) #stemmatize
            filtered_words = [w for w in tokens if len(w) > 2 if not w in stop_words] #remove stopwords
            return " ".join(filtered_words)

        for i in cols_to_tokens:
            df[str(i)+'_clean']=df[i].apply(preprocess)
        return df
