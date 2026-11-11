# Nebula Glass Mobile Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Overhaul the Big Live Marketplace Flutter mobile application with the complete Nebula Glass Mobile design system featuring deep-cosmos gradients, atmospheric blur orbs, translucent frosted-glass cards, neon data accents, Manrope typography, and a floating glass tab bar.

**Architecture:** Implement design tokens and theme extensions in `core/theme/app_theme.dart`, build reusable glassmorphism primitives (`NebulaAtmosphericBackground`, `NebulaGlassCard`, `NebulaButtonPrimary`, `FloatingGlassTabBar`, `TintedIconBubble`) in `core/widgets/nebula_glass_widgets.dart`, and overhaul each feature screen to consume the new design system.

**Tech Stack:** Flutter 3.27+, Dart 3.6+, `flutter_riverpod`, `google_fonts`, Material 3 with custom glassmorphic canvas and shaders.

---

### Task 1: Package Dependencies & Font Setup
**Files:**
- Modify: `mobile/pubspec.yaml`

**Step 1: Write pubspec dependency additions**
Add `google_fonts: ^6.2.1` to `mobile/pubspec.yaml` dependencies.

**Step 2: Run dependency resolution**
Run: `cd mobile; flutter pub get`
Expected: Resolves dependencies and exits with code 0.

---

### Task 2: Core Design System Tokens in `AppTheme`
**Files:**
- Modify: `mobile/lib/core/theme/app_theme.dart`
- Test: `mobile/test/nebula_glass_test.dart`

**Step 1: Write test for Nebula Glass theme tokens**
Verify that all color tokens (`background`, `backgroundMid`, `backgroundDeep`, `surfaceGlass`, `borderHairline`, `primaryLavender`, `secondaryMint`, `accentPink`, `accentMint`, etc.), radius tokens, and theme data are defined and accessible.

**Step 2: Run test to verify it fails**
Run: `cd mobile; flutter test test/nebula_glass_test.dart`
Expected: FAIL (AppTheme constants missing).

**Step 3: Implement `AppTheme` with Nebula Glass specifications**
- Colors:
  - `background = Color(0xFF0F0C29)`
  - `backgroundMid = Color(0xFF1A103C)`
  - `backgroundDeep = Color(0xFF2E1065)`
  - `surface = Color(0xFF17153B)`
  - `surfaceGlass = Color(0x0DFFFFFF)` (`rgba(255,255,255,0.05)`)
  - `borderHairline = Color(0x1AFFFFFF)` (`rgba(255,255,255,0.10)`)
  - `onSurface = Color(0xFFFFFFFF)`
  - `onSurfaceVariant = Color(0x99FFFFFF)` (`60%`)
  - `onSurfaceMuted = Color(0x66FFFFFF)` (`40%`)
  - `onSurfaceFaint = Color(0x4DFFFFFF)` (`30%`)
  - `primaryLavender = Color(0xFFD8B4FE)`
  - `onPrimary = Color(0xFF1A103C)`
  - `secondaryMint = Color(0xFF99F6E4)`
  - `onSecondary = Color(0xFF0F172A)`
  - `accentPink = Color(0xFFF9A8D4)`
  - `accentMint = Color(0xFF6EE7B7)`
  - `accentBlue = Color(0xFF93C5FD)`
  - `accentIndigo = Color(0xFF818CF8)`
  - `accentRose = Color(0xFFF87171)`
  - `accentAmber = Color(0xFFFCD34D)`
  - Glows: `glowPurple`, `glowIndigo`, `glowTeal`
- Typography:
  - Manrope integration with exact weight/tracking specs (`displayLg`, `headlineMd`, `numeralXl`, `numeralLg`, `bodyMd`, `bodySm`, `labelMd`, `labelSm`, `caption`, `labelUppercase`, `ctaLg`).
- Radii:
  - `radiusSm = 12.0`, `radiusMd = 16.0`, `radiusLg = 22.0`, `radiusXl = 24.0`, `radius2Xl = 32.0`, `radiusFull = 9999.0`
- Spacing:
  - `gutter = 24.0`, `cardPadding = 24.0`, `cardPaddingSm = 20.0`, `sectionGap = 24.0`

**Step 4: Run test to verify it passes**
Run: `cd mobile; flutter test test/nebula_glass_test.dart`
Expected: PASS.

---

### Task 3: Reusable Nebula Glass Component Library
**Files:**
- Create: `mobile/lib/core/widgets/nebula_glass_widgets.dart`
- Test: `mobile/test/nebula_glass_test.dart`

**Step 1: Write tests for reusable glass components**
- `NebulaAtmosphericBackground` (renders gradient + ambient blur orbs)
- `NebulaGlassCard` (renders frosted container with border and blur)
- `NebulaButtonPrimary` (renders gradient button with uppercase tracked font and bloom shadow)
- `NebulaButtonSecondary` (tonal lavender button)
- `NebulaGlassIconButton` (40px glass circle button)
- `NebulaBadgePill` (status pill with tracking)
- `TintedIconBubble` (accent colored bubble with icon)
- `NebulaAvatar` (circular avatar with presence indicator)
- `FloatingGlassTabBar` (floating navigation bar)

**Step 2: Run test to verify it fails**
Run: `cd mobile; flutter test test/nebula_glass_test.dart`
Expected: FAIL (widgets not found).

**Step 3: Implement components in `mobile/lib/core/widgets/nebula_glass_widgets.dart`**
Follow the exact YAML frontmatter and CSS recipes:
- `NebulaAtmosphericBackground`: Deep gradient (`#0F0C29` to `#2E1065`) with 3 blurred atmospheric orbs (top-right purple, bottom-left indigo, mid teal).
- `NebulaGlassCard`: `ClipRRect(borderRadius: 32px)`, `BackdropFilter(sigmaX: 12, sigmaY: 12)`, `color: rgba(255,255,255,0.05)`, `border: 1px rgba(255,255,255,0.10)`, shadow `0 8px 32px rgba(0,0,0,0.20)`.
- `NebulaButtonPrimary`: Gradient `from #D8B4FE to #C084FC`, text `#1A103C`, `ctaLg` font (Manrope 18px w800 uppercase, letterSpacing 0.04em), shadow `0 10px 30px rgba(216,180,254,0.30)`.
- `NebulaButtonSecondary`: `bg: rgba(216,180,254,0.20)`, border `1px rgba(216,180,254,0.30)`, text `#D8B4FE`, label-md typography.
- `NebulaGlassIconButton`: `40px` glass circle, `solar/material` icon.
- `NebulaBadgePill`: `bg: rgba(153,246,228,0.20)`, border `rgba(153,246,228,0.20)`, text `#99F6E4`, uppercase tracking `0.12em`.
- `TintedIconBubble`: `size 36-40` pill with `bg: accent/20`, icon `accent`.
- `NebulaAvatar`: `size 48`, border `rgba(255,255,255,0.20)`, presence dot `12px` `#99F6E4`.

**Step 4: Run test to verify it passes**
Run: `cd mobile; flutter test test/nebula_glass_test.dart`
Expected: PASS.

---

### Task 4: Splash Screen Redesign
**Files:**
- Modify: `mobile/lib/features/splash/screens/splash_screen.dart`

**Step 1: Update Splash Screen implementation**
- Use `NebulaAtmosphericBackground` with animated celestial bloom orbs.
- Center brand emblem with glowing lavender border, metallic/gradient 'B', and tracked uppercase tagline in `labelUppercase` ("DEEP COSMOS MARKETPLACE").
- Smooth transition to `MainNavigationShell`.

**Step 2: Verify visually and structurally with widget test**
Run: `cd mobile; flutter test`
Expected: PASS.

---

### Task 5: Main Navigation Shell & Home Feed Overhaul
**Files:**
- Modify: `mobile/lib/main.dart`
- Modify: `mobile/test/widget_test.dart`

**Step 1: Update `MainNavigationShell` to Floating Glass Tab Bar**
- Implement signature `FloatingGlassTabBar`:
  - 24px margins from left, right, and bottom.
  - Height 64px, rounded `32px` (`2rem`), fill `#1A103C` at 80% opacity with `BackdropFilter`.
  - 5 tab items: Home, Categories, Sell (elevated center button with lavender gradient and bloom glow breaking the top edge), Chats, Profile.
  - Active tab item gets `rgba(255,255,255,0.10)` pill with white glow.

**Step 2: Update `HomeFeedScreen`**
- Top App Bar: Flat, eyebrow "EXPLORE CATALOG" in `labelUppercase`, title "Big Live" in `displayLg` (Manrope 30px w700).
- Search Bar: Glass container with hint "Search cars, mobiles, luxury villas...", prefix search icon, filter action.
- Listing Cards: `NebulaGlassCard` with:
  - Glass photo placeholder with neon condition tag (`accentMint` / `accentAmber`).
  - Title in `headlineMd` / `bodyMd` w700.
  - Price in `numeralLg` (20px w700) with lavender/mint glow.
  - Location in `bodySm` muted.

**Step 3: Update `test/widget_test.dart` and verify**
Run: `cd mobile; flutter test test/widget_test.dart`
Expected: PASS.

---

### Task 6: Categories Screen Overhaul
**Files:**
- Modify: `mobile/lib/features/categories/screens/categories_screen.dart`

**Step 1: Implement Glass Grid Categories**
- Full `NebulaAtmosphericBackground`.
- Flat cosmic header "ALL CATEGORIES" + "Explore 8 Departments".
- 2-column grid of `NebulaGlassCard`:
  - `TintedIconBubble` for each category with alternating data accents (`accentPink`, `accentMint`, `accentBlue`, `accentAmber`, `accentIndigo`).
  - Category name in `headlineMd` (16px w600 white).
  - Ad count in `caption` (`onSurfaceMuted`).

**Step 2: Run test verification**
Run: `cd mobile; flutter test`
Expected: PASS.

---

### Task 7: Listing Detail Screen Overhaul
**Files:**
- Modify: `mobile/lib/features/listings/screens/listing_detail_screen.dart`

**Step 1: Implement Glass Listing Detail**
- Hero media preview with glass reflection and neon condition badge.
- Price in `numeralXl` (24px w700) with glowing accent.
- Title and location with uppercase metadata tags.
- Verified seller card in `NebulaGlassCard` with `NebulaAvatar` and green verified badge.
- Floating bottom glass CTA bar with Call & Chat buttons using `NebulaButtonSecondary` and `NebulaButtonPrimary`.

**Step 2: Run test verification**
Run: `cd mobile; flutter test`
Expected: PASS.

---

### Task 8: Post Ad Screen Overhaul
**Files:**
- Modify: `mobile/lib/features/listings/screens/post_ad_screen.dart`

**Step 1: Implement Glass Post Ad Form**
- Cosmic header with eyebrow "SELL AN ITEM".
- Photo upload slot: dashed glass border (`borderHairline`) with hover/focus state.
- Form inputs styled as `NebulaInputText` with glass fill and lavender focus ring.
- Category selection chips inside a glass tray.
- Submit CTA using `NebulaButtonPrimary` ("PUBLISH ADVERT").

**Step 2: Run test verification**
Run: `cd mobile; flutter test`
Expected: PASS.

---

### Task 9: Chat List & Chat Detail Screens Overhaul
**Files:**
- Modify: `mobile/lib/features/chat/screens/chat_list_screen.dart`
- Modify: `mobile/lib/features/chat/screens/chat_detail_screen.dart`

**Step 1: Implement Glass Chat List**
- Glass thread cards with `NebulaAvatar`, presence indicator, last message preview, and neon unread badge pill.

**Step 2: Implement Glass Chat Detail**
- Translucent chat bubble list (user bubble in lavender glass tint, recipient in frosted glass).
- Glass bottom composer bar with input and send icon button.

**Step 3: Run test verification**
Run: `cd mobile; flutter test`
Expected: PASS.

---

### Task 10: Profile Screen & Login Modal Overhaul
**Files:**
- Modify: `mobile/lib/features/profile/screens/profile_screen.dart`
- Modify: `mobile/lib/features/auth/screens/login_screen.dart`

**Step 1: Implement Glass Profile**
- User header card with `NebulaAvatar`, verified badge, and email/phone in `bodySm`.
- Quick stats 3-column glass cards (Active Ads, Inquiries, Rating).
- Menu items as glass list rows with chevron indicators.

**Step 2: Implement Glass Login Modal**
- Glass card dialog / sheet with cosmic background, glass input fields, role selector chips, and signature gradient CTA button.

**Step 3: Run test verification**
Run: `cd mobile; flutter test`
Expected: PASS.

---

### Task 11: Regression Testing & Visual Quality Assurance
**Files:**
- Test: `mobile/test/widget_test.dart`
- Test: `mobile/test/nebula_glass_test.dart`

**Step 1: Run complete test suite**
Run: `cd mobile; flutter test`
Expected: All tests pass without warnings or regressions.
