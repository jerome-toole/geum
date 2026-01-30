#!/bin/bash

# Rsync deploy script
# Reads config from wp-sync.yml, falls back to .env
# Usage: ./dev-scripts/deploy.sh <environment> [--dry-run]

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(dirname "$SCRIPT_DIR")"
CONFIG_FILE="$THEME_DIR/wp-sync.yml"
ENV_FILE="$THEME_DIR/.env"
THEME_NAME="sistermidnight-theme"

ENV="${1:-staging}"
DRY_RUN=""

if [[ "$2" == "--dry-run" ]] || [[ "$1" == "--dry-run" ]]; then
    DRY_RUN="--dry-run"
    if [[ "$1" == "--dry-run" ]]; then
        ENV="staging"
    fi
fi

# Parse wp-sync.yml for environment config
parse_yaml_env() {
    local env=$1
    local field=$2
    if [[ -f "$CONFIG_FILE" ]]; then
        grep -A 10 "^[[:space:]]*${env}:" "$CONFIG_FILE" 2>/dev/null | grep "^[[:space:]]*${field}:" | head -1 | sed 's/.*:[[:space:]]*//' | tr -d '"' | tr -d "'"
    fi
}

# Get value from .env file
get_env_var() {
    local var_name=$1
    if [[ -f "$ENV_FILE" ]]; then
        grep "^${var_name}=" "$ENV_FILE" 2>/dev/null | cut -d '=' -f2- | tr -d '"' | tr -d "'"
    fi
}

# Get config: try wp-sync.yml first, then .env
ENV_UPPER=$(echo "$ENV" | tr '[:lower:]' '[:upper:]')

HOST=$(parse_yaml_env "$ENV" "host")
[[ -z "$HOST" ]] && HOST=$(get_env_var "DEPLOY_${ENV_UPPER}_HOST")

PORT=$(parse_yaml_env "$ENV" "port")
[[ -z "$PORT" ]] && PORT=$(get_env_var "DEPLOY_${ENV_UPPER}_PORT")

REMOTE_PATH=$(parse_yaml_env "$ENV" "path")
[[ -z "$REMOTE_PATH" ]] && REMOTE_PATH=$(get_env_var "DEPLOY_${ENV_UPPER}_PATH")

if [[ -z "$HOST" ]] || [[ -z "$REMOTE_PATH" ]]; then
    echo "Error: Environment '$ENV' not configured"
    echo ""
    echo "Configure in wp-sync.yml:"
    echo "  environments:"
    echo "    $ENV:"
    echo "      host: user@hostname"
    echo "      port: 22  # optional"
    echo "      path: /path/to/wordpress"
    echo ""
    echo "Or in .env:"
    echo "  DEPLOY_${ENV_UPPER}_HOST=user@hostname"
    echo "  DEPLOY_${ENV_UPPER}_PORT=22"
    echo "  DEPLOY_${ENV_UPPER}_PATH=/path/to/wordpress"
    exit 1
fi

REMOTE_THEME_PATH="$REMOTE_PATH/wp-content/themes/$THEME_NAME"

# Build rsync command
RSYNC_OPTS="-avz --delete"
SSH_OPTS=""
if [[ -n "$PORT" ]]; then
    SSH_OPTS="-e 'ssh -p $PORT'"
fi

echo "Deploying to $ENV..."
echo "  Host: $HOST"
echo "  Path: $REMOTE_THEME_PATH"
if [[ -n "$DRY_RUN" ]]; then
    echo "  Mode: DRY RUN"
fi
echo ""

# Run rsync
eval rsync $RSYNC_OPTS $SSH_OPTS $DRY_RUN \
    --exclude-from="$THEME_DIR/.deployignore" \
    "$THEME_DIR/" \
    "$HOST:$REMOTE_THEME_PATH"

# Flush cache (skip on dry run)
if [[ -z "$DRY_RUN" ]]; then
    echo ""
    echo "Flushing WP cache..."
    if [[ -n "$PORT" ]]; then
        ssh -p "$PORT" "$HOST" "cd $REMOTE_PATH && wp cache flush" 2>/dev/null || echo "Cache flush skipped"
    else
        ssh "$HOST" "cd $REMOTE_PATH && wp cache flush" 2>/dev/null || echo "Cache flush skipped"
    fi
fi

echo ""
echo "Deploy to $ENV complete!"
