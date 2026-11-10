import 'package:flutter/material.dart';

class AppTheme {
  // Prestige Luxury Color Palette (Matching Web Prestige Design)
  static const Color obsidian = Color(0xFF090D16);
  static const Color midnight = Color(0xFF0F172A);
  static const Color slateCard = Color(0xFF1E293B);

  // Champagne Gold Scale
  static const Color champagneLight = Color(0xFFF3E8C9);
  static const Color champagne = Color(0xFFD4AF37);
  static const Color champagneDark = Color(0xFFB89228);
  static const Color champagneAccent = Color(0xFFDFBD69);

  // Soft Eye-Friendly Background & Surfaces
  static const Color pearlBackground = Color(0xFFF8F9FC);
  static const Color surfaceWhite = Colors.white;
  static const Color textPrimary = Color(0xFF0F172A);
  static const Color textSecondary = Color(0xFF64748B);
  static const Color borderSubtle = Color(0xFFE2E8F0);
  static const Color borderGold = Color(0x33D4AF37); // Subtle gold outline

  // Semantic & Aliases for compatibility
  static const Color primaryNavy = obsidian;
  static const Color brandBlue = Color(0xFFB89228); // Luxury gold accent
  static const Color accentAmber = champagne;
  static const Color successGreen = Color(0xFF10B981);
  static const Color errorRed = Color(0xFFEF4444);

  // Spacing constants (8px grid scale)
  static const double space4 = 4.0;
  static const double space8 = 8.0;
  static const double space12 = 12.0;
  static const double space16 = 16.0;
  static const double space20 = 20.0;
  static const double space24 = 24.0;
  static const double space32 = 32.0;

  // Curvatures
  static const double radiusSmall = 10.0;
  static const double radiusMedium = 16.0;
  static const double radiusLarge = 24.0;
  static const double minTouchTarget = 48.0;

  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: champagne,
        primary: obsidian,
        secondary: champagne,
        surface: surfaceWhite,
      ),
      scaffoldBackgroundColor: pearlBackground,
      appBarTheme: const AppBarTheme(
        backgroundColor: obsidian,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: false,
        titleTextStyle: TextStyle(
          color: Colors.white,
          fontSize: 18,
          fontWeight: FontWeight.w800,
          letterSpacing: -0.2,
        ),
      ),
      cardTheme: CardTheme(
        color: surfaceWhite,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(radiusMedium),
          side: const BorderSide(color: borderSubtle, width: 1),
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: champagne,
          foregroundColor: obsidian,
          minimumSize: const Size(minTouchTarget, minTouchTarget),
          elevation: 2,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(radiusSmall),
          ),
          textStyle: const TextStyle(fontWeight: FontWeight.w800, letterSpacing: 0.5),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: surfaceWhite,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(radiusSmall),
          borderSide: const BorderSide(color: borderSubtle),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(radiusSmall),
          borderSide: const BorderSide(color: borderSubtle),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(radiusSmall),
          borderSide: const BorderSide(color: champagne, width: 2),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      ),
      navigationBarTheme: NavigationBarThemeData(
        backgroundColor: obsidian,
        indicatorColor: champagne.withValues(alpha: 0.25),
        labelTextStyle: WidgetStateProperty.resolveWith((states) {
          if (states.contains(WidgetState.selected)) {
            return const TextStyle(color: champagne, fontSize: 12, fontWeight: FontWeight.bold);
          }
          return const TextStyle(color: Colors.white60, fontSize: 12);
        }),
        iconTheme: WidgetStateProperty.resolveWith((states) {
          if (states.contains(WidgetState.selected)) {
            return const IconThemeData(color: champagne);
          }
          return const IconThemeData(color: Colors.white60);
        }),
      ),
    );
  }
}
