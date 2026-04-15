"""
CSS Cleanup Script
Scans all HTML/PHP files for used class names, then lists
CSS selectors that have no corresponding class in any HTML file.
"""

import re
import os

SRC_DIR = os.path.dirname(os.path.abspath(__file__))
HTML_FILES = ['index.html', 'projects.php', 'project_detail.php', 'veille.html']
CSS_FILE = 'style.css'

# ── 1. Collect every class name that appears in HTML/PHP files ──────────────
all_text = ""
for fname in HTML_FILES:
    path = os.path.join(SRC_DIR, fname)
    with open(path, 'r', encoding='utf-8') as f:
        all_text += f.read()

raw_attrs = re.findall(r'class=["\']([^"\']+)["\']', all_text)
used_classes: set[str] = set()
for attr in raw_attrs:
    for token in attr.split():
        used_classes.add(token)                  # keep as-is  (e.g. "group-hover\:scale-105")
        used_classes.add(token.replace('\\', '')) # also without backslash

# ── 2. Parse CSS into blocks ────────────────────────────────────────────────
css_path = os.path.join(SRC_DIR, CSS_FILE)
with open(css_path, 'r', encoding='utf-8') as f:
    css_raw = f.read()

# Split into top-level @-blocks vs plain rules using brace depth tracking
def split_css_top_level(text: str):
    """Yield (selector_or_at, block_body) tuples for every top-level rule."""
    depth = 0
    current_start = 0
    i = 0
    while i < len(text):
        ch = text[i]
        if ch == '{':
            depth += 1
        elif ch == '}':
            depth -= 1
            if depth == 0:
                chunk = text[current_start:i+1].strip()
                if chunk:
                    yield chunk
                current_start = i + 1
        i += 1

blocks = list(split_css_top_level(css_raw))

# ── 3. For each CSS class selector, check if it's used ──────────────────────
def classes_in_selector(selector: str) -> list[str]:
    """Return bare class names referenced by a CSS selector string."""
    # Match .classname (including escaped chars like \: \/ \.)
    return re.findall(r'\.([\w\-\\:\/\.]+)', selector)

always_keep_prefixes = ('@font-face', '@keyframes', ':root', '*', 'body',
                         'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a ', 'a{',
                         'button', 'img', 'pre', '::selection', '@media')

used_blocks   = []
unused_blocks = []

for block in blocks:
    # Separate selector from body
    brace_pos = block.index('{')
    selector = block[:brace_pos].strip()

    # Always keep @-rules and element selectors
    if any(selector.startswith(p) for p in always_keep_prefixes):
        used_blocks.append(block)
        continue

    # For @media blocks, recurse into inner rules
    if selector.startswith('@media') or selector.startswith('@keyframes'):
        used_blocks.append(block)
        continue

    # Check class names
    cls_names = classes_in_selector(selector)
    if not cls_names:
        # Pure element selector (e.g. "pre", ":root") – keep
        used_blocks.append(block)
        continue

    # A block is "used" if at least one of its class names is found in the HTML
    is_used = any(
        c in used_classes or c.replace('\\', '') in used_classes
        for c in cls_names
    )
    if is_used:
        used_blocks.append(block)
    else:
        unused_blocks.append(block)

# ── 4. Report ──────────────────────────────────────────────────────────────
print(f"\n{'='*60}")
print(f"  USED CLASSES found in HTML: {len(used_classes)}")
print(f"  CSS blocks kept  : {len(used_blocks)}")
print(f"  CSS blocks UNUSED: {len(unused_blocks)}")
print(f"{'='*60}\n")

if unused_blocks:
    print("UNUSED CSS RULES:")
    for b in unused_blocks:
        # Just print the selector line
        first_line = b.split('{')[0].strip()
        print(f"  - {first_line}")
else:
    print("No unused CSS rules found.")
