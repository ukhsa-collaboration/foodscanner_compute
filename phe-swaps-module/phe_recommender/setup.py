from setuptools import setup, find_packages, setuptools

with open('requirements.txt') as f:
    requirements = f.read().splitlines()

with open("README.md", "r") as fh:
    long_description = fh.read()

setuptools.setup(
    name = "phe-recommender", # Replace with your own username
    version = "0.0.1",
    author = "Daniele Dal Grande",
    author_email = "daniele.dalgrande@flipsidegroup.com",
    description = "A product recommender system that suggests healthier items.",
    long_description = long_description,
    long_description_content_type = "text/markdown",
    install_requires=requirements,
    url = "",
    packages = setuptools.find_packages(),
    classifiers = [
        "Programming Language :: Python :: 3.6.9",
        "License :: OSI Approved :: MIT License",
        "Operating System :: OS Independent",
    ],
    python_requires='>=3.6.9',

)
