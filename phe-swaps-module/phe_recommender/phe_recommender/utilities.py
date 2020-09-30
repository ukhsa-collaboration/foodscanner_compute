import pandas as pd
import numpy as np
import re

class utilities:


    def find_badges(self,row,df,df_items):
        '''
            Methods to be used to find the badges for items in the dataset. If the new badge exist the column value will be 1
            if the new badge does not exist the column value will be 0.
        '''

        codes = df.set_index('PHE_cat')['code'].to_dict()
        codes['sandwiches'] = '08.01.11'

        #
        def get_codes(key):
            '''
                Load codes from dictionary
            '''
            return set(codes[key].split(','))

        # cheese_hard
        if row['category'] in get_codes('cheese_hard') and \
        row['fat_100'] <= 24.2 and \
        row['salt_100'] <= 2:
            return 1

        # cheese_soft
        elif row['category'] in get_codes('cheese_soft') and \
        row['fat_100'] <= 17.1 and \
        row['salt_100'] <= 0.75:
            return 1

        # cheese_other
        elif row['category'] in get_codes('cheese_other') and \
        row['fat_100'] <= 3 and \
        row['salt_100'] <= 2:
            return 1

        # spread_fat
        elif row['category'] in get_codes('spread_fat') and \
        row['fat_100'] <= 41 and \
        row['salt_100'] <= 1.6:
            return 1

        # milk
        elif row['category'] in get_codes('milk'):
            return 1

       # milk_alternative
        elif row['category'] in get_codes('milk_alternative') and \
        row['sugar_100'] <= 2.5 and \
        re.search('calcium', row['ingredients'], re.I) is not None:
            return 1

       # milk_drink
        elif row['category'] in get_codes('milk_drink') and \
        row['sugar_100'] <= 5.4 and \
        row['fat_100'] <= 1.5:
            return 1

        # yogurt
        elif row['category'] in get_codes('yogurt') and \
        row['sugar_100'] <= 10.8 and \
        row['fat_100'] <= 3:
            return 1

        # yogurt_drink
        elif row['category'] in get_codes('yogurt_drink') and \
        row['sugar_100'] <= 5.4 and \
        row['fat_100'] <= 1.5:
            return 1

        # fruit_dried
        elif row['category'] in get_codes('fruit_dried') and \
        row['fat_100'] <= 3:
            return 1

        # baked_beans
        elif row['category'] in get_codes('baked_beans') and \
        row['sugar_100'] <= 3.2 and \
        row['salt_100'] <= 0.4:
            return 1

        # cereal
        elif row['category'] in get_codes('cereal') and \
        row['sugar_100'] <= 8 and \
        row['salt_100'] <= 1.125 and \
        row['fibre_100'] >= 3:
            return 1

        # musli
        elif row['category'] in get_codes('musli') and \
        row['salt_100'] <= 1.125 and \
        row['fibre_100'] >= 3 and \
        re.search('sugar', row['ingredients'], re.I) is None:
            return 1

        # fruit_canned
        elif row['category'] in get_codes('fruit_canned') and \
        re.search('sugar', row['ingredients'], re.I) is None and \
        re.search('syrup', row['ingredients'], re.I) is None :
            return 1

        # pasta
        elif row['category'] in get_codes('pasta') and \
        row['salt_100'] <= 0.88:
            return 1

        # pasta_sauce
        elif row['category'] in get_codes('pasta_sauce') and \
        row['sugar_100'] <= 5 and \
        row['salt_100'] <= 0.93:
            return 1

        # grains
        elif row['category'] in get_codes('grains') and \
        row['salt_100'] <= 0.2:
            return 1

        # nuts
        elif row['category'] in get_codes('nuts') and \
        row['sugar_100'] <= 8 and \
        row['salt_100'] <= 0.3:
            return 1

        # sugar_alt
        elif row['category'] in get_codes('sugar_alt') and \
        re.search('syrup', row['ingredients'], re.I) is None and \
        re.search('honey', row['ingredients'], re.I) is None and \
        re.search('nectar', row['ingredients'], re.I) is None :
            return 1

        # confectionery_sf
        elif row['category'] in get_codes('confectionery_sf') and \
        row['sugar_100'] <= 0 and \
        row['saturates_100'] <= 5 :
            return 1

        # ketchup
        elif row['category'] in get_codes('ketchup') and \
        row['sugar_100'] <= 14.8 and \
        row['salt_100'] <= 1 :
            return 1

        # condiment_other
        elif row['category'] in get_codes('condiment_other') and \
        row['sugar_100'] <= 11.4 and \
        row['salt_100'] <= 0.8 :
            return 1

        # mayonnaise
        elif row['category'] in get_codes('mayonnaise') and \
        row['fat_100'] <= 18.9 and \
        row['salt_100'] <= 1.88 :
            return 1

        # veg_canned
        elif row['category'] in get_codes('veg_canned') and \
        re.search('salt', row['ingredients'], re.I) is None and \
        re.search('sugar', row['ingredients'], re.I) is None :
            return 1

        # tea_coffie
        elif row['category'] in get_codes('tea_coffie') and \
        row['fat_100'] <= 0 and \
        row['sugar_100'] <= 0 :
            return 1

        # squash_nas
        elif row['category'] in get_codes('squash_nas') and \
        row['sugar_100'] <= 2.5 and \
        re.search('sugar', row['ingredients'], re.I) is None :
            return 1

        # drink_nas
        elif row['category'] in get_codes('drink_nas') and \
        row['sugar_100'] <= 2.5 and \
        re.search('sugar', row['ingredients'], re.I) is None :
            return 1

        # fruit_veg_juice
        elif row['category'] in get_codes('fruit_veg_juice') and \
        row['packsize'] <= 150 and \
        re.search('sugar', row['ingredients'], re.I) is None :
            return 1

        # water
        elif row['category'] in get_codes('water') and \
        row['sugar_100'] <= 0.5 :
            return 1

        # bread
        elif row['category'] in get_codes('bread') and \
        row['sugar_100'] <= 22.5 and \
        row['salt_100'] <= 1.13 and \
        row['saturates_100'] <= 1.5 and \
        row['fibre_100'] >= 3 :
            return 1

        # morning_good
        elif row['category'] in get_codes('morning_good') and \
        row['sugar_100'] <= 22.5 and \
        row['salt_100'] <= 0.88 and \
        row['saturates_100'] <= 1.5 and \
        row['fibre_100'] >= 3:
            return 1

        # fruit
        elif row['category'] in get_codes('fruit') and \
        re.search('sugar', row['ingredients'], re.I) is None and \
        re.search('salt', row['ingredients'], re.I) is None :
            return 1

        # sandwiches
        elif row['category'] in get_codes('sandwiches') and \
        row['sugar_100'] <= 22.5 and \
        row['fat_100'] <= 17.5 and \
        row['salt_100'] <= 0.88 and \
        row['saturates_100'] <= 1.5 and \
        row['cals_100'] <= 400 and \
        row['fibre_100'] >= 3:
            return 1

        # veg
        elif row['category'] in get_codes('veg') and \
        re.search('sugar', row['ingredients'], re.I) is None and \
        re.search('salt', row['ingredients'], re.I) is None :
            return 1

        # fish_canned
        elif row['category'] in get_codes('fish_canned') and \
        row['salt_100'] <= 0.93 and \
        row['sugar_100'] <= 5 :
            return 1

        # fish_frozen
        elif row['category'] in get_codes('fish_frozen') and \
        row['sugar_100'] <= 5 and \
        row['fat_100'] <= 17.5 and \
        row['saturates_100'] <= 1.5 and \
        row['salt_100'] <= 1.13 :
            return 1

        # meat
        elif row['category'] in get_codes('meat') and \
        row['sugar_100'] <= 5 and \
        row['fat_100'] <= 7 and \
        row['salt_100'] <= 0.88 :
            return 1

        # jelly
        elif row['category'] in get_codes('jelly') and \
        row['sugar_100'] <= 0.5 :
            return 1

        # chips
        elif row['category'] in get_codes('chips') and \
        row['saturates_100'] <= 1.5 and \
        row['fat_100'] <= 3 and \
        row['salt_100'] <= 0.3 :
            return 1

        # veg_frozen
        elif row['category'] in get_codes('veg_frozen') and \
        re.search('sugar', row['ingredients'], re.I) is None and \
        re.search('salt', row['ingredients'], re.I) is None :
            return 1

        # snack
        elif row['category'] in get_codes('snack') and \
        row['sugar_100'] <= 22.5 and \
        row['fat_100'] <= 17.5 and \
        row['salt_100'] <= 1.5 and \
        row['saturates_100'] <= 5 and \
        row['cals_serving'] <= 100 :
            return 1

        # popcorn
        #elif row['category'] in get_codes('popcorn') :
            #return 1

        # ice_pop
        elif row['category'] in get_codes('ice_pop') and \
        row['sugar_100'] <= 2.5 and \
        row['fat_100'] <= 17.5 and \
        row['salt_100'] <= 1.5 and \
        row['saturates_100'] <= 5 and \
        row['cals_serving'] <= 100 :
            return 1

        # ready_meal
        elif row['category'] in get_codes('ready_meal') and \
        row['sugar_100'] <= 22.5 and \
        row['fat_100'] <= 17.5 and \
        row['salt_100'] <= 1.25 and \
        row['saturates_100'] <= 5 and \
        row['cals_100'] <= 223 and \
        row['sugar_serving'] <= 27 and \
        row['fat_serving'] <= 21 and \
        row['saturates_serving'] <= 6 and \
        row['salt_serving'] <= 1.8:
            return 1

        # pizza
        elif row['category'] in get_codes('pizza') and \
        row['sugar_100'] <= 22.5 and \
        row['fat_100'] <= 17.5 and \
        row['salt_100'] <= 0.95 and \
        row['saturates_100'] <= 5 and \
        row['cals_100'] <= 109 and \
        row['sugar_serving'] <= 27 and \
        row['fat_serving'] <= 21 and \
        row['saturates_serving'] <= 6 and \
        row['salt_serving'] <= 1.8 :
            return 1

        # hummus
        elif row['category'] in get_codes('hummus') and \
        row['sugar_100'] <= 22.5 and \
        row['fat_100'] <= 17.5 and \
        row['salt_100'] <= 1.5 and \
        row['saturates_100'] <= 5 :
            return 1

        else:
            return 0

    def PHE_cat_extract (self,row,df,df_items):
        '''
            The method to be used for extracting PHE_category in case it is needed for filtering.
        '''
        codes = df.set_index('PHE_cat')['code'].to_dict()
        codes['sandwiches'] = '08.01.11'

        def get_codes(key):
            return set(codes[key].split(','))

        if row['category'] in get_codes('cheese_hard'):
            return 'cheese_hard'

        # cheese_soft
        elif row['category'] in get_codes('cheese_soft'):
            return 'cheese_soft'

        # cheese_other
        elif row['category'] in get_codes('cheese_other'):
            return 'cheese_other'

        # spread_fat
        elif row['category'] in get_codes('spread_fat'):
            return 'spread_fat'

        # milk
        elif row['category'] in get_codes('milk'):
            return 'milk'

       # milk_alternative
        elif row['category'] in get_codes('milk_alternative'):
            return 'milk_alternative'

       # milk_drink
        elif row['category'] in get_codes('milk_drink'):
            return 'milk_drink'

        # yogurt
        elif row['category'] in get_codes('yogurt'):
            return 'yogurt'

        # yogurt_drink
        elif row['category'] in get_codes('yogurt_drink'):
            return 'yogurt_drink'

        # fruit_dried
        elif row['category'] in get_codes('fruit_dried'):
            return 'fruit_dried'

        # baked_beans
        elif row['category'] in get_codes('baked_beans'):
            return 'baked_beans'

        # cereal
        elif row['category'] in get_codes('cereal'):
            return 'cereal'

        # musli
        elif row['category'] in get_codes('musli'):
            return 'musli'

        # fruit_canned
        elif row['category'] in get_codes('fruit_canned'):
            return 'fruit_canned'

        # pasta
        elif row['category'] in get_codes('pasta'):
            return 'pasta'

        # pasta_sauce
        elif row['category'] in get_codes('pasta_sauce'):
            return 'pasta_sauce'

        # grains
        elif row['category'] in get_codes('grains'):
            return 'grains'

        # nuts
        elif row['category'] in get_codes('nuts'):
            return 'nuts'

        # sugar_alt
        elif row['category'] in get_codes('sugar_alt'):
            return 'sugar_alt'

        # confectionery_sf
        elif row['category'] in get_codes('confectionery_sf'):
            return 'confectionery_sf'

        # ketchup
        elif row['category'] in get_codes('ketchup'):
            return 'ketchup'

        # condiment_other
        elif row['category'] in get_codes('condiment_other'):
            return 'condiment_other'

        # mayonnaise
        elif row['category'] in get_codes('mayonnaise'):
            return 'mayonnaise'

        # veg_canned
        elif row['category'] in get_codes('veg_canned'):
            return 'veg_canned'

        # tea_coffie
        elif row['category'] in get_codes('tea_coffie'):
            return 'tea_coffie'

        # squash_nas
        elif row['category'] in get_codes('squash_nas'):
            return 'squash_nas'

        # drink_nas
        elif row['category'] in get_codes('drink_nas'):
            return 'drink_nas'

        # fruit_veg_juice
        elif row['category'] in get_codes('fruit_veg_juice'):
            return 'fruit_veg_juice'

        # water
        elif row['category'] in get_codes('water'):
            return 'water'

        # bread
        elif row['category'] in get_codes('bread'):
            return 'bread'

        # morning_good
        elif row['category'] in get_codes('morning_good'):
            return 'morning_good'

        # fruit
        elif row['category'] in get_codes('fruit'):
            return 'fruit'

        # sandwiches
        elif row['category'] in get_codes('sandwiches'):
            return 'sandwiches'

        # veg
        elif row['category'] in get_codes('veg'):
            return 'veg'

        # fish_canned
        elif row['category'] in get_codes('fish_canned'):
            return 'fish_canned'

        # fish_frozen
        elif row['category'] in get_codes('fish_frozen'):
            return 'fish_frozen'

        # meat
        elif row['category'] in get_codes('meat'):
            return 'meat'

        # jelly
        elif row['category'] in get_codes('jelly'):
            return 'jelly'

        # chips
        elif row['category'] in get_codes('chips'):
            return 'chips'

        # veg_frozen
        elif row['category'] in get_codes('veg_frozen'):
            return 'veg_frozen'

        # snack
        elif row['category'] in get_codes('snack'):
            return 'snack'

        # popcorn
        #elif row['category'] in get_codes('popcorn') :
            #return 1

        # ice_pop
        elif row['category'] in get_codes('ice_pop'):
            return 'ice_pop'

        # ready_meal
        elif row['category'] in get_codes('ready_meal'):
            return 'ready_meal'

        # pizza
        elif row['category'] in get_codes('pizza'):
            return 'pizza'

        # hummus
        elif row['category'] in get_codes('hummus'):
            return 'hummus'

        else:
            return 'Not Available'


    # this section creates vector sentence representations by obtaining word embedidings and averaging all words in the sentences
    def sent_vectorizer(self,sent, model):

        ## apply fasttext processing using cbow method which will link a word to all words in a sentence
        def ft_process (df):
            df_final['names']=df_final['product_name_clean']+' '+df_final['product_category_name_clean']+' '+df_final['sub_category_name_clean']+' '\
            +df_final['main_category_name_clean']
            for i in ['names']:
                name=str(i)+".bin"
                i=df[i].str.encode('utf-8')
                i.to_csv("ft_file.txt")
                model = fasttext.train_unsupervised("ft_file.txt", "cbow", epoch=25, wordNgrams=2, bucket=200000, dim=50, loss='hs')
                #parameters: wordNgrams    -     max length of word ngram [1]
                #            dim           -    size of word vectors default =100
                #            loss          -     loss function {ns, hs, softmax} [softmax]
                model.save_model(name) # save the model as a .bin file in a local directory can be exported

        sent_vec =[]
        numw = 0
        for w in sent:
            try:
                if numw == 0:
                    sent_vec = model[w]
                else:
                    sent_vec = np.add(sent_vec, model[w])
                    numw+=1
            except:
                pass

        return np.asarray(sent_vec) / numw
