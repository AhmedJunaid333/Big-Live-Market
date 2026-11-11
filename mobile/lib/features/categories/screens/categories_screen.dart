import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';

class CategoriesScreen extends ConsumerWidget {
  const CategoriesScreen({super.key});

  static const List<Map<String, dynamic>> _catalogCategories = [
    {
      'name': 'Motors & Cars',
      'icon': Icons.directions_car_filled_rounded,
      'count': '1,420+ Ads',
      'color': AppTheme.accentPink,
    },
    {
      'name': 'Mobile Phones',
      'icon': Icons.smartphone_rounded,
      'count': '3,890+ Ads',
      'color': AppTheme.accentMint,
    },
    {
      'name': 'Luxury Property',
      'icon': Icons.apartment_rounded,
      'count': '940+ Ads',
      'color': AppTheme.accentBlue,
    },
    {
      'name': 'Electronics & Tech',
      'icon': Icons.devices_other_rounded,
      'count': '2,150+ Ads',
      'color': AppTheme.accentIndigo,
    },
    {
      'name': 'Bikes & Cycles',
      'icon': Icons.two_wheeler_rounded,
      'count': '870+ Ads',
      'color': AppTheme.accentRose,
    },
    {
      'name': 'Watches & Jewels',
      'icon': Icons.watch_rounded,
      'count': '430+ Ads',
      'color': AppTheme.accentAmber,
    },
    {
      'name': 'Home & Furniture',
      'icon': Icons.chair_rounded,
      'count': '1,120+ Ads',
      'color': AppTheme.primaryLavender,
    },
    {
      'name': 'Jobs & Services',
      'icon': Icons.business_center_rounded,
      'count': '680+ Ads',
      'color': AppTheme.secondaryMint,
    },
  ];

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          bottom: false,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Cosmic Header
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 16, AppTheme.gutter, 16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'EXPLORE DEPARTMENTS',
                      style: AppTheme.labelUppercase,
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Categories',
                      style: AppTheme.displayLg,
                    ),
                  ],
                ),
              ),

              // 2-Up Glass Category Grid
              Expanded(
                child: GridView.builder(
                  padding: const EdgeInsets.fromLTRB(
                    AppTheme.gutter,
                    8,
                    AppTheme.gutter,
                    110, // Reserve padding to clear floating tab bar
                  ),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 2,
                    crossAxisSpacing: 14,
                    mainAxisSpacing: 14,
                    childAspectRatio: 0.84,
                  ),
                  itemCount: _catalogCategories.length,
                  itemBuilder: (context, index) {
                    final cat = _catalogCategories[index];
                    final color = cat['color'] as Color;

                    return NebulaGlassCard(
                      borderRadius: AppTheme.radius2Xl,
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                      onTap: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            backgroundColor: AppTheme.backgroundMid,
                            content: Text(
                              'Browsing ${cat['name']} catalog...',
                              style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
                            ),
                          ),
                        );
                      },
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          TintedIconBubble(
                            icon: cat['icon'] as IconData,
                            accentColor: color,
                            size: 46,
                            iconSize: 24,
                          ),
                          const SizedBox(height: 10),
                          Flexible(
                            child: Text(
                              cat['name'] as String,
                              textAlign: TextAlign.center,
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: AppTheme.bodyMd.copyWith(
                                fontWeight: FontWeight.w700,
                                fontSize: 13,
                                height: 1.25,
                                color: AppTheme.onSurface,
                              ),
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            cat['count'] as String,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: AppTheme.caption.copyWith(
                              color: AppTheme.onSurfaceMuted,
                            ),
                          ),
                        ],
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
