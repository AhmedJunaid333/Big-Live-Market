import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';
import 'category_listings_screen.dart';

final dynamicCategoriesProvider = FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final client = ref.watch(apiClientProvider);
  try {
    final response = await client.dio.get('/categories');
    final data = response.data['data'] as List<dynamic>?;
    if (data != null && data.isNotEmpty) {
      final colorPalette = [
        AppTheme.accentPink,
        AppTheme.accentMint,
        AppTheme.accentBlue,
        AppTheme.accentIndigo,
        AppTheme.accentRose,
        AppTheme.accentAmber,
        AppTheme.primaryLavender,
        AppTheme.secondaryMint,
      ];

      final iconMap = <String, IconData>{
        'motors': Icons.directions_car_filled_rounded,
        'mobile-phones': Icons.smartphone_rounded,
        'property': Icons.apartment_rounded,
        'electronics-appliances': Icons.devices_other_rounded,
      };

      return data.asMap().entries.map((entry) {
        final i = entry.key;
        final item = entry.value as Map<String, dynamic>;
        final slug = item['slug']?.toString() ?? '';
        final id = (item['id'] ?? (i + 1)) as int;
        final name = item['name']?.toString() ?? 'Category';

        return {
          'id': id,
          'name': name,
          'icon': iconMap[slug] ?? Icons.category_rounded,
          'count': '${item['children']?.length ?? 1} Subcategories',
          'color': colorPalette[i % colorPalette.length],
        };
      }).toList();
    }
  } catch (_) {}
  return CategoriesScreen.defaultCategories;
});

class CategoriesScreen extends ConsumerWidget {
  const CategoriesScreen({super.key});

  static const List<Map<String, dynamic>> defaultCategories = [
    {
      'id': 1,
      'name': 'Motors & Cars',
      'icon': Icons.directions_car_filled_rounded,
      'count': 'Cars & Vehicles',
      'color': AppTheme.accentPink,
    },
    {
      'id': 3,
      'name': 'Mobile Phones',
      'icon': Icons.smartphone_rounded,
      'count': 'Smartphones & Tabs',
      'color': AppTheme.accentMint,
    },
    {
      'id': 5,
      'name': 'Luxury Property',
      'icon': Icons.apartment_rounded,
      'count': 'Houses & Commercial',
      'color': AppTheme.accentBlue,
    },
    {
      'id': 6,
      'name': 'Electronics & Tech',
      'icon': Icons.devices_other_rounded,
      'count': 'Gadgets & Home Tech',
      'color': AppTheme.accentIndigo,
    },
    {
      'id': 7,
      'name': 'Bikes & Cycles',
      'icon': Icons.two_wheeler_rounded,
      'count': 'Motorcycles & Scooters',
      'color': AppTheme.accentRose,
    },
    {
      'id': 8,
      'name': 'Watches & Jewels',
      'icon': Icons.watch_rounded,
      'count': 'Luxury Timepieces',
      'color': AppTheme.accentAmber,
    },
    {
      'id': 9,
      'name': 'Home & Furniture',
      'icon': Icons.chair_rounded,
      'count': 'Interior & Decor',
      'color': AppTheme.primaryLavender,
    },
    {
      'id': 10,
      'name': 'Jobs & Services',
      'icon': Icons.business_center_rounded,
      'count': 'Professional Services',
      'color': AppTheme.secondaryMint,
    },
  ];

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final categoriesAsync = ref.watch(dynamicCategoriesProvider);
    final categories = categoriesAsync.asData?.value ?? defaultCategories;

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
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
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
                    NebulaGlassIconButton(
                      icon: Icons.refresh_rounded,
                      color: AppTheme.primaryLavender,
                      onPressed: () {
                        ref.invalidate(dynamicCategoriesProvider);
                      },
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
                  itemCount: categories.length,
                  itemBuilder: (context, index) {
                    final cat = categories[index];
                    final color = cat['color'] as Color;

                    return NebulaGlassCard(
                      borderRadius: AppTheme.radius2Xl,
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                      onTap: () {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (_) => CategoryListingsScreen(
                              categoryId: (cat['id'] ?? 1) as int,
                              categoryName: cat['name'] as String,
                              icon: cat['icon'] as IconData,
                              accentColor: color,
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
