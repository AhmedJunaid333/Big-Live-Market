import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';
import 'listing_detail_screen.dart';

/// Interactive Radar Screen for finding deals within 1 to 25 km radius
class NearbyRadarScreen extends ConsumerStatefulWidget {
  const NearbyRadarScreen({super.key});

  @override
  ConsumerState<NearbyRadarScreen> createState() => _NearbyRadarScreenState();
}

class _NearbyRadarScreenState extends ConsumerState<NearbyRadarScreen> with SingleTickerProviderStateMixin {
  double _radiusKm = 5.0;
  late AnimationController _pulseController;

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat();
  }

  @override
  void dispose() {
    _pulseController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final listingsAsync = ref.watch(listingsFeedProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Column(
            children: [
              // Header
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 12),
                child: Row(
                  children: [
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_ios_new_rounded,
                      onPressed: () => Navigator.pop(context),
                      tooltip: 'Back',
                    ),
                    const SizedBox(width: 14),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('GEOFENCE RADAR', style: AppTheme.labelUppercase),
                        Text('Nearby Deals', style: AppTheme.headlineMd),
                      ],
                    ),
                    const Spacer(),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: AppTheme.secondaryMint.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                        border: Border.all(color: AppTheme.secondaryMint.withValues(alpha: 0.4)),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.radar_rounded, size: 14, color: AppTheme.secondaryMint),
                          const SizedBox(width: 4),
                          Text(
                            '${_radiusKm.toInt()} km',
                            style: AppTheme.labelSm.copyWith(color: AppTheme.secondaryMint, fontWeight: FontWeight.bold),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              // Animated Pulsating Radar Canvas
              SizedBox(
                height: 180,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    AnimatedBuilder(
                      animation: _pulseController,
                      builder: (context, child) {
                        return CustomPaint(
                          size: const Size(180, 180),
                          painter: _RadarWavePainter(progress: _pulseController.value),
                        );
                      },
                    ),
                    Container(
                      width: 52,
                      height: 52,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: AppTheme.secondaryMint.withValues(alpha: 0.2),
                        border: Border.all(color: AppTheme.secondaryMint, width: 2),
                      ),
                      child: const Icon(Icons.my_location_rounded, color: AppTheme.secondaryMint, size: 26),
                    ),
                    Positioned(
                      top: 24,
                      right: 60,
                      child: _dealPin(label: 'iPhone 15', price: 'PKR 435k'),
                    ),
                    Positioned(
                      bottom: 30,
                      left: 70,
                      child: _dealPin(label: 'Honda Civic', price: 'PKR 8.8M'),
                    ),
                  ],
                ),
              ),

              // Slider for Range Selection
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 8),
                child: Column(
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Scan Radius Range:', style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceVariant)),
                        Text('${_radiusKm.toStringAsFixed(0)} KM', style: AppTheme.labelMd.copyWith(color: AppTheme.secondaryMint, fontWeight: FontWeight.bold)),
                      ],
                    ),
                    SliderTheme(
                      data: SliderTheme.of(context).copyWith(
                        activeTrackColor: AppTheme.secondaryMint,
                        inactiveTrackColor: AppTheme.borderHairline,
                        thumbColor: AppTheme.secondaryMint,
                        overlayColor: AppTheme.secondaryMint.withValues(alpha: 0.2),
                      ),
                      child: Slider(
                        min: 1.0,
                        max: 25.0,
                        divisions: 24,
                        value: _radiusKm,
                        onChanged: (val) => setState(() => _radiusKm = val),
                      ),
                    ),
                  ],
                ),
              ),

              // Nearby Listings List
              Expanded(
                child: listingsAsync.when(
                  data: (listings) {
                    return ListView.separated(
                      physics: const BouncingScrollPhysics(),
                      padding: const EdgeInsets.all(AppTheme.gutter),
                      itemCount: listings.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 12),
                      itemBuilder: (ctx, idx) {
                        final item = listings[idx] as Map<String, dynamic>;
                        final title = item['title']?.toString() ?? 'Listing';
                        final dist = ((idx + 1) * 1.2).toStringAsFixed(1);
                        final priceMinor = (item['price_minor'] ?? 0) as num;
                        final price = 'PKR ${(priceMinor / 100).toStringAsFixed(0)}';

                        return RepaintBoundary(
                          child: NebulaGlassCard(
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (_) => ListingDetailScreen(listing: item),
                                ),
                              );
                            },
                            child: Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    color: AppTheme.secondaryMint.withValues(alpha: 0.15),
                                    borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                                    border: Border.all(color: AppTheme.secondaryMint.withValues(alpha: 0.3)),
                                  ),
                                  child: Column(
                                    children: [
                                      const Icon(Icons.near_me_rounded, color: AppTheme.secondaryMint, size: 22),
                                      const SizedBox(height: 4),
                                      Text('$dist km', style: AppTheme.caption.copyWith(color: AppTheme.secondaryMint, fontWeight: FontWeight.bold)),
                                    ],
                                  ),
                                ),
                                const SizedBox(width: 14),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(title, style: AppTheme.headlineMd, maxLines: 1, overflow: TextOverflow.ellipsis),
                                      const SizedBox(height: 4),
                                      Text(price, style: AppTheme.priceSm),
                                    ],
                                  ),
                                ),
                                const Icon(Icons.chevron_right_rounded, color: AppTheme.onSurfaceVariant),
                              ],
                            ),
                          ),
                        );
                      },
                    );
                  },
                  loading: () => const Center(child: CircularProgressIndicator(color: AppTheme.primaryLavender)),
                  error: (e, _) => Center(child: Text('Failed to load radar: $e')),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _dealPin({required String label, required String price}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: AppTheme.backgroundMid.withValues(alpha: 0.9),
        borderRadius: BorderRadius.circular(AppTheme.radiusFull),
        border: Border.all(color: AppTheme.secondaryMint),
      ),
      child: Text('$label • $price', style: const TextStyle(color: AppTheme.secondaryMint, fontSize: 10, fontWeight: FontWeight.bold)),
    );
  }
}

class _RadarWavePainter extends CustomPainter {
  final double progress;
  _RadarWavePainter({required this.progress});

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final maxRadius = size.width / 2;

    for (int i = 0; i < 3; i++) {
      final p = (progress + i * 0.33) % 1.0;
      final radius = p * maxRadius;
      final paint = Paint()
        ..color = AppTheme.secondaryMint.withValues(alpha: (1.0 - p) * 0.35)
        ..style = PaintingStyle.stroke
        ..strokeWidth = 1.5;
      canvas.drawCircle(center, radius, paint);
    }
  }

  @override
  bool shouldRepaint(_RadarWavePainter oldDelegate) => oldDelegate.progress != progress;
}
