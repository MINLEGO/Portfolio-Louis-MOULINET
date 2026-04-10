# Design System Specification: The Technical Architect

## 1. Overview & Creative North Star
**Creative North Star: "The Digital Blueprint"**
This design system moves away from the "junior portfolio" template and adopts the aesthetic of a high-end IDE mixed with editorial engineering journals. The goal is to present a first-year IT student not as a learner, but as a **Technical Architect** in the making. 

We achieve this through a "Blueprint" layout strategy: intentional asymmetry, oversized monospaced typography, and a depth model that mimics layered code environments. The interface should feel like a high-performance dashboard—precise, organized, and deeply technical, yet softened by sophisticated glassmorphism.

---

## 2. Colors: Tonal Depth & The "No-Line" Rule
The palette is rooted in a deep "Obsidian" foundation with high-energy technical accents, transitioning to a refined light theme as per the technical tokens.

### The Foundation
- **Background (`#f4f4f4`):** The absolute base. All depth is built *up* from here. (Reflects `neutral_color_hex` from tokens)
- **Primary / Electric Blue (`#81ecff`):** Used for "Active State" signals and critical data points. (Reflects `primary_color_hex` from tokens)
- **Tertiary / Mint Green (`#afffd1`):** Reserved for "Success," "Compiled," or "Valid" states, evoking a terminal terminal vibe. (Reflects `tertiary_color_hex` from tokens)

### The "No-Line" Rule
Standard 1px solid borders are strictly prohibited for sectioning. Structural boundaries must be defined through:
1. **Background Shifts:** Placing a `surface-container-low` section against a `surface` background.
2. **Glassmorphism:** Using semi-transparent fills with `backdrop-blur` (e.g., 12px to 20px) to define headers or floating cards.
3. **Tonal Transitions:** Using the `surface-container` tiers (Lowest to Highest) to create "nested" depth.

### Signature Textures
Apply a subtle linear gradient to main CTAs or Hero backgrounds:
*   `Primary (#81ecff)` to `Primary-Container (#00e3fd)` at a 135-degree angle. This adds "visual soul" and prevents the UI from looking flat or "bootstrap-heavy."

---

## 3. Typography: The Monospace Edge
Typography is the primary driver of the "Coding" identity. We use a high-contrast scale to create an editorial feel.

- **Display & Headlines (Space Grotesk):** Chosen for its geometric, futuristic proportions. (Reflects `headline_font` from tokens)
    - *Usage:* `display-lg` (3.5rem) should be used for hero statements, often with tight letter-spacing (-0.02em) to feel authoritative.
- **Titles & Body (Inter):** A workhorse sans-serif for maximum readability. (Reflects `body_font` from tokens)
    - *Usage:* `body-lg` (1rem) for project descriptions. Keep line-height generous (1.6) to ensure the technical content remains approachable.
- **Labels (Manrope):** Used for micro-copy and metadata (e.g., "Language: Java"). (Reflects `label_font` from tokens)
- **The "Code-Injection" Technique:** For a signature look, use a monospaced font (like JetBrains Mono) for specific `Title-SM` elements to mimic variable names in a codebase (e.g., `const project_name = "CloudScale"`).

---

## 4. Elevation & Depth: Tonal Layering
We reject traditional drop shadows in favor of **Tonal Layering**.

- **The Layering Principle:** 
    - **Base:** `surface` (#f4f4f4)
    - **Sections:** `surface-container-low` (#e8e8e8)
    - **Cards/Floating Elements:** `surface-container-high` (#dcdcdc)
- **Ambient Shadows:** Only use shadows for "Actionable" floating elements (like Modals or Hovered Cards). Use a diffused shadow: `y: 20px, blur: 40px, color: rgba(0, 0, 0, 0.4)`. Avoid grey shadows; ensure the shadow color is a darker tint of the background.
- **The "Ghost Border" Fallback:** If accessibility requires a stroke, use `outline-variant` (#c8c8c8) at **15% opacity**. It should be felt, not seen.
- **Glassmorphism:** Navigation bars and sticky headers must use `surface-container` colors at 70% opacity with a `saturate(180%) blur(15px)` backdrop filter.

---

## 5. Components: Precision Engineering

### Buttons
- **Primary:** Gradient fill (`Primary` to `Primary-Container`). `Roundedness-sharp` (0rem). No border.
- **Secondary:** Transparent background with a `Ghost Border` (15% opacity `outline-variant`). On hover, transition to 10% `primary` opacity.
- **Tertiary:** Text-only in `Primary` color. Use for low-emphasis actions like "Read More."

### Technical Chips (Skills/Tags)
- Use `surface-variant` for the background and `on-surface-variant` for text. 
- **Style:** Small caps or Monospaced font for the label to emphasize the technical nature (Git, Docker, Python).
- **Radius:** `full` (9999px) for a "Pill" look that contrasts against the sharper grid.

### Cards & Projects
- **Strict Rule:** Forbid divider lines. Use `surface-container-highest` background shifts and vertical whitespace (32px - 48px) to separate content blocks.
- **Technical Header:** Each project card should start with a "Breadcrumb" style label (e.g., `01 / BTS-SIO / Development`) using `label-sm` in `Primary` color.

### Input Fields
- **Default:** `surface-container-lowest` background with an `outline-variant` ghost border. 
- **Focus State:** Border color shifts to `Primary`, and a subtle `Primary` outer glow (4px blur).

### Code Blocks (Portfolio Specific)
- Use `surface-container-lowest` with a left-side accent border of 2px in `Tertiary` (Mint Green). This signals "Stable/Success" code.

---

## 6. Do's and Don'ts

### Do
- **Do** use asymmetrical margins. For example, a 60% width column for text balanced by a 40% empty space or a decorative technical icon.
- **Do** use `primary-dim` for hover states on links to create a sophisticated transition.
- **Do** use "Technical Icons" (Docker, Git) as large, low-opacity background watermarks (5% opacity) to fill white space.

### Don't
- **Don't** use pure white (#FFFFFF). Always use `on-surface` (#212121, inferred light-mode contrast for a light background) to reduce eye strain in light mode.
- **Don't** use standard "Drop Shadows" on cards. Use the Tonal Layering method described in Section 4.
- **Don't** use icons with varying stroke weights. Stick to a single technical icon set (e.g., Phosphor Icons in "Light" weight) to maintain professional consistency.
- **Don't** center-align long blocks of text. Stick to left-alignment to mimic the structure of a code editor.

---

## 7. Roundedness Scale
To maintain the "Modern & Professional" look, we use a restrained corner radius:
- **Default (Cards/Inputs):** `0rem` (Sharp, professional)
- **Large (Containers):** `0rem`
- **Pills (Chips/Badges):** `9999px` (Full)

This mix of sharp and pill-shaped elements prevents the design from feeling too "soft" or "playful," keeping it firmly in the "Competent & Organized" brand persona.