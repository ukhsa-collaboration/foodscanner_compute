FROM ubuntu:20.04

# Fix timezone issue
ENV TZ=Europe/London

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone \
    && apt-get update \
    && apt-get dist-upgrade -y \
    && apt-get install -y cron vim python3-pip python3-pymysql php7.4-cli php7.4-mysql php7.4-gd python3-setuptools composer

# For some reason this will fail if you bundle it in with previous command using &&
RUN pip3 install flask_sqlalchemy

# Install AWS CLI (pip already installed)
RUN pip3 install awscli

# Add and install the machine learning categorization module
COPY categorizer-module /root/categorizer-module
WORKDIR /root/categorizer-module

RUN pip3 install -r requirements.txt

# install either wheels for trained models
RUN pip3 install ./spacy/packages-full/en_textcat-2.0.0.tar.gz
# RUN pip3 install ./spacy/packages-simple/en_textcat_simple-2.0.0.tar.gz

# Add and install the swaps module
COPY phe-swaps-module /root/phe-swaps-module
WORKDIR /root/phe-swaps-module/phe_recommender
RUN python3 setup.py install

# Add our wrapper code
COPY src /root/src

# Run composer install to install packages
WORKDIR /root/src
RUN composer install

# Execute the containers startup script which will start many processes/services
# The startup file was already added when we added "project"
CMD ["/bin/bash", "/root/src/startup.sh"]
