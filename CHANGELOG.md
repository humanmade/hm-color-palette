# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Post meta for storing color palette selection per post/page
- React component for the block editor sidebar panel
- Support for using the component in blocks with block attributes
- Body class injection based on selected palette (`has-{palette}-color-palette`)
- `hm_color_palette_options` filter for programmatic palette customization
- Uses active theme's `theme.json` color palette by default
- GitHub Actions workflows for automated build to `release` branch and tag-and-release
