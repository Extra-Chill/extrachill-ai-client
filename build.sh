#!/bin/bash

# ExtraChill AI Client - Production Build Script
# Creates a clean, production-ready ZIP package for WordPress deployment

set -e

# Get the directory where this script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Extract version from plugin header
VERSION=$(grep -m 1 "Version:" extrachill-ai-client.php | awk '{print $3}')
PLUGIN_SLUG="extrachill-ai-client"

echo "Building ${PLUGIN_SLUG} v${VERSION}..."

# Create dist directory if it doesn't exist
mkdir -p dist

# Clean previous build for this version
if [ -d "dist/${PLUGIN_SLUG}-${VERSION}" ]; then
    echo "Removing previous build..."
    rm -rf "dist/${PLUGIN_SLUG}-${VERSION}"
fi

if [ -f "dist/${PLUGIN_SLUG}-${VERSION}.zip" ]; then
    rm "dist/${PLUGIN_SLUG}-${VERSION}.zip"
fi

# Install production dependencies
echo "Installing production dependencies..."
composer install --no-dev --optimize-autoloader

# Create build directory
BUILD_DIR="dist/${PLUGIN_SLUG}-${VERSION}"
mkdir -p "$BUILD_DIR"

# Copy files, excluding those in .buildignore
echo "Copying files..."

# Read .buildignore patterns and build rsync exclude arguments
EXCLUDE_ARGS=""
if [ -f ".buildignore" ]; then
    while IFS= read -r pattern || [ -n "$pattern" ]; do
        # Skip empty lines and comments
        if [ -n "$pattern" ] && [[ ! "$pattern" =~ ^[[:space:]]*# ]]; then
            EXCLUDE_ARGS="$EXCLUDE_ARGS --exclude=$pattern"
        fi
    done < .buildignore
fi

# Copy all files except excluded ones
rsync -av --exclude="dist" --exclude=".git" $EXCLUDE_ARGS . "$BUILD_DIR/"

# Create ZIP file
echo "Creating ZIP archive..."
cd dist
zip -q -r "${PLUGIN_SLUG}-${VERSION}.zip" "${PLUGIN_SLUG}-${VERSION}"
cd ..

# Clean up build directory
rm -rf "$BUILD_DIR"

# Restore development dependencies
echo "Restoring development dependencies..."
composer install

echo ""
echo "✓ Build complete!"
echo "  Package: dist/${PLUGIN_SLUG}-${VERSION}.zip"
echo "  Version: ${VERSION}"
echo ""
echo "Ready for deployment to WordPress network."