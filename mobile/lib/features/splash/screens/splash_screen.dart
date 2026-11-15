import 'dart:async';
import 'package:flutter/material.dart';
import 'package:video_player/video_player.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with TickerProviderStateMixin {
  late final AnimationController _controller;
  late final AnimationController _progressController;
  late final Animation<double> _scaleAnimation;
  late final Animation<double> _fadeAnimation;
  VideoPlayerController? _videoController;

  @override
  void initState() {
    super.initState();

    // Main entrance animations (fade & scale in)
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

    // Full Splash Animation Duration (4.6 seconds for complete Nexus vortex cycle)
    _progressController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 4600),
    );

    _progressController.addStatusListener((status) {
      if (status == AnimationStatus.completed && mounted) {
        Navigator.of(context).pushReplacement(
          PageRouteBuilder(
            transitionDuration: const Duration(milliseconds: 700),
            pageBuilder: (_, animation, __) => FadeTransition(
              opacity: animation,
              child: const MainNavigationShell(),
            ),
          ),
        );
      }
    });

    _controller.forward();
    _progressController.forward();
    _initVideo();
  }

  void _initVideo() {
    try {
      _videoController = VideoPlayerController.asset('assets/videos/nexus.mp4')
        ..initialize().then((_) {
          if (mounted) {
            _videoController?.setLooping(true);
            _videoController?.setVolume(0.0);
            _videoController?.play();
            setState(() {});
          }
        }).catchError((e) {
          debugPrint('Nexus splash video failed to initialize: $e');
        });
    } catch (e) {
      debugPrint('Nexus splash video exception: $e');
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    _progressController.dispose();
    _videoController?.dispose();
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
                      // Nexus Animated Orb with Centered Luxury Emblem
                      SizedBox(
                        width: 250,
                        height: 250,
                        child: Stack(
                          alignment: Alignment.center,
                          children: [
                            // 1. Live Nexus Video Particle Layer or fallback poster
                            if (_videoController != null && _videoController!.value.isInitialized)
                              SizedBox(
                                width: 250,
                                height: 250,
                                child: ClipOval(
                                  child: FittedBox(
                                    fit: BoxFit.cover,
                                    child: SizedBox(
                                      width: _videoController!.value.size.width,
                                      height: _videoController!.value.size.height,
                                      child: VideoPlayer(_videoController!),
                                    ),
                                  ),
                                ),
                              )
                            else
                              ClipOval(
                                child: Image.asset(
                                  'assets/images/nexus_poster.jpg',
                                  width: 250,
                                  height: 250,
                                  fit: BoxFit.cover,
                                ),
                              ),

                            // 2. Subtle Radial Vignette Gradient to blend particles into background
                            Container(
                              width: 250,
                              height: 250,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                gradient: RadialGradient(
                                  colors: [
                                    Colors.transparent,
                                    Colors.transparent,
                                    AppTheme.background.withValues(alpha: 0.5),
                                    AppTheme.background,
                                  ],
                                  stops: const [0.0, 0.60, 0.88, 1.0],
                                ),
                              ),
                            ),

                            // 3. 3D Purple Shopping Bag Emblem inside the glowing Nexus vortex
                            Container(
                              width: 125,
                              height: 125,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                boxShadow: [
                                  BoxShadow(
                                    color: const Color(0xFF7C3AED).withValues(alpha: 0.45),
                                    blurRadius: 28,
                                    spreadRadius: 2,
                                  ),
                                  BoxShadow(
                                    color: const Color(0xFF38BDF8).withValues(alpha: 0.35),
                                    blurRadius: 16,
                                  ),
                                ],
                              ),
                              child: Image.asset(
                                'assets/icons/app_logo_mark.png',
                                fit: BoxFit.contain,
                              ),
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 12),

                      // Metallic "Big Live Classified" Logo Typography
                      Container(
                        constraints: const BoxConstraints(maxWidth: 260),
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                        child: Image.asset(
                          'assets/icons/app_logo_text.png',
                          fit: BoxFit.contain,
                        ),
                      ),

                      const SizedBox(height: 16),

                      // Subtitle Pill
                      NebulaBadgePill(
                        label: 'PAKISTAN\'S PREMIER MARKETPLACE',
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
                    AnimatedBuilder(
                      animation: _progressController,
                      builder: (context, _) {
                        return Container(
                          width: 140,
                          height: 4,
                          decoration: BoxDecoration(
                            color: Colors.white.withValues(alpha: 0.08),
                            borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                          ),
                          alignment: Alignment.centerLeft,
                          child: FractionallySizedBox(
                            widthFactor: _progressController.value.clamp(0.05, 1.0),
                            child: Container(
                              decoration: BoxDecoration(
                                gradient: const LinearGradient(
                                  colors: [
                                    Color(0xFF38BDF8),
                                    Color(0xFF818CF8),
                                    Color(0xFFEC4899),
                                  ],
                                ),
                                borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                                boxShadow: const [
                                  BoxShadow(
                                    color: Color(0x6638BDF8),
                                    blurRadius: 10,
                                    spreadRadius: 1,
                                  ),
                                ],
                              ),
                            ),
                          ),
                        );
                      },
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
