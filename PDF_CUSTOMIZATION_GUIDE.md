# 🎨 PDF CUSTOMIZATION GUIDE

## Overview
This guide explains how to customize the PDF output when using Pandoc to convert the documentation.

## Common Options

### Layout
- `-V geometry:margin=1in` (Standard Margins)
- `-V papersize=a4` (Paper Size)
- `-V fontsize=11pt` (Readable Font)

### Styling
- `-V mainfont="Arial"` (Set Font)
- `-V documentclass=report` (Chapter based layout)
- `--highlight-style=tango` (Code block colors)

### Navigation
- `--toc` (Add Table of Contents)
- `--toc-depth=3` (Depth of TOC)
- `--number-sections` (1.1, 1.2 numbering)
- `-V colorlinks=true` (Clickable links)

## Example Commands

**Professional Report:**
```bash
pandoc input.md -o output.pdf \
  --pdf-engine=xelatex \
  -V geometry:margin=1in \
  --toc --number-sections
```

**Print Friendly (Black & White):**
```bash
pandoc input.md -o output.pdf \
  --pdf-engine=xelatex \
  -V colorlinks=false
```

See `convert-professional.ps1` for the working implementation.
