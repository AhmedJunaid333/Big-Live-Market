import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  // Atmospheric / Ground Palette (Used only as page gradient & floating tab-bar fill)
  static const Color background = Color(0xFF0F0C29);
  static const Color backgroundMid = Color(0xFF1A103C);
  static const Color backgroundDeep = Color(0xFF2E1065);

  // Glass / Chrome Palette (Used for cards, inputs, chips, borders)
  static const Color surface = Color(0xFF17153B);
  static const Color surfaceGlass = Color(0x0DFFFFFF); // rgba(255,255,255,0.05)
  static const Color borderHairline = Color(0x1AFFFFFF); // rgba(255,255,255,0.10)
  static const Color onSurface = Color(0xFFFFFFFF);
  static const Color onSurfaceVariant = Color(0x99FFFFFF); // rgba(255,255,255,0.60)
  static const Color onSurfaceMuted = Color(0x66FFFFFF); // rgba(255,255,255,0.40)
  static const Color onSurfaceFaint = Color(0x4DFFFFFF); // rgba(255,255,255,0.30)

  // Brand Palette
  static const Color primaryLavender = Color(0xFFD8B4FE);
  static const Color primary = primaryLavender;
  static const Color onPrimary = Color(0xFF1A103C);
  static const Color secondaryMint = Color(0xFF99F6E4);
  static const Color secondary = secondaryMint;
  static const Color onSecondary = Color(0xFF0F172A);

  // Data Palette (Neon Accents - Rings, Bars, Tinted Bubbles, Medallions)
  static const Color accentPink = Color(0xFFF9A8D4);
  static const Color accentMint = Color(0xFF6EE7B7);
  static const Color accentBlue = Color(0xFF93C5FD);
  static const Color accentIndigo = Color(0xFF818CF8);
  static const Color accentRose = Color(0xFFF87171);
  static const Color accentAmber = Color(0xFFFCD34D);

  // Glow Orbs & Atmosphere Blooms
  static const Color glowPurple = Color(0x33A855F7); // rgba(168,85,247,0.20)
  static const Color glowIndigo = Color(0x336366F1); // rgba(99,102,241,0.20)
  static const Color glowTeal = Color(0x1A14B8A6); // rgba(20,184,166,0.10)

  // Status Semantics
  static const Color success = Color(0xFF34D399);
  static const Color error = Color(0xFFF87171);
  static const Color warning = Color(0xFFFCD34D);

  // Backward Compatibility Aliases
  static const Color obsidian = background;
  static const Color midnight = backgroundMid;
  static const Color slateCard = surface;
  static const Color champagne = primaryLavender;
  static const Color champagneLight = primaryLavender;
  static const Color champagneDark = primaryLavender;
  static const Color champagneAccent = accentPink;
  static const Color pearlBackground = background;
  static const Color surfaceWhite = surface;
  static const Color textPrimary = onSurface;
  static const Color textSecondary = onSurfaceVariant;
  static const Color borderSubtle = borderHairline;
  static const Color borderGold = borderHairline;
  static const Color primaryNavy = backgroundMid;
  static const Color brandBlue = primaryLavender;
  static const Color successGreen = success;
  static const Color errorRed = error;

  // Curvatures
  static const double radiusSm = 12.0;
  static const double radiusMd = 16.0;
  static const double radiusLg = 22.0;
  static const double radiusXl = 24.0;
  static const double radius2Xl = 32.0;
  static const double radiusFull = 9999.0;

  // Aliases for radius
  static const double radiusSmall = radiusSm;
  static const double radiusMedium = radiusMd;
  static const double radiusLarge = radius2Xl;
  static const double minTouchTarget = 48.0;

  // Spacing
  static const double space4 = 4.0;
  static const double space8 = 8.0;
  static const double space12 = 12.0;
  static const double space16 = 16.0;
  static const double space20 = 20.0;
  static const double space24 = 24.0;
  static const double space32 = 32.0;

  static const double gutter = 24.0;
  static const double cardPadding = 24.0;
  static const double cardPaddingSm = 20.0;
  static const double sectionGap = 24.0;

  // Typography (Manrope with exact weights & tracking)
  static TextStyle get displayLg => GoogleFonts.manrope(
        fontSize: 30,
        fontWeight: FontWeight.w700,
        height: 36 / 30,
        letterSpacing: -0.02 * 30,
        color: onSurface,
      );

  static TextStyle get headlineMd => GoogleFonts.manrope(
        fontSize: 18,
        fontWeight: FontWeight.w500,
        height: 24 / 18,
        letterSpacing: -0.01 * 18,
        color: onSurface,
      );

  static TextStyle get numeralXl => GoogleFonts.manrope(
        fontSize: 24,
        fontWeight: FontWeight.w700,
        height: 28 / 24,
        letterSpacing: -0.02 * 24,
        color: onSurface,
      );

  static TextStyle get numeralLg => GoogleFonts.manrope(
        fontSize: 20,
        fontWeight: FontWeight.w700,
        height: 24 / 20,
        letterSpacing: -0.01 * 20,
        color: onSurface,
      );

  static TextStyle get bodyMd => GoogleFonts.manrope(
        fontSize: 14,
        fontWeight: FontWeight.w500,
        height: 20 / 14,
        letterSpacing: 0,
        color: onSurface,
      );

  static TextStyle get bodySm => GoogleFonts.manrope(
        fontSize: 12,
        fontWeight: FontWeight.w500,
        height: 16 / 12,
        letterSpacing: 0,
        color: onSurfaceVariant,
      );

  static TextStyle get labelMd => GoogleFonts.manrope(
        fontSize: 13,
        fontWeight: FontWeight.w700,
        height: 16 / 13,
        letterSpacing: 0,
        color: primaryLavender,
      );

  static TextStyle get labelSm => GoogleFonts.manrope(
        fontSize: 11,
        fontWeight: FontWeight.w600,
        height: 14 / 11,
        letterSpacing: 0,
        color: onSurfaceVariant,
      );

  static TextStyle get caption => GoogleFonts.manrope(
        fontSize: 10,
        fontWeight: FontWeight.w500,
        height: 14 / 10,
        letterSpacing: 0,
        color: onSurfaceMuted,
      );

  static TextStyle get labelUppercase => GoogleFonts.manrope(
        fontSize: 10,
        fontWeight: FontWeight.w700,
        height: 14 / 10,
        letterSpacing: 0.12 * 10,
        color: onSurfaceVariant,
      );

  static TextStyle get ctaLg => GoogleFonts.manrope(
        fontSize: 18,
        fontWeight: FontWeight.w800,
        height: 22 / 18,
        letterSpacing: 0.04 * 18,
        color: onPrimary,
      );

  // Gradients
  static const LinearGradient cosmosGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [
      background,
      backgroundMid,
      backgroundDeep,
    ],
  );

  static const LinearGradient primaryCtaGradient = LinearGradient(
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
    colors: [
      Color(0xFFD8B4FE),
      Color(0xFFC084FC),
    ],
  );

  // Shadows
  static const List<BoxShadow> glassShadow = [
    BoxShadow(
      color: Color(0x33000000), // rgba(0,0,0,0.20)
      blurRadius: 32,
      offset: Offset(0, 8),
    ),
  ];

  static const List<BoxShadow> ctaGlowShadow = [
    BoxShadow(
      color: Color(0x4DD8B4FE), // rgba(216,180,254,0.30)
      blurRadius: 30,
      offset: Offset(0, 10),
    ),
  ];

  static const List<BoxShadow> activeTabGlow = [
    BoxShadow(
      color: Color(0x1AFFFFFF), // rgba(255,255,255,0.10)
      blurRadius: 15,
      spreadRadius: 1,
    ),
  ];

  static ThemeData get darkTheme {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.dark,
      colorScheme: const ColorScheme.dark(
        primary: primaryLavender,
        onPrimary: onPrimary,
        secondary: secondaryMint,
        onSecondary: onSecondary,
        surface: surface,
        onSurface: onSurface,
        error: error,
      ),
      scaffoldBackgroundColor: background,
      textTheme: GoogleFonts.manropeTextTheme(
        ThemeData.dark().textTheme,
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: Colors.transparent,
        foregroundColor: onSurface,
        elevation: 0,
        centerTitle: false,
      ),
      cardTheme: CardTheme(
        color: surfaceGlass,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(radius2Xl),
          side: const BorderSide(color: borderHairline, width: 1),
        ),
      ),
    );
  }

  // Set default lightTheme to return our darkTheme since Nebula Glass is dark-mode only!
  static ThemeData get lightTheme => darkTheme;
}
