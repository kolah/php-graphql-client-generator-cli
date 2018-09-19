#!/bin/bash
echo "Preparing release"
# Unpack secrets; -C ensures they unpack *in* the .travis directory
tar xvf .travis/secrets.tar -C .travis

# Setup SSH agent:
eval "$(ssh-agent -s)" #start the ssh agent
chmod 600 .travis/deploy-key.pem
ssh-add .travis/deploy-key.pem

# Setup git defaults:
git config --global user.email "krzysztof@kolasiak.pl"
git config --global user.name "kolah"

# Add SSH-based remote to GitHub repo:
git remote add deploy git@github.com:kolah/php-graphql-client-generator-cli.git
git fetch deploy

# Get box and build PHAR
wget https://box-project.github.io/box2/manifest.json
BOX_URL=$(php bin/parse-manifest.php manifest.json)
rm manifest.json
wget -O box.phar ${BOX_URL}
chmod 755 box.phar
./box.phar build -vv

# Without the following step, we cannot checkout the gh-pages branch due to
# file conflicts:
mv bin/gql2php.phar bin/gql2php.phar.tmp

# Checkout gh-pages and add PHAR file and version:
git checkout -b gh-pages deploy/gh-pages
mv bin/gql2php.phar.tmp bin/gql2php.phar
sha1sum bin/gql2php.phar > bin/gql2php.phar.version
git add bin/gql2php.phar bin/gql2php.phar.version

# Commit and push:
git commit -m 'Rebuilt phar'
git push deploy gh-pages:gh-pages
