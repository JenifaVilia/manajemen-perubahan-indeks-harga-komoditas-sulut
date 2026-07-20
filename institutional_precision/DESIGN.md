---
name: Institutional Precision
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#40484f'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#707880'
  outline-variant: '#c0c7d0'
  surface-tint: '#006496'
  primary: '#004d75'
  on-primary: '#ffffff'
  primary-container: '#006699'
  on-primary-container: '#bfe0ff'
  inverse-primary: '#90cdff'
  secondary: '#745b00'
  on-secondary: '#ffffff'
  secondary-container: '#fecb00'
  on-secondary-container: '#6e5700'
  tertiary: '#124d6f'
  on-tertiary: '#ffffff'
  tertiary-container: '#316589'
  on-tertiary-container: '#bde0ff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#cce5ff'
  primary-fixed-dim: '#90cdff'
  on-primary-fixed: '#001e31'
  on-primary-fixed-variant: '#004b72'
  secondary-fixed: '#ffe08b'
  secondary-fixed-dim: '#f1c100'
  on-secondary-fixed: '#241a00'
  on-secondary-fixed-variant: '#584400'
  tertiary-fixed: '#cae6ff'
  tertiary-fixed-dim: '#9bccf4'
  on-tertiary-fixed: '#001e30'
  on-tertiary-fixed-variant: '#0e4b6d'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  display:
    fontFamily: Inter
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  mono-data:
    fontFamily: monospace
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  sidebar_width: 260px
  container_max_width: 1440px
  gutter: 1.5rem
  margin_mobile: 1rem
  margin_desktop: 2rem
  stack_sm: 0.5rem
  stack_md: 1rem
  stack_lg: 1.5rem
---

## Brand & Style
The design system is engineered for high-integrity data visualization and institutional reporting. It prioritizes clarity, density, and professional authority, catering to policymakers, researchers, and data analysts. 

The aesthetic leans into **Corporate Modernism** with a focus on functional efficiency. The interface utilizes a rigorous information hierarchy where data is the protagonist. The emotional response is one of stability, transparency, and meticulous accuracy. Visual flourishes are minimized to reduce cognitive load, ensuring that complex statistical datasets remain legible and actionable.

## Colors
The palette is anchored by the institutional blue, signifying trust and official capacity. The secondary gold is used exclusively for strategic accents—highlighting key data points, primary calls to action, or critical status indicators. 

A comprehensive grayscale supports the structural framework, utilizing cool-toned grays to define boundaries without competing with data ink. System alerts utilize high-chroma red and pink for anomaly detection, while a forest green handles validation and positive trends. Backgrounds use a very subtle off-white to reduce eye strain during long-term data monitoring.

## Typography
This design system utilizes a systematic application of Inter to achieve a neutral, utilitarian feel. A "Data Mono" style is introduced for tabular figures to ensure vertical alignment in statistical columns.

Headlines are tight and bold to provide clear section anchoring. Body text is optimized for readability at 14px (md) for standard forms and 12px (sm) for dense data environments. Labels utilize a slight tracking increase and uppercase styling to differentiate metadata from actual data values.

## Layout & Spacing
The layout follows a **Fixed Sidebar + Fluid Content** model. The sidebar remains docked to the left, housing the primary navigation hierarchy. The main content area utilizes a responsive grid that adjusts column counts based on the viewport.

- **Desktop:** 12-column grid with 24px gutters. Content is contained within a max-width of 1440px to prevent excessive line lengths on ultra-wide monitors.
- **Tablet:** 8-column grid with 16px gutters. Sidebar collapses into an icon-only rail or a hamburger menu.
- **Mobile:** 4-column grid with 16px margins. Cards stack vertically, and data tables transition to a horizontal scroll or card-based summary.

## Elevation & Depth
Depth is communicated through **Tonal Layering** and crisp, low-opacity borders rather than heavy shadows. This maintains a "flat" professional look while defining clear container boundaries.

- **Level 0 (Canvas):** The base background layer (#F8FAFC).
- **Level 1 (Cards/Sidebar):** White surfaces with a 1px border (#E2E8F0).
- **Level 2 (Popovers/Modals):** White surfaces with a subtle, diffused ambient shadow (8px blur, 4% opacity black) to suggest separation from the primary data plane.
- **Active States:** Subtle inset shadows or 2px primary-colored left-borders for indicating the current page in the sidebar.

## Shapes
The shape language is conservative and geometric. A "Soft" rounding (4px) is applied to standard UI elements like buttons, input fields, and cards. This provides a modern touch without sacrificing the serious, institutional character of the design system. Larger containers like modals may use the 8px (lg) rounding to appear more approachable, but data-heavy table rows and chart segments maintain sharp or minimally rounded edges to maximize precision.

## Components
### KPI Cards
Standardized containers for high-level metrics. They must include a clear label, the primary value in `display` typography, and a trend indicator (percentage change) using status colors.

### Data Tables
The core of the system. Use alternating "zebra" striping (very subtle gray) for readability. Headers must be "sticky" and use the `label-md` style with vertical separators. Numerical data should be right-aligned and use the `mono-data` font.

### Charts & Visualization
Use a dedicated categorical palette that begins with the Primary Blue. Avoid gradients in bars or areas; use solid fills with 80% opacity to ensure grid lines remain visible.

### Buttons
- **Primary:** Solid Primary Blue with white text.
- **Secondary:** Outlined Primary Blue with 1px stroke.
- **Accent:** Solid Gold with dark blue text for urgent or "New" actions.

### Input Fields
Rectangular with a 1px gray border. On focus, the border transitions to Primary Blue with a 2px outer "halo" of the same color at 10% opacity. Labels are always persistent above the field.               