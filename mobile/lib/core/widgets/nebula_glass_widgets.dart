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
          if (showOrbs)
            const RepaintBoundary(
              child: _StaticOrbsLayer(),
            ),
          child,
        ],
      ),
    );
  }
}

class _StaticOrbsLayer extends StatelessWidget {
  const _StaticOrbsLayer();

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        // Purple bloom orb - top right
        Positioned(
          top: -60,
          right: -60,
          child: IgnorePointer(
            child: Container(
              width: 300,
              height: 300,
              decoration: const BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.glowPurple,
                boxShadow: [
                  BoxShadow(
                    color: AppTheme.glowPurple,
                    blurRadius: 70,
                    spreadRadius: 30,
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
              decoration: const BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.glowIndigo,
                boxShadow: [
                  BoxShadow(
                    color: AppTheme.glowIndigo,
                    blurRadius: 80,
                    spreadRadius: 30,
                  ),
                ],
              ),
            ),
          ),
        ),
        // Teal bloom orb - mid right
        Positioned(
          top: 360,
          right: -40,
          child: IgnorePointer(
            child: Container(
              width: 220,
              height: 220,
              decoration: const BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.glowTeal,
                boxShadow: [
                  BoxShadow(
                    color: AppTheme.glowTeal,
                    blurRadius: 60,
                    spreadRadius: 20,
                  ),
                ],
              ),
            ),
          ),
        ),
      ],
    );
  }
}

/// Translucent frosted glass card with hairline border and silky smooth performance
class NebulaGlassCard extends StatelessWidget {
  final Widget child;
  final EdgeInsetsGeometry? padding;
  final double? borderRadius;
  final VoidCallback? onTap;
  final Border? border;
  final Color? color;
  final bool useBlur;

  const NebulaGlassCard({
    super.key,
    required this.child,
    this.padding,
    this.borderRadius,
    this.onTap,
    this.border,
    this.color,
    this.useBlur = false,
  });

  @override
  Widget build(BuildContext context) {
    final radius = borderRadius ?? AppTheme.radius2Xl;

    final containerDecoration = BoxDecoration(
      color: color ?? AppTheme.surfaceGlass,
      gradient: const LinearGradient(
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
        colors: [
          Color(0x1AFFFFFF), // 10% translucent white highlight
          Color(0x08FFFFFF), // 3% deep glass fill
        ],
      ),
      borderRadius: BorderRadius.circular(radius),
      border: border ?? Border.all(color: AppTheme.borderHairline, width: 1),
      boxShadow: AppTheme.glassShadow,
    );

    Widget cardBody;
    if (useBlur) {
      cardBody = ClipRRect(
        borderRadius: BorderRadius.circular(radius),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 8, sigmaY: 8),
          child: Container(
            padding: padding ?? const EdgeInsets.all(AppTheme.cardPadding),
            decoration: containerDecoration,
            child: child,
          ),
        ),
      );
    } else {
      cardBody = Container(
        padding: padding ?? const EdgeInsets.all(AppTheme.cardPadding),
        decoration: containerDecoration,
        child: child,
      );
    }

    if (onTap != null) {
      return Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(radius),
          splashColor: AppTheme.primaryLavender.withValues(alpha: 0.12),
          highlightColor: AppTheme.primaryLavender.withValues(alpha: 0.06),
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

/// Signature Bottom Navigation Tab Bar with center elevated SELL CTA (OLX Style)
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
      decoration: BoxDecoration(
        color: const Color(0xF50F1420),
        border: Border(
          top: BorderSide(
            color: AppTheme.borderHairline.withValues(alpha: 0.8),
            width: 1.0,
          ),
        ),
        boxShadow: const [
          BoxShadow(
            color: Color(0x66000000),
            blurRadius: 20,
            offset: Offset(0, -5),
          ),
        ],
      ),
      child: SafeArea(
        top: false,
        child: SizedBox(
          height: 62,
          child: Stack(
            clipBehavior: Clip.none,
            alignment: Alignment.center,
            children: [
              Row(
                children: [
                  // Tab 0: Home
                  Expanded(
                    child: _buildTabItem(
                      index: 0,
                      activeIcon: Icons.home_filled,
                      inactiveIcon: Icons.home_outlined,
                      label: 'Home',
                    ),
                  ),

                  // Tab 1: Chats
                  Expanded(
                    child: _buildTabItem(
                      index: 1,
                      activeIcon: Icons.chat_bubble_rounded,
                      inactiveIcon: Icons.chat_bubble_outline_rounded,
                      label: 'Chats',
                    ),
                  ),

                  // Center Gap for SELL button
                  const SizedBox(width: 68),

                  // Tab 3: My Ads
                  Expanded(
                    child: _buildTabItem(
                      index: 3,
                      activeIcon: Icons.view_agenda_rounded,
                      inactiveIcon: Icons.view_agenda_outlined,
                      label: 'My Ads',
                    ),
                  ),

                  // Tab 4: Account
                  Expanded(
                    child: _buildTabItem(
                      index: 4,
                      activeIcon: Icons.person_rounded,
                      inactiveIcon: Icons.person_outline_rounded,
                      label: 'Account',
                    ),
                  ),
                ],
              ),

              // Center Raised SELL Button (OLX Style multi-color gradient ring + plus)
              Positioned(
                top: -18,
                child: GestureDetector(
                  onTap: onCenterFabPressed,
                  behavior: HitTestBehavior.opaque,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Container(
                        width: 54,
                        height: 54,
                        padding: const EdgeInsets.all(3.5),
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          gradient: const SweepGradient(
                            colors: [
                              Color(0xFF38BDF8), // Cyan
                              Color(0xFF34D399), // Mint
                              Color(0xFFFBBF24), // Amber/Yellow
                              Color(0xFF818CF8), // Indigo
                              Color(0xFF38BDF8), // Cyan
                            ],
                          ),
                          boxShadow: const [
                            BoxShadow(
                              color: Color(0x5538BDF8),
                              blurRadius: 14,
                              spreadRadius: 1,
                              offset: Offset(0, 3),
                            ),
                          ],
                        ),
                        child: Container(
                          decoration: const BoxDecoration(
                            shape: BoxShape.circle,
                            color: Color(0xFF0F1420),
                          ),
                          child: const Icon(
                            Icons.add_rounded,
                            size: 32,
                            color: Colors.white,
                          ),
                        ),
                      ),
                      const SizedBox(height: 3),
                      Text(
                        'SELL',
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.w900,
                          letterSpacing: 0.5,
                          color: selectedIndex == 2
                              ? const Color(0xFF38BDF8)
                              : Colors.white.withValues(alpha: 0.9),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTabItem({
    required int index,
    required IconData activeIcon,
    required IconData inactiveIcon,
    required String label,
  }) {
    final isActive = selectedIndex == index;

    return GestureDetector(
      onTap: () => onTabSelected(index),
      behavior: HitTestBehavior.opaque,
      child: Container(
        color: Colors.transparent,
        padding: const EdgeInsets.symmetric(vertical: 4),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            AnimatedContainer(
              duration: const Duration(milliseconds: 180),
              curve: Curves.easeOut,
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 2),
              decoration: BoxDecoration(
                color: isActive ? Colors.white.withValues(alpha: 0.08) : Colors.transparent,
                borderRadius: BorderRadius.circular(AppTheme.radiusFull),
              ),
              child: Icon(
                isActive ? activeIcon : inactiveIcon,
                size: 22,
                color: isActive ? const Color(0xFF38BDF8) : AppTheme.onSurfaceMuted,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                fontSize: 10,
                fontWeight: isActive ? FontWeight.w700 : FontWeight.w500,
                color: isActive ? const Color(0xFF38BDF8) : AppTheme.onSurfaceMuted,
                letterSpacing: 0.1,
              ),
              maxLines: 1,
            ),
          ],
        ),
      ),
    );
  }
}
