#!/bin/bash

# IntelliExport: AI Data Preparation Tool

CONFIG_FILE="config.ini"
DEBUG=false
VIEWS=()

# Function to display usage information
show_usage() {
    echo "Usage: ./intelliexport.sh [options] <database:view1> [database:view2] ..."
    echo "Options:"
    echo "  -c, --config <file>     Specify a custom config file (default: config.ini)"
    echo "  -d, --debug             Enable debug mode"
    echo "  -h, --help              Show this help message"
    echo "Arguments:"
    echo "  database:view           Specify the database and view to export"
    echo "                          If no database is specified, 'default' will be used"
    exit 1
}

# Function to parse arguments
parse_args() {
    while [[ $# -gt 0 ]]; do
        case $1 in
            -c|--config)
                CONFIG_FILE="$2"
                shift 2
                ;;
            -d|--debug)
                DEBUG=true
                shift
                ;;
            -h|--help)
                show_usage
                ;;
            *)
                if [[ $1 == *:* ]]; then
                    VIEWS+=("$1")
                else
                    VIEWS+=("default:$1")
                fi
                shift
                ;;
        esac
    done
}

# Main execution
parse_args "$@"

if [ ${#VIEWS[@]} -eq 0 ]; then
    echo "Error: No views specified."
    show_usage
fi

# Prepare arguments for PHP script
php_args=("$CONFIG_FILE")

if [ "$DEBUG" = true ]; then
    php_args+=("--debug")
fi

for view in "${VIEWS[@]}"; do
    php_args+=("$view")
done

# Execute the PHP script
echo "Exporting data from views: ${VIEWS[@]}"
export WRAPPER_SCRIPT=true
php "$(dirname "$0")/main.php" "${php_args[@]}"