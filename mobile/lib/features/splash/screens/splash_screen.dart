import 'dart:async';
import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _scaleAnimation;
  late final Animation<double> _fadeAnimation;
  Timer? _timer;

  @override
  void initState() {
    super.initState();

    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1400),
    );

    _scaleAnimation = CurvedAnimation(
      parent: _controller,
      curve: Curves.easeOutBack,
    );

    _fadeAnimation = CurvedAnimation(
      parent: _controller,
      curve: Curves.easeIn,
    );

    _controller.forward();

    _timer = Timer(const Duration(milliseconds: 2400), () {
      if (mounted) {
        Navigator.of(context).pushReplacement(
          PageRouteBuilder(
            transitionDuration: const Duration(milliseconds: 600),
            pageBuilder: (_, animation, __) => FadeTransition(
              opacity: animation,
              child: const MainNavigationShell(),
            ),
          ),
        );
      }
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: NebulaAtmosphericBackground(
        child: Stack(
          children: [
            // Central Animated Brand Content
            Center(
              child: FadeTransition(
                opacity: _fadeAnimation,
                child: ScaleTransition(
                  scale: _scaleAnimation,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Lavender Gradient Glowing Emblem
                      Container(
                        width: 90,
                        height: 90,
                        decoration: BoxDecoration(
                          gradient: AppTheme.primaryCtaGradient,
                          borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
                          boxShadow: const [
                            BoxShadow(
                              color: Color(0x66D8B4FE), // rgba(216,180,254,0.40)
                              blurRadius: 36,
                              spreadRadius: 4,
                            ),
                          ],
                          border: Border.all(
                            color: Colors.white.withValues(alpha: 0.3),
                            width: 1.5,
                          ),
                        ),
                        alignment: Alignment.center,
                        child: const Text(
                          'B',
                          style: TextStyle(
                            fontSize: 48,
                            fontWeight: FontWeight.w900,
                            color: AppTheme.onPrimary,
                            letterSpacing: -1,
                          ),
                        ),
                      ),

                      const SizedBox(height: 24),

                      // App Title
                      Text.rich(
                        TextSpan(
                          style: AppTheme.displayLg.copyWith(fontSize: 26),
                          children: const [
                            TextSpan(text: 'Big Live '),
                            TextSpan(
                              text: 'Market',
                              style: TextStyle(
                                color: AppTheme.primaryLavender,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 12),

                      // Subtitle Pill
                      NebulaBadgePill(
                        label: 'DEEP COSMOS MARKETPLACE',
                        color: AppTheme.secondaryMint,
                      ),
                    ],
                  ),
                ),
              ),
            ),

            // Bottom Progress Indicator & Metadata
            Positioned(
              bottom: 48,
              left: 0,
              right: 0,
              child: FadeTransition(
                opacity: _fadeAnimation,
                child: Column(
                  children: [
                    Container(
                      width: 64,
                      height: 4,
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.05),
                        borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                      ),
                      child: Container(
                        width: 48,
                        decoration: BoxDecoration(
                          color: AppTheme.accentPink,
                          borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                          boxShadow: const [
                            BoxShadow(
                              color: Color(0x66F9A8D4),
                              blurRadius: 10,
                              spreadRadius: 1,
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'NEBULA GLASS EDITION · v1.0.0',
                      style: AppTheme.labelUppercase.copyWith(
                        color: AppTheme.onSurfaceMuted,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
