#! /bin/bash -e

#
#   Output colours
#

GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

#
#   Install WordPress
#

# -----------------------------------------------------------------------------
# Geum Site Setup Script
#
# This script clears the terminal, displays a formatted header for the Geum Site
# Setup, and prompts the user to confirm whether to run the setup process.
# -----------------------------------------------------------------------------
clear
# Display script description

echo -e "${GREEN}"
echo "+----------------------+"
echo "| Geum Site Setup      |"
echo "+----------------------+"
echo -e "${NC}"

echo -e "${BLUE}This script will configure your WordPress site for development by enabling debugging, removing default content, setting up language and permalinks, disabling comments, and activating plugins and the current theme.${NC}"
echo

read -r -p $'\e[34mRun setup? (Y/n) \e[0m' run

if [ "$run" == "n" ]; then
    exit
fi

echo -e "${GREEN}Running setup...${NC}"

printf '\n'
echo "Enabling debugging..."
wp config set WP_DEBUG true --raw
wp config set WP_DEBUG_LOG true --raw
wp config set WP_DEBUG_DISPLAY false --raw
wp config set WP_ENVIRONMENT_TYPE "development"
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Deleting default plugins, content and older themes..."

# Use || true to prevent script exit if items don't exist
wp post delete $(wp post list --post_type=page --posts_per_page=1 --post_status=publish --pagename="sample-page" --field=ID --format=ids 2>/dev/null) --quiet 2>/dev/null || true
wp post delete $(wp post list --post_type=post --posts_per_page=1 --post_status=publish --name="hello-world" --field=ID --format=ids 2>/dev/null) --quiet 2>/dev/null || true

wp plugin delete akismet hello --quiet 2>/dev/null || true
wp theme delete twentytwentytwo twentytwentythree twentytwentyfour --quiet 2>/dev/null || true

echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Discouraging search engines..."
wp option update blog_public 0 --quiet
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Activating English (UK) language..."
wp language core install en_GB --quiet
wp site switch-language en_GB --quiet
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Disabling comments/pings..."

wp post list --post_type=page --format=ids | xargs -r wp post update --comment_status=closed --ping_status=closed --quiet 2>/dev/null || true

wp option update default_pingback_flag "" --quiet
wp option update default_ping_status "" --quiet
wp option update default_comment_status "" --quiet
wp option update require_name_email 1 --quiet
wp option update comment_registration 1 --quiet
wp option update close_comments_days_old 0 --quiet
wp option update show_comments_cookies_opt_in "" --quiet
wp option update comments_notify 1 --quiet
wp option update moderation_notify 1 --quiet
wp option update comment_moderation 1 --quiet
wp option update comment_previously_approved "" --quiet
wp option update comment_max_links 0 --quiet

echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Setting default permalink structure..."
wp rewrite structure '/%postname%/' --hard --quiet
wp rewrite flush --hard --quiet
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Creating homepage..."
wp post create --post_type=page --post_title=Home --post_status=publish --menu_order=-1 --quiet
wp option update show_on_front "page" --quiet
wp option update page_on_front $(wp post list --post_type=page --post_status=publish --posts_per_page=1 --pagename=home --field=ID --format=ids) --quiet
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Activating all plugins..."
wp plugin activate --all --skip-themes --quiet
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Activating theme..."
wp theme activate "$(basename "$(pwd)")"
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "█ █▄░█ █▀ ▀█▀ ▄▀█ █░░ █░░   █▀▀ █▀█ █▀▄▀█ █▀█ █░░ █▀▀ ▀█▀ █▀▀"
echo "█ █░▀█ ▄█ ░█░ █▀█ █▄▄ █▄▄   █▄▄ █▄█ █░▀░█ █▀▀ █▄▄ ██▄ ░█░ ██▄"
printf '\n'
