import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../screens/category_listings_screen.dart';

/// OLX-style visual category model
class CategoryButtonItem {
  final int id;
  final String label;
  final IconData icon;
  final Color tint;
  final String subtitle;

  const CategoryButtonItem({
    required this.id,
    required this.label,
    required this.icon,
    required this.tint,
    required this.subtitle,
  });
}

/// Visual 2-row category grid matching modern OLX design with high-curvature glass buttons
class OlxCategoryGrid extends ConsumerWidget {
  const OlxCategoryGrid({super.key});

  static const List<CategoryButtonItem> categories = [
    CategoryButtonItem(
      id: 3,
      label: 'Mobiles',
      icon: Icons.smartphone_rounded,
      tint: Color(0xFF93C5FD), // Cyan/Blue
      subtitle: 'Phones & Tabs',
    ),
    CategoryButtonItem(
      id: 1,
      label: 'Vehicles',
      icon: Icons.directions_car_filled_rounded,
      tint: Color(0xFFF9A8D4), // Pink
      subtitle: 'Cars & Bikes',
    ),
    CategoryButtonItem(
      id: 5,
      label: 'Property',
      icon: Icons.home_work_rounded,
      tint: Color(0xFF99F6E4), // Mint
      subtitle: 'Sale & Rent',
    ),
    CategoryButtonItem(
      id: 6,
      label: 'Electronics',
      icon: Icons.laptop_mac_rounded,
      tint: Color(0xFFD8B4FE), // Lavender
      subtitle: 'Gadgets & TV',
    ),
    CategoryButtonItem(
      id: 2,
      label: 'Bikes',
      icon: Icons.two_wheeler_rounded,
      tint: Color(0xFFFCD34D), // Amber
      subtitle: 'Motorcycles',
    ),
    CategoryButtonItem(
      id: 7,
      label: 'Services',
      icon: Icons.handyman_rounded,
      tint: Color(0xFF6EE7B7), // Emerald
      subtitle: 'Home & Expert',
    ),
    CategoryButtonItem(
      id: 8,
      label: 'Jobs',
      icon: Icons.work_rounded,
      tint: Color(0xFFFCA5A5), // Rose
      subtitle: 'Careers & Hiring',
    ),
    CategoryButtonItem(
      id: 9,
      label: 'Animals',
      icon: Icons.pets_rounded,
      tint: Color(0xFFFDE047), // Yellow
      subtitle: 'Pets & Livestock',
    ),
    CategoryButtonItem(
      id: 10,
      label: 'Furniture',
      icon: Icons.chair_rounded,
      tint: Color(0xFFA5B4FC), // Indigo
      subtitle: 'Decor & Living',
    ),
    CategoryButtonItem(
      id: 11,
      label: 'Fashion',
      icon: Icons.watch_rounded,
      tint: Color(0xFFFDA4AF), // Rose pink
      subtitle: 'Watches & Wear',
    ),
  ];

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 10, AppTheme.gutter, 8),
          child: Row(
            children: [
              Text(
                'ALL CATEGORIES',
                style: AppTheme.labelUppercase.copyWith(
                  letterSpacing: 1.1,
                  fontSize: 11,
                  color: AppTheme.onSurfaceVariant,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const Spacer(),
              Text(
                'See All (10)',
                style: AppTheme.caption.copyWith(
                  color: AppTheme.primaryLavender,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
        ),
        SizedBox(
          height: 172,
          child: GridView.builder(
            padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter),
            physics: const BouncingScrollPhysics(),
            scrollDirection: Axis.horizontal,
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 2,
              mainAxisSpacing: 10,
              crossAxisSpacing: 10,
              childAspectRatio: 0.88,
            ),
            itemCount: categories.length,
            itemBuilder: (context, index) {
              final cat = categories[index];
              return RepaintBoundary(
                child: Material(
                  color: Colors.transparent,
                  child: InkWell(
                    borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => CategoryListingsScreen(
                            categoryId: cat.id,
                            categoryName: cat.label,
                            icon: cat.icon,
                            accentColor: cat.tint,
                          ),
                        ),
                      );
                    },
                    child: Container(
                      decoration: BoxDecoration(
                        color: AppTheme.surfaceGlass,
                        borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                        border: Border.all(
                          color: cat.tint.withValues(alpha: 0.25),
                          width: 1,
                        ),
                      ),
                      padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 8),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Container(
                            width: 36,
                            height: 36,
                            decoration: BoxDecoration(
                              color: cat.tint.withValues(alpha: 0.15),
                              shape: BoxShape.circle,
                              border: Border.all(
                                color: cat.tint.withValues(alpha: 0.35),
                                width: 1,
                              ),
                            ),
                            child: Icon(cat.icon, color: cat.tint, size: 20),
                          ),
                          const SizedBox(height: 5),
                          Text(
                            cat.label,
                            style: AppTheme.bodySm.copyWith(
                              fontSize: 11,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.onSurface,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            textAlign: TextAlign.center,
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              );
            },
          ),
        ),
      ],
    );
  }
}
