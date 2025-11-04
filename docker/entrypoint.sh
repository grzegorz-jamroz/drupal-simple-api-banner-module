#!/bin/bash

# Exit immediately if a command exits with a non-zero status (error)
set -e

SITE_PATH="web/sites/default"

# 1. Wait for the database to be ready
echo "Waiting for database..."
while ! mysqladmin ping -h"db" -u"db" -p"db" --skip-ssl --silent; do
    sleep 1
done
echo "Database is ready."

# 2. Check if Drupal is already installed. We check for the settings.php file.
if [ ! -f "${SITE_PATH}/settings.php" ]; then
    echo "Drupal not found. Installing now..."

    # Run the Drupal site installer
    drush site:install standard \
      --db-url=mysql://db:db@db/db \
      --site-name="Simple API Banner Demo" \
      --account-name=admin \
      --account-pass=admin \
      -y

    echo "Drupal installation complete."
else
    echo "Drupal is already installed."
fi

## 3. Update composer.json to include custom module repository
echo "Updating composer.json to include custom module repository..."

FILE="composer.json"
NEW='{"type":"path","url":"custom/module/*","options":{"symlink":true}}'

if [ ! -f "$FILE" ]; then
  echo "Error: $FILE not found." >&2
  exit 1
fi

if ! command -v jq >/dev/null 2>&1; then
  echo "Error: jq is required. Install jq and retry." >&2
  exit 2
fi

# If the same entry (by type+url) already exists, do nothing.
if jq -e --argjson new "$NEW" '(.repositories // []) | any(.type == $new.type and .url == $new.url)' "$FILE" >/dev/null; then
  echo "Repository entry already present."
  exit 0
fi

tmp=$(mktemp)
jq --argjson new "$NEW" '
  if (.repositories? | type) == "array" then
    .repositories += [$new]
  else
    .repositories = [$new]
  end
' "$FILE" > "$tmp" && mv "$tmp" "$FILE"

echo "Repository entry added to $FILE."

## 4. Install module
composer require grzegorz-jamroz/drupal-simple-api-banner-module

# 5. Enable module
echo "Enabling Simple API Banner Module..."
drush en simple_api_banner -y
echo "Simple API Banner Module enabled."

# 6. Add simple_api_banner_block to a content region in the Claro theme
echo "Adding Simple API Banner block to Claro Content region..."
drush php:eval "use \Drupal\block\Entity\Block; Block::create(['id' => 'claro_simpleapibanner', 'theme' => 'claro', 'region' => 'content', 'plugin' => 'simple_api_banner_block', 'settings' => ['label' => 'Simple API Banner', 'label_display' => 'visible'], 'visibility' => ['request_path' => ['id' => 'request_path', 'negate' => false, 'pages' => \"/node/add/*\r\n/node/*/edit\"], 'response_status' => ['id' => 'response_status', 'negate' => false, 'status_codes' => [200]]]])->save();"
echo "Block Simple API Banner added to Claro Content region."

# 7. Set correct permissions for the files directory
echo "Setting file permissions..."
mkdir -p "${SITE_PATH}/files"
chown -R www-data:www-data "${SITE_PATH}/files"

# 8. Prepare environment for development: disable caching, enable twig debug

drush theme:dev on

if [ ! -f "web/sites/default/settings.local.php" ]; then
  cp web/sites/example.settings.local.php web/sites/default/settings.local.php

  sed -i "s/^# \(\$settings\['cache'\]\['bins'\]\['render'\] = 'cache\.backend\.null';\)/\1/" web/sites/default/settings.local.php
  sed -i "s/^# \(\$settings\['cache'\]\['bins'\]\['page'\] = 'cache\.backend\.null';\)/\1/" web/sites/default/settings.local.php
  sed -i "s/^# \(\$settings\['cache'\]\['bins'\]\['dynamic_page_cache'\] = 'cache\.backend\.null';\)/\1/" web/sites/default/settings.local.php
  sed -i "s/^# \(\$settings\['extension_discovery_scan_tests'\] = 'cache\.backend\.null';\)/\1/" web/sites/default/settings.local.php

  if ! grep -q "\$config['twig.config']['debug'] = TRUE;" web/sites/default/settings.local.php; then
    echo -e "\n/** Disable Twig Caching.*/\n\$config['twig.config']['debug'] = TRUE;\n\$config['twig.config']['auto_reload'] = TRUE;\n\$config['twig.config']['cache'] = FALSE;" >> web/sites/default/settings.local.php
  fi

  if ! grep -q "Include settings.local.php file';" web/sites/default/settings.php; then
    echo -e "\n/** Include settings.local.php file */\nif (file_exists(\$app_root . '/' . \$site_path . '/settings.local.php')) { include \$app_root . '/' . \$site_path . '/settings.local.php'; }" >> web/sites/default/settings.php
  fi
fi

if [ ! -f "web/sites/default/development.services.yml" ]; then
  cp web/sites/development.services.yml web/sites/default/development.services.yml
fi

sudo chown -R 1000:1000 /var/www/html/web/sites
sudo chmod 777 -R /var/www/html/web/sites

echo "Setup complete. Starting web server..."

# 9. Execute the original command for the container (start the Apache web server)
exec "$@"
