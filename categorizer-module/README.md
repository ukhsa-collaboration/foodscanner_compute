This module is responsible for the AI algorithm(s) that will categorize food products based on certain attributes.

## Training
To train each of the algorithms, first you need to install the dependent packages, which on Ubuntu 20.04 is as easy as running:

```bash
sudo apt-get install python3-pip python3-setuptools -y
pip3 install -r requirements.txt
```

Then train the algorithm you desire with the relevant script. E.g.

```bash
python3 sklearn-train.py
```

```bash
python3 spacy-train.py
```

Re-training results in changes to the files within the `models` folders, which should be merged into master. 
Any changes to the training data within the `data` folders should remain in the ml-training branch.


## Getting a Prediction

Below is an example JSON configuration from which you can create a prediction in each of the algorithms (each of them takes the same structure).

```json
[{
    "pvid": "9417986939834",
    "categories": [{
        "description": "Dairy & Bread"
    }, {
        "description": "Yoghurts"
    }],
    "languages": [{
        "groupingSets": [{
            "attributes": {
                "storageType": [{
                    "lookupValue": "Ambient"
                }],
                "packType": [{
                    "lookupValue": "Sleeve"
                }],
                "ingredients": ["Invert Sugar Syrup", "Wheat Flour", "Water", "Glucose Syrup", "Sugar", "Rice Bran Oil", "Pecan Nuts (3%)", "Sorbitol", "Dried Skimmed Milk", "Breadcrumb (Wheat)", "Glycerine", "Milk Proteins", "Unsalted Butter", "Dried Whole Egg", "Modified Maize Starch", "Raising Agents (Sodium Bicarbonate, Disodium Diphosphate)", "Dried Buttermilk", "Firming Agent (Calcium Chloride)", "Stabilisers (Xanthan Gum, Sodium Phosphate)", "Flavourings", "Emulsifiers (Polyglycerol Esters of Fatty Acids, Mono- and Diglycerides of Fatty Acids, Lecithin)", "Gelling Agent (Pectin)", "Preservatives (Sorbic Acid, Potassium Sorbate, Alcohol)", "Salt", "Acidity Regulator (Citric Acid)", "Natural Colour (Caramel I)"],
                "regulatedProductName": "Aunty's Delicious Butterscotch & Pecan Steamed Puds 2 x 95g"
            }
        }]
    }]
}]
```

Save the file to `input.json`.

Then simply run:

```bash
python3 spacy-getPredictions.py -p input.json
```


```bash
python3 sklearn-getPredictions.py -p input.json
```

You will recieve the predicted category in a JSON response similar to:

```json
[{"pvid": 9417986939834, "category": "condiments_other"}]
```

Note that the output pvid will match whatever pvid is fed in, so we will swap this value out with the product barcodes, or whatever identifier we need.



