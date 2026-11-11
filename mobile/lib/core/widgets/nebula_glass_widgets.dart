import 'dart:ui';
import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

/// Cosmic background with multi-stop gradient and blurred ambient orbs
class NebulaAtmosphericBackground extends StatelessWidget {
  final Widget child;
  final bool showOrbs;

  const NebulaAtmosphericBackground({
    super.key,
    required this.child,
    this.showOrbs = true,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        gradient: AppTheme.cosmosGradient,
      ),
      child: Stack(
        fit: StackFit.expand,
        children: [
          if (showOrbs) ...[
            // Purple bloom orb - top right
            Positioned(
              top: -60,
              right: -60,
              child: IgnorePointer(
                child: Container(
                  width: 300,
                  height: 300,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppTheme.glowPurple,
                    boxShadow: const [
                      BoxShadow(
                        color: AppTheme.glowPurple,
                        blurRadius: 90,
                        spreadRadius: 40,
                      ),
                    ],
                  ),
                ),
              ),
            ),
            // Indigo bloom orb - bottom left
            Positioned(
              bottom: 80,
              left: -80,
              child: IgnorePointer(
                child: Container(
                  width: 320,
                  height: 320,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppTheme.glowIndigo,
                    boxShadow: const [
                      BoxShadow(
                        color: AppTheme.glowIndigo,
                        blurRadius: 100,
                        spreadRadius: 40,
                      ),
                    ],
                  ),
                ),
              ),
            ),
            // Teal bloom orb - mid right
            Positioned(
              top: MediaQuery.of(context).size.height * 0.45,
              right: -40,
              child: IgnorePointer(
                child: Container(
                  width: 220,
                  height: 220,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppTheme.glowTeal,
                    boxShadow: const [
                      BoxShadow(
                        color: AppTheme.glowTeal,
                        blurRadius: 80,
                        spreadRadius: 20,
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
          child,
        ],
      ),
    );
  }
}

/// Translucent frosted glass card with hairline border and backdrop blur
class NebulaGlassCard extends StatelessWidget {
  final Widget child;
  final EdgeInsetsGeometry? padding;
  final double? borderRadius;
  final VoidCallback? onTap;
  final Border? border;
  final Color? color;

  const NebulaGlassCard({
    super.key,
    required this.child,
    this.padding,
    this.borderRadius,
    this.onTap,
    this.border,
    this.color,
  });

  @override
  Widget build(BuildContext context) {
    final radius = borderRadius ?? AppTheme.radius2Xl;

    Widget cardBody = ClipRRect(
      borderRadius: BorderRadius.circular(radius),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 12, sigmaY: 12),
        child: Container(
          padding: padding ?? const EdgeInsets.all(AppTheme.cardPadding),
          decoration: BoxDecoration(
            color: color ?? AppTheme.surfaceGlass,
            borderRadius: BorderRadius.circular(radius),
            border: border ?? Border.all(color: AppTheme.borderHairline, width: 1),
            boxShadow: AppTheme.glassShadow,
          ),
          child: child,
        ),
      ),
    );

    if (onTap != null) {
      return Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(radius),
          onTap: onTap,
          child: cardBody,
        ),
      );
    }

    return cardBody;
  }
}

/// Signature CTA button: Gradient lavender, ultra-rounded (32px), tracked uppercase label, bloom shadow
class NebulaButtonPrimary extends StatelessWidget {
  final String label;
  final VoidCallback? onPressed;
  final Widget? icon;
  final bool isLoading;
  final EdgeInsetsGeometry? padding;
  final TextStyle? textStyle;

  NebulaButtonPrimary({
    super.key,
    required this.label,
    required this.onPressed,
    dynamic icon,
    this.isLoading = false,
    this.padding,
    this.textStyle,
  }) : icon = icon is IconData
            ? Icon(icon, size: 18, color: AppTheme.onPrimary)
            : (icon as Widget?);

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
        boxShadow: AppTheme.ctaGlowShadow,
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
          onTap: isLoading ? null : onPressed,
          child: Ink(
            padding: padding ?? const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
            decoration: BoxDecoration(
              gradient: AppTheme.primaryCtaGradient,
              borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
            ),
            child: Center(
              child: isLoading
                  ? const SizedBox(
                      width: 22,
                      height: 22,
                      child: CircularProgressIndicator(
                        strokeWidth: 2.5,
                        valueColor: AlwaysStoppedAnimation<Color>(AppTheme.onPrimary),
                      ),
                    )
                  : Row(
                      mainAxisSize: MainAxisSize.min,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        if (icon != null) ...[
                          icon!,
                          const SizedBox(width: 6),
                        ],
                        Flexible(
                          child: Text(
                            label.toUpperCase(),
                            style: textStyle ?? AppTheme.ctaLg.copyWith(fontSize: 15),
                            overflow: TextOverflow.ellipsis,
                            maxLines: 1,
                          ),
                        ),
                      ],
                    ),
            ),
          ),
        ),
      ),
    );
  }
}

/// Secondary button: Tonal lavender chip with hairline border
class NebulaButtonSecondary extends StatelessWidget {
  final String label;
  final VoidCallback? onPressed;
  final IconData? icon;
  final EdgeInsetsGeometry? padding;

  const NebulaButtonSecondary({
    super.key,
    required this.label,
    required this.onPressed,
    this.icon,
    this.padding,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(AppTheme.radiusSm),
        onTap: onPressed,
        child: Ink(
          padding: padding ?? const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
          decoration: BoxDecoration(
            color: AppTheme.primaryLavender.withValues(alpha: 0.20),
            borderRadius: BorderRadius.circular(AppTheme.radiusSm),
            border: Border.all(
              color: AppTheme.primaryLavender.withValues(alpha: 0.30),
              width: 1,
            ),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              if (icon != null) ...[
                Icon(icon, size: 16, color: AppTheme.primaryLavender),
                const SizedBox(width: 4),
              ],
              Flexible(
                child: Text(
                  label,
                  style: AppTheme.labelMd,
                  overflow: TextOverflow.ellipsis,
                  maxLines: 1,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

/// Icon button with translucent glass styling (40px)
class NebulaGlassIconButton extends StatelessWidget {
  final IconData icon;
  final VoidCallback? onPressed;
  final double size;
  final double iconSize;
  final Color? color;
  final String? tooltip;

  const NebulaGlassIconButton({
    super.key,
    required this.icon,
    required this.onPressed,
    this.size = 40.0,
    this.iconSize = 22.0,
    this.color,
    this.tooltip,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: AppTheme.surfaceGlass,
        border: Border.all(color: AppTheme.borderHairline, width: 1),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          customBorder: const CircleBorder(),
          onTap: onPressed,
          child: Icon(
            icon,
            size: iconSize,
            color: color ?? AppTheme.onSurfaceVariant,
          ),
        ),
      ),
    );
  }
}

/// Tinted Icon Bubble: size-8 to size-10 rounded-full pill with `bg-[accent]/20 text-[accent]`
class TintedIconBubble extends StatelessWidget {
  final IconData icon;
  final Color accentColor;
  final double size;
  final double iconSize;

  const TintedIconBubble({
    super.key,
    required this.icon,
    Color? tint,
    Color? accentColor,
    this.size = 40.0,
    this.iconSize = 20.0,
  }) : accentColor = tint ?? accentColor ?? AppTheme.primaryLavender;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: accentColor.withValues(alpha: 0.20),
        border: Border.all(
          color: accentColor.withValues(alpha: 0.25),
          width: 1,
        ),
      ),
      alignment: Alignment.center,
      child: Icon(
        icon,
        size: iconSize,
        color: accentColor,
      ),
    );
  }
}

enum NebulaBadgeTone { mint, lavender, cyan, pink }

/// Status Badge Pill: Mint status indicator with uppercase tracking
class NebulaBadgePill extends StatelessWidget {
  final String label;
  final Color color;

  const NebulaBadgePill({
    super.key,
    String? label,
    String? text,
    Color? color,
    NebulaBadgeTone? tone,
  })  : label = text ?? label ?? '',
        color = color ??
            (tone == NebulaBadgeTone.cyan
                ? AppTheme.accentCyan
                : tone == NebulaBadgeTone.lavender
                    ? AppTheme.primaryLavender
                    : tone == NebulaBadgeTone.pink
                        ? AppTheme.accentPink
                        : AppTheme.secondaryMint);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.20),
        borderRadius: BorderRadius.circular(AppTheme.radiusFull),
        border: Border.all(
          color: color.withValues(alpha: 0.25),
          width: 1,
        ),
      ),
      child: Text(
        label.toUpperCase(),
        style: AppTheme.labelUppercase.copyWith(
          color: color,
        ),
      ),
    );
  }
}

/// Circular Avatar with optional presence dot
class NebulaAvatar extends StatelessWidget {
  final String? initials;
  final String? imageUrl;
  final double size;
  final bool isOnline;

  const NebulaAvatar({
    super.key,
    this.initials,
    this.imageUrl,
    this.size = 48.0,
    this.isOnline = false,
  });

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        Container(
          width: size,
          height: size,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: AppTheme.surfaceGlass,
            border: Border.all(
              color: Colors.white.withValues(alpha: 0.20),
              width: 1,
            ),
            boxShadow: const [
              BoxShadow(
                color: Color(0x4D000000),
                blurRadius: 12,
                offset: Offset(0, 4),
              ),
            ],
          ),
          alignment: Alignment.center,
          child: Text(
            initials ?? 'U',
            style: TextStyle(
              color: AppTheme.primaryLavender,
              fontWeight: FontWeight.w700,
              fontSize: size * 0.42,
            ),
          ),
        ),
        if (isOnline)
          Positioned(
            top: 0,
            right: 0,
            child: Container(
              width: 12,
              height: 12,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.secondaryMint,
                border: Border.all(
                  color: AppTheme.backgroundMid,
                  width: 2,
                ),
              ),
            ),
          ),
      ],
    );
  }
}

/// Signature Floating Glass Tab Bar with center elevated CTA
class FloatingGlassTabBar extends StatelessWidget {
  final int selectedIndex;
  final ValueChanged<int> onTabSelected;
  final VoidCallback onCenterFabPressed;

  const FloatingGlassTabBar({
    super.key,
    required this.selectedIndex,
    required this.onTabSelected,
    required this.onCenterFabPressed,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(AppTheme.gutter, 0, AppTheme.gutter, 24),
      height: 64,
      child: Stack(
        clipBehavior: Clip.none,
        alignment: Alignment.center,
        children: [
          // Glass Bar Container
          ClipRRect(
            borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
            child: BackdropFilter(
              filter: ImageFilter.blur(sigmaX: 16, sigmaY: 16),
              child: Container(
                height: 64,
                padding: const EdgeInsets.symmetric(horizontal: 8),
                decoration: BoxDecoration(
                  color: AppTheme.backgroundMid.withValues(alpha: 0.85),
                  borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
                  border: Border.all(color: AppTheme.borderHairline, width: 1),
                  boxShadow: const [
                    BoxShadow(
                      color: Color(0x80000000),
                      blurRadius: 50,
                      offset: Offset(0, 25),
                    ),
                  ],
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,
                  children: [
                    _buildTabItem(0, Icons.home_rounded, 'Home'),
                    _buildTabItem(1, Icons.grid_view_rounded, 'Categories'),
                    const SizedBox(width: 56), // Gap for center floating FAB
                    _buildTabItem(3, Icons.chat_bubble_rounded, 'Chats'),
                    _buildTabItem(4, Icons.person_rounded, 'Profile'),
                  ],
                ),
              ),
            ),
          ),

          // Center FAB button that breaks the top edge
          Positioned(
            top: -20,
            child: GestureDetector(
              onTap: onCenterFabPressed,
              child: Container(
                width: 56,
                height: 56,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: AppTheme.primaryCtaGradient,
                  border: Border.all(
                    color: AppTheme.background,
                    width: 4,
                  ),
                  boxShadow: const [
                    BoxShadow(
                      color: Color(0x66D8B4FE), // rgba(216,180,254,0.40)
                      blurRadius: 20,
                      spreadRadius: 2,
                    ),
                  ],
                ),
                child: const Icon(
                  Icons.add_circle_rounded,
                  size: 32,
                  color: AppTheme.onPrimary,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTabItem(int index, IconData icon, String tooltip) {
    final isActive = selectedIndex == index;

    return GestureDetector(
      onTap: () => onTabSelected(index),
      behavior: HitTestBehavior.opaque,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        width: 48,
        height: 48,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: isActive ? Colors.white.withValues(alpha: 0.10) : Colors.transparent,
          boxShadow: isActive ? AppTheme.activeTabGlow : null,
        ),
        child: Icon(
          icon,
          size: 24,
          color: isActive ? AppTheme.onSurface : AppTheme.onSurfaceMuted,
        ),
      ),
    );
  }
}
